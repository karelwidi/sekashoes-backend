<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // Hanya mengambil layanan yang status is_active nya true
        $services = Service::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil daftar layanan',
            'data' => $services
        ]);
    }
}
