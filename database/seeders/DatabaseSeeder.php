<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        //admin
        User::factory()->create([
            'name' => 'Admin Sekashoes',
            'email' => 'admin@sekashoes.com',
            'password' => Hash::make('password123'), 
            'phone_number' => '081234567890',
            'role' => 'admin',
        ]);

        //customer
        User::factory()->create([
            'name' => 'Customer Testing',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'phone_number' => '089876543210',
            'role' => 'customer',
            ]);

        //daftar katalog
        $services = [
            [
                'name' => 'Deep Clean',
                'description' => 'Pembersihan detail dan menyeluruh untuk semua bagian luar dan dalam sepatu.',
                'price' => 50000,
                'is_active' => true,
            ],
            [
                'name' => 'Fast Clean',
                'description' => 'Pembersihan instan untuk bagian luar (upper & midsole), cocok untuk sepatu yang dipakai harian.',
                'price' => 30000,
                'is_active' => true,
            ],
            [
                'name' => 'Unyellowing',
                'description' => 'Perawatan khusus untuk menghilangkan noda kuning membandel pada sol sepatu karet.',
                'price' => 45000,
                'is_active' => true,
            ],
            [
                'name' => 'Repaint / Recolor',
                'description' => 'Pewarnaan ulang atau ubah warna sepatu agar terlihat seperti baru kembali.',
                'price' => 150000,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
