<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Shift existing folders 6-12 up by one (in reverse order to avoid conflicts)
        foreach ([12, 11, 10, 9, 8, 7, 6] as $old) {
            DB::table('data_room_folders')
                ->whereNull('investor_id')
                ->where('folder_number', (string) $old)
                ->update([
                    'folder_number' => (string) ($old + 1),
                    'order'         => $old + 1,
                ]);
        }

        // Insert new Financial Models folder as #6
        DB::table('data_room_folders')->insert([
            'folder_number'    => '6',
            'folder_name'      => 'Financial Models',
            'parent_folder_id' => null,
            'investor_id'      => null,
            'order'            => 6,
            'description'      => 'Financial models, projections, valuation analysis and scenario planning — investor-facing',
            'access_level'     => 'public',
            'is_active'        => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        // Remove the new folder
        DB::table('data_room_folders')
            ->whereNull('investor_id')
            ->where('folder_number', '6')
            ->where('folder_name', 'Financial Models')
            ->delete();

        // Shift folders 7-13 back down to 6-12
        foreach ([7, 8, 9, 10, 11, 12, 13] as $current) {
            DB::table('data_room_folders')
                ->whereNull('investor_id')
                ->where('folder_number', (string) $current)
                ->update([
                    'folder_number' => (string) ($current - 1),
                    'order'         => $current - 1,
                ]);
        }
    }
};
