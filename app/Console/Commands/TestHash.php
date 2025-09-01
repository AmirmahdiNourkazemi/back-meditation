<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestHash extends Command
{
    protected $signature = 'test:hash';
    protected $description = 'Test Hash::make and Hash::check';

    public function handle()
    {
        $plain = 'mypassword123';
        $hashed = Hash::make($plain);

        $this->info("Plain: $plain");
        $this->info("Hashed: $hashed");

        $check = Hash::check($plain, $hashed) ? 'Match' : 'Does not match';
        $checkWrong = Hash::check('wrongpass', $hashed) ? 'Match' : 'Does not match';

        $this->info("Check correct password: $check");
        $this->info("Check wrong password: $checkWrong");
    }
}
