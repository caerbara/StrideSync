<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RunningSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RunningSessionSeeder extends Seeder
{
    public function run(): void
    {
        // First, ensure there's at least one user to assign sessions to
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        for ($i = 0; $i < 25; $i++) {
            $startTime = Carbon::now()->subDays(rand(1, 30))->addHours(rand(5, 20));
            $endTime = (clone $startTime)->addMinutes(rand(20, 60));

            RunningSession::create([
                'user_id' => $user->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'average_pace' => rand(400, 700) . ' s/km',
                'duration' => $startTime->diff($endTime)->format('%H:%I:%S'),
                'location_name' => 'Park ' . rand(1, 5),
            ]);
        }
    }
}
