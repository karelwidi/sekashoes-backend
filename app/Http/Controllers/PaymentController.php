<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function createQris($id)
    {
        // 1. Cari data pesanan berdasarkan ID
        $order = Order::findOrFail($id);

        // Jika QRIS sudah pernah dibuat sebelumnya, kembalikan yang lama agar tidak ganda
        if ($order->snap_token) {
            return response()->json([
                'success' => true,
                'qr_string' => $order->snap_token
            ]);
        }

        // 2. Siapkan data untuk dikirim ke Xendit
        $secretKey = env('XENDIT_SECRET_KEY');
        
        // Kita panggil API Xendit menggunakan Http Client bawaan Laravel
        $response = Http::withBasicAuth($secretKey, '')
            ->post('https://api.xendit.co/qr_codes', [
                'external_id' => 'SEKA-' . $order->id . '-' . time(),
                'type' => 'DYNAMIC',
                'callback_url' => 'http://sekashoes-backend.test/api/xendit/callback', // Nanti kita buat untuk terima notif sukses
                'amount' => 25000 // Ganti dengan kolom harga Anda, misal: $order->total_price
            ]);

        $data = $response->json();

        // 3. Jika berhasil, simpan 'qr_string' ke database 
        // (Kita manfaatkan kolom snap_token yang sudah Anda buat sebelumnya)
        if (isset($data['qr_string'])) {
            $order->update([
                'snap_token' => $data['qr_string']
            ]);

            return response()->json([
                'success' => true,
                'qr_string' => $data['qr_string']
            ]);
        }

        // Jika gagal, tampilkan errornya
        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat QRIS',
            'detail' => $data
        ], 500);
    }

    public function webhook(Request $request)
    {
        // 1. Ambil semua data yang dikirimkan Xendit
        $data = $request->all();

        // 2. Pastikan ini adalah notifikasi dari pembayaran QRIS
        if (isset($data['event']) && $data['event'] === 'qr.payment') {
            
            // 3. Ambil external_id (contoh: SEKA-1-123456789)
            $externalId = $data['data']['qr_code']['external_id'];
            
            // 4. Pecah teksnya untuk mengambil ID pesanan (angka 1)
            $parts = explode('-', $externalId);
            $orderId = $parts[1] ?? null;

            if ($orderId) {
                // 5. Cari pesanannya di database dan ubah status jadi paid (lunas)
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid'
                    ]);
                }
            }
        }

        // 6. Wajib merespons 200 OK agar Xendit tahu sinyalnya sudah kita terima
        return response()->json(['message' => 'Notifikasi diterima'], 200);
    }

    public function checkStatus($id)
    {
        $order = Order::findOrFail($id);
        
        return response()->json([
            'payment_status' => $order->payment_status
        ]);
    }
}
