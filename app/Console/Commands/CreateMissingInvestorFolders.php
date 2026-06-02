<?php

namespace App\Console\Commands;

use App\Models\Investor;
use App\Models\DataRoomFolder;
use App\Services\DataRoomService;
use Illuminate\Console\Command;

class CreateMissingInvestorFolders extends Command
{
    protected $signature   = 'investors:create-missing-folders {--id= : Run for a single investor ID only}';
    protected $description = 'Create Section 12 (private) Data Room folder structure for investors that are missing it';

    public function handle(DataRoomService $service): void
    {
        $query = Investor::query();

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $investors = $query->get();
        $created   = 0;
        $skipped   = 0;

        foreach ($investors as $investor) {
            $exists = DataRoomFolder::where('investor_id', $investor->id)
                ->whereNull('parent_folder_id')
                ->exists();

            if ($exists) {
                $this->line("  SKIP  [{$investor->id}] {$investor->organization_name} — folder already exists");
                $skipped++;
                continue;
            }

            $service->createInvestorFolders($investor);
            $this->info("  CREATED [{$investor->id}] {$investor->organization_name}");
            $created++;
        }

        $this->newLine();
        $this->info("Done. Created: {$created} | Skipped (already had folders): {$skipped}");
    }
}
