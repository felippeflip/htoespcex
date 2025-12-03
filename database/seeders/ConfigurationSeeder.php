<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('configurations')->updateOrInsert(
            ['key' => 'pagtesouro_codigo_servico'],
            ['value' => '5994'], // Valor dummy, usuário deve alterar
        );
    }
}
