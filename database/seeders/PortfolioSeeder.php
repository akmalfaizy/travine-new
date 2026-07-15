<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Portfolio::create([
            'title' => 'Desa Wisata Nusantara',
            'category' => 'Pengembangan Ekosistem',
            'description' => 'Pengembangan ekosistem wisata desa terintegrasi di 5 provinsi.',
            'content' => 'Proyek ini berfokus pada digitalisasi desa wisata dan pemberdayaan masyarakat lokal melalui pelatihan manajemen pariwisata berkelanjutan.',
            'image' => 'portfolio-1.webp',
        ]);
        \App\Models\Portfolio::create([
            'title' => 'Bali Cultural Experience',
            'category' => 'Jasa Wisata',
            'description' => 'Paket wisata budaya premium dengan pengalaman autentik.',
            'content' => 'Pengalaman mendalam ke jantung budaya Bali, termasuk workshop tari tradisional, upacara adat, dan kuliner khas lokal yang jarang diketahui turis.',
            'image' => 'portfolio-2.webp',
        ]);
        \App\Models\Portfolio::create([
            'title' => 'Raja Ampat Eco Tourism',
            'category' => 'Konsultasi Destinasi',
            'description' => 'Konsultasi pengembangan ekowisata berkelanjutan.',
            'content' => 'Strategi konservasi lingkungan yang dipadukan dengan pariwisata premium untuk menjaga kelestarian ekosistem bawah laut Raja Ampat.',
            'image' => 'portfolio-3.webp',
        ]);
        \App\Models\Portfolio::create([
            'title' => 'Labuan Bajo Gateway',
            'category' => 'Pengembangan Ekosistem',
            'description' => 'Master plan pengembangan destinasi wisata super prioritas.',
            'content' => 'Pengembangan infrastruktur pendukung pariwisata yang ramah lingkungan dan terintegrasi dengan kearifan lokal NTT.',
            'image' => 'portfolio-4.webp',
        ]);
    }
}
