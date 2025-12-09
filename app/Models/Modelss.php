<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Modelss extends Model
{
    public function tampil($table)
    {
        return DB::table($table)->get();
    }

    public function checkData($table, $where)
    {
        return DB::table($table)->where($where)->first();
    }

    public function insertData($table, $data)
    {
        return DB::table($table)->insert($data);
    }

    public function getWhere($table, $where)
    {
        return DB::table($table)->where($where)->get();
    }

    public function updateData($table, $where, $data)
    {
        return DB::table($table)->where($where)->update($data);
    }
    
    public function deleteData($table, $where)
    {
        return DB::table($table)->where($where)->delete();
    }

    public function join($table1, $table2, $on)
    {
        return DB::table($table1)
            ->leftjoin($table2, $on[0], '=', $on[1])
            ->get();
    }

    public function join3($table1, $table2, $table3, $on, $on1, $column)
    {
        return DB::table($table1)
            ->leftjoin($table2, $on[0], '=', $on[1])
            ->leftjoin($table3, $on1[0], '=', $on1[1])
            ->select($column)
            ->get();
    }

    public function join5($table1, $table2, $table3, $table4, $table5, $on, $on1, $on2, $on3, $column, $where )
    {
        return DB::table($table1)
            ->join($table2, $on[0], '=', $on[1])
            ->join($table3, $on1[0],     '=', $on1[1])
            ->leftjoin($table4, $on2[0], '=', $on2[1])
            ->leftjoin($table5, $on3[0], '=', $on3[1])
            ->select($column)
            ->where($where)
            ->get();
    }

    public function join4($table1, $table2, $table3, $table4, $on, $on1, $on2, $where, $column)
    {
        return DB::table($table1)
            ->join($table2, $on[0], '=', $on[1])
            ->join($table3, $on1[0], '=', $on1[1])
            ->join($table4, $on2[0], '=', $on2[1])
            ->where($where)
            ->select($column)
            ->get();
    }
    public function join0($table1, $table2, $on) {
    return DB::table($table1)
        ->join($table2, $on[0], '=', $on[1])
        ->select("$table1.*", "$table2.nama_jabatan")
        ->get();
}
}
