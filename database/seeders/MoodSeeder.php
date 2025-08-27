<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mood;

class MoodSeeder extends Seeder
{
    public function run()
    {
        $moods = [
            [
                'name' => 'خوشحال',
                'emoji' => '😊',
                'description' => 'احساس شادی و رضایت از شرایط زندگی یا لحظه حال.'
            ],
            [
                'name' => 'غمگین',
                'emoji' => '😢',
                'description' => 'حس ناراحتی و اندوه که ممکن است به دلیل اتفاقی ناخوشایند باشد.'
            ],
            [
                'name' => 'آرام',
                'emoji' => '😌',
                'description' => 'احساسی از آرامش و رهایی از استرس و تنش.'
            ],
            [
                'name' => 'عصبانی',
                'emoji' => '😠',
                'description' => 'حس خشم یا ناراحتی شدید نسبت به موقعیتی خاص.'
            ],
            [
                'name' => 'هیجان‌زده',
                'emoji' => '🤩',
                'description' => 'احساس شور و شوق فراوان برای تجربه یا رویدادی خاص.'
            ],
            [
                'name' => 'خسته',
                'emoji' => '😴',
                'description' => 'کمبود انرژی و نیاز به استراحت یا خواب.'
            ]
        ];

        foreach ($moods as $mood) {
            Mood::create($mood);
        }
    }
}
