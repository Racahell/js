<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Modelss;
use Spatie\SimpleExcel\SimpleExcelWriter;
use TCPDF;

class Kontrol extends Controller{

//----------------------L--O--G--I--N-----------------------
    public function showlogin(){
        if (session()->has('id')) {
            return redirect('/dashboard');
        }
        echo view('header');
        echo view('login');
        echo view('footer');
    }       
    public function aksilogin(Request $request){

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = DB::table('user')->where('nama_user', $username)->first();

        if (!$user) {
            return response()->json(['status' => 'error']);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json(['status' => 'error']);
        }

        $request->session()->put('id', $user->id_user);
        $request->session()->put('nama_user', $user->nama_user);
        $request->session()->put('level', $user->id_level);

        if ($user->id_level == 2) { 

            $request->session()->put('role', 'pembeli');
        } else {
            $karyawan = DB::table('karyawan')
            ->where('id_user', $user->id_user)
            ->first();

            if ($karyawan) {
                $jabatan = DB::table('jabatan')
                ->where('id_jabatan', $karyawan->id_jabatan)
                ->value('nama_jabatan');
                $request->session()->put('role', $jabatan);
            } else {
                $jabatan = DB::table('jabatan')
                ->where('id_jabatan', $user->id_level)
                ->value('nama_jabatan');
                $request->session()->put('role', $jabatan ?? 'karyawan');
            }
        }
        return response()->json([
            'status' => 'success',
            'redirect' => '/dashboard'
        ]);
    }
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    return redirect ('/login'); // atau redirect jika non-AJAX
}

//----------------------R--E--G--I--S--T--E--R-----------------------
public function registerpost(Request $request)
{

    $request->validate([
        'nama_user' => 'required|string|max:255|unique:user,nama_user',
        'password' => 'required|min:3',
        'alamat' => 'required|string',
        'notlp' => 'required|string',
        'email' => 'required|email',
    ]);

    $id_user = DB::table('user')->insertGetId([
        'nama_user' => $request->nama_user,
        'password' => Hash::make($request->password),
        'id_level' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('pembeli')->insert([
        'id_user' => $id_user,
        'nama_pembeli' => $request->nama_user,
        'alamat' => $request->alamat,
        'notlp' => $request->notlp,
        'email' => $request->email,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['status' => 'success', 'redirect' => '/login']);
}

public function showregister()
{
    if (session()->has('id')) {
        return redirect('/dashboard');
    }
    echo view('header');
    echo view('login');
    echo view('footer');
}


//----------------------D--A--S--H--B--O--A--R--D-----------------------
public function dashboard()
{
    $data = [];

    if (session('role') == 'pembeli') {
        $id_pembeli = DB::table('pembeli')->where('id_user', session('id'))->value('id_pembeli');

        $data['transaksiTerakhir'] = DB::table('transaksi')
        ->select(
            'transaksi.id_transaksi',
            'transaksi.total_harga',
            'transaksi.tanggal_transaksi',
            DB::raw('CASE 
                WHEN MAX(pengiriman.tanggal_menerima) IS NOT NULL THEN "Diterima"
                WHEN MAX(pengiriman.tanggal_kirim) IS NOT NULL THEN "Dikirim"
                ELSE "Menunggu Kirim"
                END as status_pengiriman')
        )
        ->join('detail', 'transaksi.id_transaksi', '=', 'detail.id_transaksi')
        ->leftJoin('pengiriman', 'detail.id_detail', '=', 'pengiriman.id_detail')
        ->where('transaksi.id_pembeli', $id_pembeli)
        ->groupBy('transaksi.id_transaksi', 'transaksi.total_harga', 'transaksi.tanggal_transaksi')
        ->orderBy('transaksi.tanggal_transaksi', 'desc')
        ->limit(5)
        ->get();
        
        $data['barangTersedia'] = DB::table('barang')
        ->where('stok', '>', 0)
        ->orderBy('nama_barang')
        ->get();
    }
    // Data untuk OWNER
    elseif (session('role') == 'owner') {
        $data['totalKaryawan'] = DB::table('karyawan')->count();
        $data['totalUser'] = DB::table('user')->count();
        $data['totalPendapatan'] = DB::table('transaksi')->sum('total_harga');
        $data['totalTransaksi'] = DB::table('transaksi')->count();
    }

    // Data untuk ADMIN
    elseif (session('role') == 'admin') {
        $data['totalBarang'] = DB::table('barang')->count();
        $data['totalStok'] = DB::table('barang')->sum('stok');
        $data['transaksiHariIni'] = DB::table('transaksi')
        ->whereDate('tanggal_transaksi', now()->toDateString())
        ->count();
        $data['pendapatanHariIni'] = DB::table('transaksi')
        ->whereDate('tanggal_transaksi', now()->toDateString())
        ->sum('total_harga');
    }

    // Data untuk STOKER
    elseif (session('role') == 'stoker') {
        $data['totalBarang'] = DB::table('barang')->count();
        $data['totalStok'] = DB::table('barang')->sum('stok');
        $data['returDiproses'] = DB::table('retur')->count();
    }

    // Data untuk KURIR
    elseif (session('role') == 'kurir') {
        $data['pengirimanHariIni'] = DB::table('pengiriman')
        ->whereDate('created_at', now()->toDateString())
        ->count();
        $data['pengirimanSelesai'] = DB::table('pengiriman')
        ->whereNotNull('tanggal_menerima')
        ->count();
        $data['pengirimanTertunda'] = DB::table('pengiriman')
        ->whereNull('tanggal_kirim')
        ->count();
    }

    echo view('header');
    echo view('menu');
    echo view('dashboard', $data);
    echo view('footer');
}


//----------------------R--I--W--A--Y--A--T---T--R--A--N--S--A--K--S--I-----------------------
public function riwayattransaksi()
{
    $data = [];

    // Data untuk PEMBELI
    if (session('role') == 'pembeli') {
        $id_pembeli = DB::table('pembeli')->where('id_user', session('id'))->value('id_pembeli');

        $data['transaksiTerakhir'] = DB::table('transaksi')
        ->select(
            'transaksi.id_transaksi',
            'transaksi.total_harga',
            'transaksi.tanggal_transaksi',
            DB::raw('CASE 
                WHEN MAX(pengiriman.tanggal_menerima) IS NOT NULL THEN "Diterima"
                WHEN MAX(pengiriman.tanggal_kirim) IS NOT NULL THEN "Dikirim"
                ELSE "Menunggu Kirim"
                END as status_pengiriman')
        )
        ->join('detail', 'transaksi.id_transaksi', '=', 'detail.id_transaksi')
        ->leftJoin('pengiriman', 'detail.id_detail', '=', 'pengiriman.id_detail')
        ->where('transaksi.id_pembeli', $id_pembeli)
        ->groupBy('transaksi.id_transaksi', 'transaksi.total_harga', 'transaksi.tanggal_transaksi')
        ->orderBy('transaksi.tanggal_transaksi', 'desc')
        ->limit(5)
        ->get();
        
        $data['barangTersedia'] = DB::table('barang')
        ->where('stok', '>', 0)
        ->orderBy('nama_barang')
        ->get();
    }
    echo view('header');
    echo view('menu');
    echo view('riwayattransaksi', $data);
    echo view('footer');
}

//----------------------U--S--E--R-----------------------
public function user(){
   if (session('id')>0) {
    $bom = new Modelss();
    $pam ['tableshow']= $bom->tampil('user');

    echo view('header');
    echo view('menu');
    echo view('login',$pam);
    echo view('footer'); 
}else{
    return redirect('/error');
}            
}
public function apiUserList() {
    if (!session('id')) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
    $users = DB::table('user')
    ->select('id_user', 'nama_user', 'id_level')
    ->orderBy('id_level', 'asc')
    ->get();
    return response()->json($users);
}

    // ✅ Fungsi Reset Password
public function resetpw($id_user) {
    if (!session('id')) { return response()->json(['status' => 'error', 'message' => 'Sesi berakhir.'], 401); }
    try {
        $user = DB::table('user')->where('id_user', $id_user)->first();
        if (!$user) {
           return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan.'], 404);
       }

       DB::table('user')->where('id_user', $id_user)->update(['password' => Hash::make('12345')]);

       return response()->json([
        'status' => 'success',
        'message' => 'Password user ' . $user->nama_user . ' berhasil direset menjadi 12345.'
    ]);
   } catch (\Exception $e) {
    return response()->json(['status' => 'error', 'message' => 'Gagal mereset password.'], 500);
}
}
//----------------------K--A--R--Y--A--W--A--N-----------------------

public function halkaryawan()
{
    if (session('id')>0) {

        $bom = new Modelss();
        $on = array('karyawan.id_jabatan', 'jabatan.id_jabatan');
        $pam ['karya']= $bom->join('karyawan', 'jabatan', $on);


        echo view('header');
        echo view('menu');
        echo view('login', $pam);
        echo view('footer');
    }else{
        return redirect('/error');
    } 
}

public function tambahkaryawan(Request $request)
{
    $request->validate([
        'nama_user' => 'required|string|max:255|unique:user,nama_user',
        'password' => 'required|min:3',
        'alamat' => 'required|string',
        'notlp' => 'required|string',
        'email' => 'required|email',
        'jabatan' => 'required|exists:jabatan,id_jabatan',
    ]);

    $id_user = DB::table('user')->insertGetId([
        'nama_user' => $request->nama_user,
        'password' => Hash::make($request->password),
        'id_level' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);


    DB::table('karyawan')->insert([
        'id_user' => $id_user,
        'nama_karyawan' => $request->nama_user,
        'alamat' => $request->alamat,
        'id_jabatan' => $request->jabatan,
        'notlp' => $request->notlp,
        'email' => $request->email,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil ditambahkan!']);
}

public function updateKaryawan(Request $request, $id)
{
    $request->validate([
        'nama_karyawan' => 'required|string|max:255',
        'alamat' => 'required|string',
        'id_jabatan' => 'required|exists:jabatan,id_jabatan',
        'notlp' => 'required|string',
        'email' => 'required|email|unique:karyawan,email,' . $id . ',id_karyawan',
    ]);

    $karyawan = DB::table('karyawan')->where('id_karyawan', $id)->first();
    if (!$karyawan) {
        return response()->json(['status' => 'error', 'message' => 'Karyawan tidak ditemukan!'], 404);
    }

    DB::table('karyawan')->where('id_karyawan', $id)->update([
        'nama_karyawan' => $request->nama_karyawan,
        'alamat' => $request->alamat,
        'id_jabatan' => $request->id_jabatan,
        'notlp' => $request->notlp,
        'email' => $request->email,
        'updated_at' => now()
    ]);

    return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil diperbarui!']);
}

public function hapusKaryawan($id_karyawan)
{
    // 1. Cari data karyawan dan pastikan ada
    $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
    
    if (!$karyawan) {
        return response()->json(['status' => 'error', 'message' => 'Karyawan tidak ditemukan!'], 404);
    }
    
    // Simpan id_user terkait
    $id_user_terkait = $karyawan->id_user;

    try {
        // 2. Hapus data dari tabel 'karyawan'
        DB::table('karyawan')->where('id_karyawan', $id_karyawan)->delete();

        // 3. Hapus data yang sesuai dari tabel 'user'
        DB::table('user')->where('id_user', $id_user_terkait)->delete();

        return response()->json(['status' => 'success', 'message' => 'Karyawan berhasil dihapus! Akun user juga telah dihapus.']);
        
    } catch (\Exception $e) {
        // Tangani jika terjadi error database
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()], 500);
    }
}
    // Mengembalikan data karyawan dalam format JSON (untuk AJAX)
public function apiKaryawan(Request $request)
{
    $perPage = $request->get('per_page', 10);
    $data = DB::table('karyawan')
    ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
    ->select(
        'karyawan.id_karyawan',
        'karyawan.nama_karyawan',
        'karyawan.alamat',
        'karyawan.notlp',
        'karyawan.email',
        'karyawan.id_jabatan',          // ✅ kirim ID-nya
        'jabatan.nama_jabatan as jabatan' // ✅ kirim nama-nya sebagai 'jabatan'
    )
    ->paginate($perPage);

    return response()->json([
        'data' => $data->items(),
        'current_page' => $data->currentPage(),
        'last_page' => $data->lastPage(),
        'per_page' => $data->perPage(),
        'total' => $data->total(),
    ]);
}

public function halkasir()
{
    if (session('id') <= 0 || session('role') != 'kasir') {
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }

    // Ambil id_karyawan dari user login
    $id_karyawan = DB::table('karyawan')
    ->where('id_user', session('id'))
    ->value('id_karyawan');

    // Ambil transaksi yang dilakukan kasir ini
    $transaksi = DB::table('transaksi')
    ->join('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'transaksi.id_transaksi',
        'transaksi.tanggal_transaksi',
        'transaksi.total_harga',
        'pembeli.nama_pembeli'
    )
    ->where('transaksi.id_karyawan', $id_karyawan)
    ->orderBy('transaksi.tanggal_transaksi', 'desc')
    ->get();

    echo view('header');
    echo view('menu');
    echo view('halkasir', ['transaksi' => $transaksi]);
    echo view('footer');
}


//----------------------B--A--R--A--N--G-----------------------
public function halbarang()
{
    if (session('id')>0) {

        $bom = new Modelss();
        $barang = $bom->tampil('barang');

        echo view('header');
        echo view('menu');
        echo view('halbarang', ['barang' => $barang]);
        echo view('footer');
    }else{
        return redirect('/error');
    }
}

public function createBarang()
{
    if (session('id')>0) {

        echo view('header');
        echo view('menu');
        echo view('halformbarang');
        echo view('footer');
    }else{
        return redirect('/error');
    }
}


public function storeBarang(Request $request)
{
    $bom = new Modelss();

    $data = [
        'kode_barang' => $request->kode_barang,
        'nama_barang' => $request->nama_barang,
        'jenis'       => $request->jenis,
        'harga'       => (int)$request->harga,
        'stok'        => (int)$request->stok,
        'tanggal'     => $request->tanggal,

    ];

    $bom->insertData('barang', $data);

    return redirect('/halbarang')->with('success', 'Barang berhasil ditambahkan!');
}


public function editBarang($id_barang)
{
    if (session('id')>0) {

        $bom = new Modelss();
        $barang = $bom->checkData('barang', ['id_barang' => $id_barang]);

        echo view('header');
        echo view('menu');
        echo view('halformbarang', ['barang' => $barang]);
        echo view('footer');
    }else{
        return redirect('/error');
    }
}


public function updateBarang(Request $request, $id_barang)
{
    $bom = new Modelss();

    $data = [
        'kode_barang' => $request->kode_barang,
        'nama_barang' => $request->nama_barang,
        'jenis'       => $request->jenis,
        'harga'       => $request->harga,
        'stok'        => $request->stok,
        'tanggal'     => $request->tanggal,

    ];
    $where = ['id_barang' => $id_barang];
    $bom->updateData('barang', $where, $data);

    return redirect('/halbarang')->with('success', 'Barang berhasil diupdate!');
}

public function hapusBarang($id_barang)
{
    $bom = new Modelss();
    $where = ['id_barang' => $id_barang];
    $bom->deleteData('barang', $where);
    return redirect('/halbarang');
}

//----------------------T--R--A--N--S--A--K--S--I-----------------------
   public function haltransaksi(Request $request)
    {
        if (session('id') <= 0) {
            return redirect('/error');
        }

        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        echo view('header');
        echo view('menu');
        echo view('haltransaksi', [
            'transaksi' => collect([]), 
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ]);
        echo view('footer');
    }

    // API: Mengambil list transaksi untuk tabel AJAX
    public function apiTransaksiList(Request $request)
    {
        if (session('id') <= 0) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        $query = DB::table('transaksi')
            ->join('detail', 'transaksi.id_transaksi', '=', 'detail.id_transaksi')
            ->leftJoin('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
            ->leftJoin('barang', 'detail.id_barang', '=', 'barang.id_barang') 
            ->select(
                'transaksi.id_transaksi',
                'transaksi.total_harga',
                'transaksi.tanggal_transaksi',
                'karyawan.nama_karyawan',
                'pembeli.nama_pembeli',
                'detail.id_detail', 
            );

        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereBetween('transaksi.tanggal_transaksi', [$tanggal_awal, $tanggal_akhir . ' 23:59:59']);
        } elseif ($tanggal_awal) {
            $query->where('transaksi.tanggal_transaksi', '>=', $tanggal_awal);
        } elseif ($tanggal_akhir) {
            $query->where('transaksi.tanggal_transaksi', '<=', $tanggal_akhir . ' 23:59:59');
        }

        $transaksiList = $query->get();

        $transaksi = $transaksiList->groupBy('id_transaksi')->map(function ($group) {
            $first = $group->first();
            $nama_pelaku = $first->nama_pembeli ?? $first->nama_karyawan ?? '—';

            return (object) [
                'id_transaksi' => $first->id_transaksi,
                'total_harga' => $first->total_harga,
                'tanggal_transaksi' => $first->tanggal_transaksi,
                'nama_pelaku' => $nama_pelaku,
                'jumlah_jenis_barang' => $group->count(),
            ];
        })->values();

        return response()->json(['data' => $transaksi]);
    }


public function apiDetailTransaksi($id)
{
    if (session('id') <= 0) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $rows = DB::table('detail')
        ->join('transaksi', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
        ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
        ->leftJoin('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
        ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
        ->select(
            'detail.id_detail',
            'barang.nama_barang',
            'detail.harga',
            'detail.jumlah',
            DB::raw('(detail.harga * detail.jumlah) AS subtotal'),
            DB::raw("
                CASE 
                    WHEN EXISTS(
                        SELECT 1 FROM retur 
                        WHERE retur.id_detail = detail.id_detail
                    ) 
                    THEN 1 ELSE 0 
                END AS retur
            "),
            'transaksi.tanggal_transaksi',
            'transaksi.total_harga',
            'pembeli.nama_pembeli',
            'karyawan.nama_karyawan'
        )
        ->where('detail.id_transaksi', $id)
        ->get();

    if ($rows->isEmpty()) {
        return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
    }

    $first = $rows->first();

    return response()->json([
        'info' => [
            'nama_pelaku'       => $first->nama_pembeli ?? $first->nama_karyawan ?? '—',
            'tanggal_transaksi' => $first->tanggal_transaksi,
            'total_harga'       => $first->total_harga
        ],
        'items' => $rows
    ]);
}


public function viewpembeli(Request $request)
{
    if (session('id') > 0) {
        $tanggalDipilih = $request->input('tanggal');

        if ($tanggalDipilih) {
            $barang = DB::table('barang')->where('tanggal', $tanggalDipilih)->get();
        } else {
            $barang = DB::table('barang')->get();
        }

        $tanggalList = DB::table('barang')
        ->select('tanggal')
        ->distinct()
        ->orderBy('tanggal', 'desc')
        ->get();

        echo view('header');
        echo view('menu');
        echo view('viewpembeli', compact('barang', 'tanggalList', 'tanggalDipilih'));
        echo view('footer');
    } else {
        return redirect('/login');
    }
}


public function detailtransaksi($id)
{
    $data = DB::table('detail')
        ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
        ->join('transaksi', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
        ->join('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
        ->where('detail.id_transaksi', $id)
        ->select(
            'barang.nama_barang',
            'detail.harga as harga_satuan',
            'detail.jumlah as qty',
            'detail.subtotal'
        )
        ->get();

    $info = DB::table('transaksi')
        ->join('pembeli','transaksi.id_pembeli','=','pembeli.id_pembeli')
        ->where('transaksi.id_transaksi', $id)
        ->select(
            'pembeli.nama_pembeli as nama_pelaku',
            'transaksi.tanggal_transaksi',
            'transaksi.total_harga'
        )
        ->first();

    return response()->json([
        'info' => $info,
        'items' => $data
    ]);
}

public function detailpembeli($id)
{
    if (session('id') <= 0) return redirect('/login');

    $bom = new Modelss();

    $on = ['detail.id_barang', 'barang.id_barang'];
    $on1 = ['detail.id_transaksi', 'transaksi.id_transaksi'];
    $on2 = ['transaksi.id_karyawan', 'karyawan.id_karyawan'];
    $on3 = ['transaksi.id_pembeli', 'pembeli.id_pembeli'];
    $where = ['transaksi.id_transaksi' => $id];

    $detail = $bom->join5(
        'detail', 'barang', 'transaksi', 'karyawan', 'pembeli',
        $on, $on1, $on2, $on3,
        ['transaksi.*', 'detail.*', 'barang.nama_barang', 'pembeli.nama_pembeli as nama_pelaku'],
        $where
    );

    // ambil semua id_detail dari hasil
    $allDetailIds = collect($detail)->pluck('id_detail')->toArray();

    // cek retur sekali (efisien)
    $existingRetur = [];
    if (!empty($allDetailIds)) {
        $existingRetur = DB::table('retur')->whereIn('id_detail', $allDetailIds)->pluck('id_detail')->toArray();
    }

    echo view('header');
    echo view('menu');
    echo view('detailpembeli', [
        'detail' => collect($detail),
        'existingRetur' => $existingRetur
    ]);
    echo view('footer');
}

//----------------------K--E--R--A--N--J--A--N--G-----------------------
public function tambahKeranjang(Request $request)
{
    $request->validate([
        'id_barang' => 'required|exists:barang,id_barang',
        'jumlah' => 'required|integer|min:1',
    ]);

    $bom = new Modelss();
    $where = array('id_barang'=> $request->id_barang);
    $barang = $bom->checkData('barang', $where);

    $keranjang = session('keranjang', []);

    $index = null;
    foreach ($keranjang as $key => $item) {
        if ($item['id_barang'] == $request->id_barang) {
            $index = $key;
            break; //Cek apakah barang sudah ada di keranjang
        }
    }

    if ($index !== null) {
        $keranjang[$index]['jumlah'] += $request->jumlah;
    } else {
        $keranjang[] = [
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'harga' => $barang->harga,
            'jumlah' => $request->jumlah, //Tambahkan barang (atau update jumlah jika sudah ada)
        ];
    }

    session(['keranjang' => $keranjang]);
    return back();
}

public function lihatKeranjang()
{
    if (!session('id') || session('role') != 'pembeli') {
        return redirect('/login');
    }
    $keranjang = session('keranjang', []);

    // Ambil kasir aktif (hanya ID-nya)
    $kasir = DB::table('karyawan')
    ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
    ->where('jabatan.nama_jabatan', 'kasir')
    ->select('karyawan.id_karyawan')
    ->first();

    $id_kasir = $kasir ? $kasir->id_karyawan : null;

    echo view('header');
    echo view('menu');
    echo view('checkout', [
        'keranjang' => $keranjang, 
        'id_kasir' => $id_kasir,
    ]);
    echo view('footer');
}
public function hapusKeranjang($id)
{
    $keranjang = session('keranjang', []);
    unset($keranjang[$id]);
    $keranjang = array_values($keranjang);
    session(['keranjang' => $keranjang]);

    return redirect()->route('/keranjang');
}

public function prosesTransaksi(Request $request)
{
    $keranjang = session('keranjang', []);

    $request->validate([
        'id_karyawan' => 'required|exists:karyawan,id_karyawan',
    ]);

    $id_transaksi = DB::table('transaksi')->insertGetId([
        'id_karyawan' => $request->id_karyawan,
        'total_harga' => 0,
        'tanggal_transaksi' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $total = 0;
    foreach ($keranjang as $item) {
        $subtotal = $item['harga'] * $item['jumlah'];
        $total += $subtotal;

        DB::table('detail')->insert([
            'id_transaksi' => $id_transaksi,
            'id_barang' => $item['id_barang'],
            'harga' => $item['harga'],
            'jumlah' => $item['jumlah'],
            'tanggal' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    $bom = new Modelss();
    $where = array('id_transaksi'=> $id_transaksi);
    $transaksi['transaksi'] = $bom->updateData('transaksi', $where, [
        'total_harga' => $total,
        'updated_at' => now()
    ]);

    session()->forget('keranjang');

    return redirect()->route('haltransaksi');
}

public function prosesCheckout(Request $request)
{
    $keranjang = session('keranjang', []);

    $request->validate([
        'id_karyawan' => 'required|exists:karyawan,id_karyawan',
    ]);

    $id_pembeli = DB::table('pembeli')
    ->where('id_user', session('id'))
    ->value('id_pembeli');

    // Buat transaksi
    $id_transaksi = DB::table('transaksi')->insertGetId([
        'id_karyawan' => $request->id_karyawan,
        'id_pembeli' => $id_pembeli,
        'total_harga' => 0,
        'tanggal_transaksi' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $total = 0;
    foreach ($keranjang as $item) {
        $subtotal = $item['harga'] * $item['jumlah'];
        $total += $subtotal;

        // Simpan detail transaksi
        $id_detail = DB::table('detail')->insertGetId([
            'id_transaksi' => $id_transaksi,
            'id_barang' => $item['id_barang'],
            'harga' => $item['harga'],
            'jumlah' => $item['jumlah'],
            'tanggal' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ✅ OTOMATIS BUAT PENGIRIMAN
        DB::table('pengiriman')->insert([
            'id_detail' => $id_detail,
            'tanggal_kirim' => null,
            'tanggal_menerima' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kurangi stok
        DB::table('barang')
        ->where('id_barang', $item['id_barang'])
        ->decrement('stok', $item['jumlah']);
    }

    // Update total transaksi
    DB::table('transaksi')
    ->where('id_transaksi', $id_transaksi)
    ->update([
        'total_harga' => $total,
        'updated_at' => now()
    ]);

    session()->forget('keranjang');
    return redirect('/pembayaran/' . $id_transaksi);
}

public function halamanPembayaran($id_transaksi)
{
    // Hanya user yang SUDAH LOGIN dan ROLE PEMBELI yang boleh akses
    if (!session('id') || session('role') != 'pembeli') {
        return redirect('/login');
    }

    $where = ['transaksi.id_transaksi' => $id_transaksi];
    $transaksi = DB::table('transaksi')
    ->join('detail', 'transaksi.id_transaksi', '=', 'detail.id_transaksi')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->join('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
    ->select(
        'transaksi.*',
        'detail.jumlah',
        'detail.harga as harga_satuan',
        'barang.nama_barang',
        'karyawan.nama_karyawan as kasir'
    )
    ->where($where)
    ->get();

    $transaksiUtama = $transaksi->first();
    if (!$transaksiUtama) {
        return redirect('/keranjang')->with('error', 'Data transaksi tidak ditemukan!');
    }

    echo view('header');
    echo view('menu');
    echo view('pembayaran', [
        'transaksi' => $transaksi,
        'transaksiUtama' => $transaksiUtama
    ]);
    echo view('footer');
}

//----------------------H--A--P--U--S---T--R--A--N--S--A--K--S--I-----------------------
public function hapustransaksi($id_transaksi)
{
    $bom = new Modelss();
    $where = ['id_transaksi' => $id_transaksi];
    $bom->deleteData('transaksi', $where);
    return redirect('/transaksi');
}

//----------------------L--A--P--O--R--A--N-----------------------
public function laporan()
{
    $transaksi = DB::table('transaksi')
    ->select('id_transaksi', 'total_harga', 'tanggal_transaksi')
    ->orderBy('tanggal_transaksi', 'desc')
    ->get();

    $totalPendapatan = $transaksi->sum('total_harga');

    $pendapatanPerTanggal = [];
    foreach ($transaksi as $t) {
        $tgl = $t->tanggal_transaksi;
        if (!isset($pendapatanPerTanggal[$tgl])) {
            $pendapatanPerTanggal[$tgl] = 0;
        }
        $pendapatanPerTanggal[$tgl] += $t->total_harga;
    }

    echo view('header');
    echo view('menu');
    echo view('laporan', compact('transaksi', 'totalPendapatan', 'pendapatanPerTanggal'));
    echo view('footer');
}

//----------------------C--E--T--A--K---D--E--T--A--I--L-----------------------
public function cetakDetail($id)
{
    if (session('id') <= 0) {
        return redirect('/login');
    }

    $detail = DB::table('transaksi')
    ->join('detail', 'transaksi.id_transaksi', '=', 'detail.id_transaksi')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->leftJoin('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
    ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'transaksi.*',
        'detail.*',
        'barang.nama_barang',
        DB::raw("COALESCE(pembeli.nama_pembeli, karyawan.nama_karyawan, '—') as nama_pelaku")
    )
    ->where('transaksi.id_transaksi', $id)
    ->get();

    if ($detail->isEmpty()) {
        abort(404, 'Transaksi tidak ditemukan');
    }

    return view('printtransaksi', ['detail' => $detail]);
}

//---------------------------P--E--N--G--I--R--I--M--A--N---------------------------
    // Tampilkan daftar pengiriman
public function halpengiriman()
{
    if (session('id') <= 0) return redirect('/error');

    $pengiriman = DB::table('pengiriman')
    ->join('detail', 'pengiriman.id_detail', '=', 'detail.id_detail')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->join('transaksi', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
    ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'pengiriman.*',
        'barang.nama_barang',
        'pembeli.nama_pembeli as nama_pelanggan',
        'transaksi.id_transaksi'
    )
    ->orderBy('pengiriman.created_at', 'desc')
    ->get();

    echo view('header');
    echo view('menu');
    echo view('halpengiriman', ['pengiriman' => $pengiriman]);
    echo view('footer');
}

    // Form tambah pengiriman
public function createPengiriman($id_detail)
{
    if (session('id') <= 0) return redirect('/error');

    $detail = DB::table('detail')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->join('transaksi', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
    ->select('detail.*', 'barang.nama_barang', 'transaksi.id_transaksi')
    ->where('detail.id_detail', $id_detail)
    ->first();

    if (!$detail) return redirect('/pengiriman')->with('error', 'Detail transaksi tidak ditemukan!');

    echo view('header');
    echo view('menu');
    echo view('formpengiriman', compact('detail'));
    echo view('footer');
}

    // Simpan pengiriman
public function storePengiriman(Request $request)
{
    $request->validate([
        'id_detail' => 'required|exists:detail,id_detail',
    ]);

    $exists = DB::table('pengiriman')->where('id_detail', $request->id_detail)->first();
    if ($exists) {
        return back()->with('error', 'Pengiriman untuk barang ini sudah ada!');
    }

    DB::table('pengiriman')->insert([
        'id_detail' => $request->id_detail,
        'tanggal_kirim' => null,
        'tanggal_menerima' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/pengiriman')->with('success', 'Pengiriman berhasil dibuat!');
}

//---------------------------R--E--T--U--R---------------------------

    // Tampilkan daftar retur
public function halretur()
{
    if (session('id') <= 0) return redirect('/error');

    $retur = DB::table('retur')
    ->join('detail', 'retur.id_detail', '=', 'detail.id_detail')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->join('transaksi', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
    ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'retur.*',
        'barang.nama_barang',
        'pembeli.nama_pembeli as nama_pelanggan',
        'transaksi.id_transaksi'
    )
    ->orderBy('retur.created_at', 'desc')
    ->get();

    echo view('header');
    echo view('menu');
    echo view('halretur', ['retur' => $retur]);
    echo view('footer');
}

    // Form ajukan retur
public function createRetur($id_detail)
{
    if (session('id') <= 0 || session('level') != 2) return redirect('/login');

    $detail = DB::table('detail')
    ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
    ->where('detail.id_detail', $id_detail)
    ->first();

    if (!$detail) {
        return redirect('/dashboard')->with('error', 'Barang tidak ditemukan!');
    }

    echo view('header');
    echo view('menu');
    echo view('formretur', compact('detail'));
    echo view('footer');
}

    // Simpan retur
public function storeRetur(Request $request)
{
    $request->validate([
        'id_detail' => 'required|exists:detail,id_detail',
        'alasan' => 'required|string'
    ]);

    DB::table('retur')->insert([
        'id_detail' => $request->id_detail,
        'alasan' => $request->alasan,
        'tanggal_retur' => now()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $id_transaksi = DB::table('detail')
    ->where('id_detail', $request->id_detail)
    ->value('id_transaksi');

    return redirect('/transaksi/detailp/' . $id_transaksi)
    ->with('success', 'Pengajuan retur berhasil dikirim!');
}

    // Hapus retur
public function hapusRetur($id_retur)
{
    DB::table('retur')->where('id_retur', $id_retur)->delete();
    return redirect('/retur')->with('success', 'Data retur dihapus!');
}

    // Konfirmasi retur
public function konfirmasiRetur(Request $request, $id_retur)
{
    if (!in_array(session('role'), ['stoker', 'admin', 'owner'])) {
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }

    $status = $request->query('status');
    if (!in_array($status, ['disetujui', 'ditolak'])) {
        return back()->with('error', 'Status tidak valid!');
    }

    $retur = DB::table('retur')->where('id_retur', $id_retur)->first();
    if (!$retur) {
        return back()->with('error', 'Data retur tidak ditemukan!');
    }

    if ($status === 'disetujui') {
        $detail = DB::table('detail')->where('id_detail', $retur->id_detail)->first();
        if ($detail) {
            DB::table('barang')
            ->where('id_barang', $detail->id_barang)
            ->increment('stok', $detail->jumlah);

            $subtotal = $detail->harga * $detail->jumlah;
            DB::table('transaksi')
            ->where('id_transaksi', $detail->id_transaksi)
            ->decrement('total_harga', $subtotal);
        }
    }

    DB::table('retur')
    ->where('id_retur', $id_retur)
    ->update([
        'status' => $status,
        'updated_at' => now()
    ]);

    return back()->with('success', 'Status retur berhasil diperbarui!');
}

//---------------------------L--A--P--O--R--A--N--_--O--W--N--E--R---------------------------

public function laporanOwner()
{
    if (session('level') != 1) {
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }

    $totalKaryawan = DB::table('karyawan')->count();
    $totalUser = DB::table('user')->count();
    $pendapatan = DB::table('transaksi')->sum('total_harga');

    $transaksi = DB::table('transaksi')
    ->select('tanggal_transaksi', 'total_harga')
    ->get();

    $laporanBulanan = [];
    foreach ($transaksi as $t) {
        $bulan = date('F', strtotime($t->tanggal_transaksi));
        if (!isset($laporanBulanan[$bulan])) {
            $laporanBulanan[$bulan] = 0;
        }
        $laporanBulanan[$bulan] += $t->total_harga;
    }

    $hasilBulanan = [];
    foreach ($laporanBulanan as $bulan => $jumlah) {
        $hasilBulanan[] = [
            'bulan' => $bulan,
            'pendapatan' => $jumlah
        ];
    }

    echo view('header');
    echo view('menu');
    echo view('laporan_owner', [
        'totalKaryawan' => $totalKaryawan,
        'totalUser' => $totalUser,
        'pendapatan' => $pendapatan,
        'laporanBulanan' => $hasilBulanan
    ]);
    echo view('footer');
}
//---------------------------K--U--R--I--R---------------------------

public function kirimPengiriman($id_pengiriman)
{
    if (session('role') != 'kurir') {
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }

    DB::table('pengiriman')
    ->where('id_pengiriman', $id_pengiriman)
    ->update([
        'tanggal_kirim' => now()->toDateString(),
        'updated_at' => now()
    ]);

    return back()->with('success', 'Barang berhasil dikirim!');
}

public function terimaPengiriman($id_pengiriman)
{
    if (session('role') != 'kurir') {
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }

    DB::table('pengiriman')
    ->where('id_pengiriman', $id_pengiriman)
    ->update([
        'tanggal_menerima' => now()->toDateString(),
        'updated_at' => now()
    ]);

    return back()->with('success', 'Pengiriman berhasil dikonfirmasi!');
}

public function hapusPengiriman($id_pengiriman)
{
    DB::table('pengiriman')->where('id_pengiriman', $id_pengiriman)->delete();
    return redirect('/pengiriman')->with('success', 'Data pengiriman dihapus!');
}

//----------------------E--X--P--O--R--T---P--D--F---&---E--X--C--E--L-----------------------

// Export PDF menggunakan TCPDF
public function exportPdfTransaksi(Request $request)
{
    if (!session('id')) {
        return redirect('/login');
    }

    $tanggal_awal = $request->input('tanggal_awal');
    $tanggal_akhir = $request->input('tanggal_akhir');

    $query = DB::table('transaksi')
    ->leftJoin('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
    ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'transaksi.id_transaksi',
        'transaksi.total_harga',
        'transaksi.tanggal_transaksi',
        'karyawan.nama_karyawan',
        'pembeli.nama_pembeli'
    );

    if ($tanggal_awal && $tanggal_akhir) {
        $query->whereBetween('transaksi.tanggal_transaksi', [$tanggal_awal, $tanggal_akhir]);
    } elseif ($tanggal_awal) {
        $query->where('transaksi.tanggal_transaksi', '>=', $tanggal_awal);
    } elseif ($tanggal_akhir) {
        $query->where('transaksi.tanggal_transaksi', '<=', $tanggal_akhir);
    }

    $transaksiList = $query->get()->map(function ($t) {
        $t->nama_pelaku = $t->nama_pembeli ?? $t->nama_karyawan ?? '—';
        $t->jumlah_jenis_barang = DB::table('detail')->where('id_transaksi', $t->id_transaksi)->count();
        return $t;
    });

    // === Mulai buat PDF dengan TCPDF ===
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Sistem');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Daftar Transaksi');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // Judul
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'DAFTAR TRANSAKSI', 0, 1, 'C');
    $pdf->Ln(5);

    // Rentang tanggal (jika ada)
    if ($tanggal_awal || $tanggal_akhir) {
        $pdf->SetFont('times', '', 11);
        $label = 'Semua waktu';
        if ($tanggal_awal && $tanggal_akhir) {
            $label = 'Dari ' . \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') .
            ' s.d. ' . \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y');
        } elseif ($tanggal_awal) {
            $label = 'Sejak ' . \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y');
        } elseif ($tanggal_akhir) {
            $label = 'Sampai ' . \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y');
        }
        $pdf->Cell(0, 6, $label, 0, 1, 'C');
        $pdf->Ln(5);
    }

    // Header tabel
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(7, 7, 'No', 1, 0, 'C');
    $pdf->Cell(10, 7, 'ID', 1, 0, 'C');
    $pdf->Cell(30, 7, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Nama Pelaku', 1, 0, 'C');
    $pdf->Cell(30, 7, 'Jenis Barang', 1, 0, 'C');
    $pdf->Cell(35, 7, 'Total (Rp)', 1, 1, 'C');

    // Isi tabel
    $pdf->SetFont('times', '', 10);
    if ($transaksiList->isEmpty()) {
        $pdf->Cell(195, 7, 'Tidak ada data transaksi', 1, 1, 'C');
    } else {
        foreach ($transaksiList as $index => $t) {
            $pdf->Cell(7, 7, $index + 1, 1);
            $pdf->Cell(10, 7, $t->id_transaksi, 1);
            $pdf->Cell(30, 7, \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d-m-Y'), 1);
            $pdf->Cell(50, 7, $t->nama_pelaku, 1);
            $pdf->Cell(30, 7, $t->jumlah_jenis_barang, 1, 0, 'C');
            $pdf->Cell(35, 7, number_format($t->total_harga, 0, ',', '.'), 1, 1, 'R');
        }
    }

    // Output PDF
    $filename = 'transaksi-' . now()->format('Y-m-d') . '.pdf';
    return response($pdf->Output($filename, 'S'))
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}
// Export Excel menggunakan Maatwebsite/Excel
public function exportExcelTransaksi(Request $request)
{
    if (session('id') <= 0) {
        return redirect('/login');
    }

    $tanggal_awal = $request->input('tanggal_awal');
    $tanggal_akhir = $request->input('tanggal_akhir');

    $query = DB::table('transaksi')
    ->leftJoin('karyawan', 'transaksi.id_karyawan', '=', 'karyawan.id_karyawan')
    ->leftJoin('pembeli', 'transaksi.id_pembeli', '=', 'pembeli.id_pembeli')
    ->select(
        'transaksi.id_transaksi',
        'transaksi.total_harga',
        'transaksi.tanggal_transaksi',
        'karyawan.nama_karyawan',
        'pembeli.nama_pembeli'
    );

    if ($tanggal_awal && $tanggal_akhir) {
        $query->whereBetween('transaksi.tanggal_transaksi', [$tanggal_awal, $tanggal_akhir]);
    } elseif ($tanggal_awal) {
        $query->where('transaksi.tanggal_transaksi', '>=', $tanggal_awal);
    } elseif ($tanggal_akhir) {
        $query->where('transaksi.tanggal_transaksi', '<=', $tanggal_akhir);
    }

    $transaksiList = $query->get()->map(function ($t) {
        $t->nama_pelaku = $t->nama_pembeli ?? $t->nama_karyawan ?? '—';
        $t->jumlah_jenis_barang = DB::table('detail')->where('id_transaksi', $t->id_transaksi)->count();
        return $t;
    });

    // Gunakan SimpleExcelWriter dari Spatie
    $filename = 'daftar-transaksi-' . now()->format('Y-m-d') . '.xlsx';

    $writer = SimpleExcelWriter::create(storage_path("app/public/{$filename}"))
    ->addRows($transaksiList->map(function ($item) {
        return [
            'ID Transaksi' => $item->id_transaksi,
            'Tanggal' => \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y'),
            'Pelaku' => $item->nama_pelaku,
            'Total Harga (Rp)' => $item->total_harga,
            'Jumlah Jenis Barang' => $item->jumlah_jenis_barang,
        ];
    })->toArray());

    // Download file
    return response()->download(storage_path("app/public/{$filename}"))->deleteFileAfterSend(true);
}

public function exportPdfKeuangan(Request $request)
{
    if (!session('id')) {
        return redirect('/login');
    }

    $tanggal_awal = $request->input('tanggal_awal');
    $tanggal_akhir = $request->input('tanggal_akhir');

    $query = DB::table('transaksi')->select('id_transaksi', 'total_harga', 'tanggal_transaksi');

    if ($tanggal_awal && $tanggal_akhir) {
        $query->whereBetween('tanggal_transaksi', [$tanggal_awal, $tanggal_akhir]);
    } elseif ($tanggal_awal) {
        $query->where('tanggal_transaksi', '>=', $tanggal_awal);
    } elseif ($tanggal_akhir) {
        $query->where('tanggal_transaksi', '<=', $tanggal_akhir);
    }

    $transaksi = $query->get();
    $totalPendapatan = $transaksi->sum('total_harga');

    $pendapatanPerTanggal = [];
    foreach ($transaksi as $t) {
        $tgl = $t->tanggal_transaksi;
        $pendapatanPerTanggal[$tgl] = ($pendapatanPerTanggal[$tgl] ?? 0) + $t->total_harga;
    }

    // === Buat PDF ===
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Sistem Alat Musik');
    $pdf->SetAuthor('Owner');
    $pdf->SetTitle('Laporan Keuangan Transaksi');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // === HEADER ===
    $pdf->SetFont('times', 'B', 18);
    $pdf->Cell(0, 10, 'LAPORAN KEUANGAN TRANSAKSI', 0, 1, 'C');
    $pdf->SetFont('times', '', 11);
    $pdf->Cell(0, 6, 'Periode: ' . 
        ($tanggal_awal ? \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') : 'Semua waktu') . 
        ' s.d. ' . 
        ($tanggal_akhir ? \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') : 'Sekarang'), 
        0, 1, 'C');
    $pdf->Cell(0, 6, 'Dicetak pada: ' . now()->locale('id')->translatedFormat('d F Y'), 0, 1, 'C');
    $pdf->Ln(8);

    // === RINGKASAN ===
    $pdf->SetFont('times', 'B', 13);
    $pdf->Cell(0, 8, 'Ringkasan Pendapatan', 0, 1);
    $pdf->SetFont('times', '', 11);
    $pdf->Cell(0, 7, 'Total Pendapatan: Rp ' . number_format($totalPendapatan, 0, ',', '.'), 0, 1);
    $pdf->Ln(5);

    // === TABEL PENDAPATAN PER TANGGAL ===
    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 7, 'Pendapatan per Tanggal', 0, 1);
    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(80, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(80, 8, 'Pendapatan (Rp)', 1, 1, 'C');

    $pdf->SetFont('times', '', 10);
    if (empty($pendapatanPerTanggal)) {
        $pdf->Cell(160, 8, 'Tidak ada data', 1, 1, 'C');
    } else {
        foreach ($pendapatanPerTanggal as $tgl => $jumlah) {
            $pdf->Cell(80, 8, \Carbon\Carbon::parse($tgl)->format('d-m-Y'), 1);
            $pdf->Cell(80, 8, number_format($jumlah, 0, ',', '.'), 1, 1, 'R');
        }
    }
    $pdf->Ln(8);

    // === TABEL DAFTAR TRANSAKSI ===
    $pdf->SetFont('times', 'B', 12);
    $pdf->Cell(0, 7, 'Daftar Transaksi', 0, 1);
    $pdf->Ln(2);

    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(45, 8, 'ID Transaksi', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(45, 8, 'Total (Rp)', 1, 1, 'C');

    $pdf->SetFont('times', '', 10);
    if ($transaksi->isEmpty()) {
        $pdf->Cell(155, 8, 'Tidak ada transaksi ditemukan', 1, 1, 'C');
    } else {
        foreach ($transaksi as $index => $t) {
            $pdf->Cell(15, 8, $index + 1, 1, 0, 'C');
            $pdf->Cell(45, 8, $t->id_transaksi, 1, 0, 'C');
            $pdf->Cell(50, 8, \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d-m-Y'), 1, 0, 'C');
            $pdf->Cell(45, 8, number_format($t->total_harga, 0, ',', '.'), 1, 1, 'R');
        }
    }

    $filename = 'laporan-keuangan-' . now()->format('Y-m-d') . '.pdf';
    return response($pdf->Output($filename, 'S'))
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

public function exportExcelKeuangan(Request $request){
    if (!session('id')) {
        return redirect('/login');
    }

    $tanggal_awal = $request->input('tanggal_awal');
    $tanggal_akhir = $request->input('tanggal_akhir');

    $query = DB::table('transaksi')->select('id_transaksi', 'total_harga', 'tanggal_transaksi');

    if ($tanggal_awal && $tanggal_akhir) {
        $query->whereBetween('tanggal_transaksi', [$tanggal_awal, $tanggal_akhir]);
    } elseif ($tanggal_awal) {
        $query->where('tanggal_transaksi', '>=', $tanggal_awal);
    } elseif ($tanggal_akhir) {
        $query->where('tanggal_transaksi', '<=', $tanggal_akhir);
    }

    $transaksi = $query->get();
    $totalPendapatan = $transaksi->sum('total_harga');

    $pendapatanPerTanggal = [];
    foreach ($transaksi as $t) {
        $tgl = $t->tanggal_transaksi;
        $pendapatanPerTanggal[$tgl] = ($pendapatanPerTanggal[$tgl] ?? 0) + $t->total_harga;
    }

    $filename = 'laporan-keuangan-' . now()->format('Y-m-d') . '.xlsx';
    $filePath = storage_path("app/public/{$filename}");
    $writer = SimpleExcelWriter::create($filePath);

    // === HEADER ===
    $writer->addRow(['LAPORAN KEUANGAN TRANSAKSI']);
    $writer->addRow(['Periode:', 
        ($tanggal_awal ? \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') : 'Semua waktu') . 
        ' s.d. ' . 
        ($tanggal_akhir ? \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') : 'Sekarang')
    ]);
    $writer->addRow(['Dicetak pada:', now()->locale('id')->translatedFormat('d F Y')]);
    $writer->addRow([]);
    $writer->addRow(['Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.')]);
    $writer->addRow([]);
    
    // === PENDAPATAN PER TANGGAL ===
    $writer->addRow(['PENDAPATAN PER TANGGAL']);
    $writer->addRow(['Tanggal', 'Pendapatan (Rp)']);
    foreach ($pendapatanPerTanggal as $tgl => $jumlah) {
        $writer->addRow([
            \Carbon\Carbon::parse($tgl)->format('d-m-Y'),
            number_format($jumlah, 0, ',', '.')
        ]);
    }
    $writer->addRow([]);
    
    // === DAFTAR TRANSAKSI ===
    $writer->addRow(['DAFTAR TRANSAKSI']);
    $writer->addRow(['No', 'ID Transaksi', 'Tanggal', 'Total (Rp)']);
    foreach ($transaksi as $index => $t) {
        $writer->addRow([
            $index + 1,
            $t->id_transaksi,
            \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d-m-Y'),
            number_format($t->total_harga, 0, ',', '.')
        ]);
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}

}