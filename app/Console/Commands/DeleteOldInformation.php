<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Information;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeleteOldInformation extends Command
{
    protected $signature = 'information:delete-old';
    protected $description = 'Hapus data Information yang lebih dari 2 bulan';

    public function handle()
    {
        $dateLimit = Carbon::now()->subMonths(2); // Ambil tanggal 2 bulan yang lalu

        $deleted = Information::where('created_at', '<', $dateLimit)->delete();

        Log::info("Deleted $deleted old records from Information.");

        $this->info("Deleted $deleted old records.");
    }
}

