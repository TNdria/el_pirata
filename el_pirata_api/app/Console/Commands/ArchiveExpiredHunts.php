<?php

namespace App\Console\Commands;

use App\Models\hunting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveExpiredHunts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-expired-hunts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archivage automatique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $now = Carbon::now();
        $updated = hunting::where('is_archived', 0)
            ->where('start_date', '<', $now)
            ->update(['is_archived' => 1]);

        $this->info("✅ Archived {$updated} expired hunts.");

    }
}
