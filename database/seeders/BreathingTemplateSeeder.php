<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BreathingTemplate;

class BreathingTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'نفس آرام', 'inhale' => 4, 'exhale' => 4, 'repeat' => 5],
            ['name' => 'نفس تمرکزی', 'inhale' => 3, 'exhale' => 3, 'repeat' => 8],
            ['name' => 'نفس تسکین دهنده', 'inhale' => 6, 'exhale' => 6, 'repeat' => 10],
            ['name' => 'نفس انرژی بخش', 'inhale' => 2, 'exhale' => 2, 'repeat' => 12],
            ['name' => 'نفس عمیق', 'inhale' => 5, 'exhale' => 5, 'repeat' => 7],
            ['name' => 'نفس آرامش بخش', 'inhale' => 4, 'exhale' => 6, 'repeat' => 6],
            ['name' => 'نفس پاکسازی', 'inhale' => 3, 'exhale' => 5, 'repeat' => 8],
            ['name' => 'نفس ذهنی', 'inhale' => 4, 'exhale' => 4, 'repeat' => 9],
            ['name' => 'نفس کنترل استرس', 'inhale' => 5, 'exhale' => 7, 'repeat' => 6],
            ['name' => 'نفس تمرینی', 'inhale' => 3, 'exhale' => 4, 'repeat' => 10],
        ];

        foreach ($templates as $template) {
            BreathingTemplate::create($template);
        }
    }
}
