<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log; // Ditambahkan untuk mencatat log lokal

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari Next.js
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'shoe_brand' => 'required|string',
            'shoe_color' => 'required|string',
        ]);

        // 2. Ambil data layanan untuk mendapatkan harga aslinya
        $service = Service::findOrFail($request->service_id);

        // 3. Simpan pesanan ke database
        $order = Order::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'shoe_brand' => $request->shoe_brand,
            'shoe_color' => $request->shoe_color,
            'phone_number' => $request->phone_number,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_price' => $service->price, // Mengunci harga saat transaksi
        ]);

        // 4. Kembalikan respons sukses ke frontend
        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan cuci sepatu berhasil dibuat!',
            'data' => $order
        ], 201);
    }

    // Fungsi untuk menampilkan semua pesanan (Untuk Dasbor Admin)
    public function index()
    {
        // Mengambil pesanan lengkap dengan data pelanggan dan layanan yang dipilih
        $orders = Order::with(['user', 'service'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data pesanan',
            'data' => $orders
        ]);
    }

    // Fungsi untuk mengubah status pengerjaan sepatu (Untuk Dasbor Admin)
    public function updateStatus(Request $request, $id)
    {
        // Validasi input status agar tidak diisi sembarangan
        $request->validate([
            'status' => 'required|in:pending,washing,ready_for_pickup,completed'
        ]);

        // Cari pesanan berdasarkan ID, lalu update statusnya
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        // ==========================================
        // SIMULASI NOTIFIKASI WHATSAPP KE LOG LOKAL
        // ==========================================
        if ($request->status === 'ready_for_pickup' && $order->phone_number) {
            
            $targetNumber = $order->phone_number;
            
            $pesan = "Halo! Kabar gembira dari *Sekashoes*. \n\n"
                   . "Sepatu *" . $order->shoe_brand . "* Anda sudah selesai dikerjakan dan siap untuk diambil. \n\n"
                   . "Terima kasih sudah mempercayakan perawatan sepatu Anda kepada kami!";

            try {
                // Mencatat simulasi pengiriman pesan ke file storage/logs/laravel.log
                Log::info("=== SIMULASI KIRIM WA OTOMATIS ===");
                Log::info("Nomor Tujuan : " . $targetNumber);
                Log::info("Isi Pesan    :\n" . $pesan);
                Log::info("=================================");
                
            } catch (\Exception $e) {
                Log::error('Gagal mencatat simulasi WA: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan berhasil diperbarui menjadi ' . $request->status,
            'data' => $order
        ]);
    }

    // Fungsi untuk mencari 1 pesanan spesifik (Untuk Halaman Cek Status)
    public function show($id)
    {
        // Cari pesanan berdasarkan ID, sertakan data layanannya
        $order = Order::with('service')->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|string|in:unpaid,paid',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status pembayaran berhasil diperbarui',
            'data' => $order
        ]);
    }

    public function destroy($id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'status' => 'error',
            'message' => 'Pesanan tidak ditemukan'
        ], 404);
    }

    $order->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Pesanan berhasil dihapus'
    ]);
}

}