<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;

class EmployeeController extends Controller
{
    public function index()
    {
        // Pastikan file view ini ada di resources/views/employees/pdm.blade.php
        return view('employees.pdm');
    }

    public function updatePdm(Request $request)
    {
        // Untuk mengetes apakah data berhasil dikirim
        return response()->json([
            'message' => 'Data diterima!',
            'data' => $request->all()
        ]);
    }
}