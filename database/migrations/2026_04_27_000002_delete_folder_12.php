<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $folder = DB::table('data_room_folders')
            ->where('folder_number', '12')
            ->whereNull('investor_id')
            ->first();

        if (! $folder) {
            return;
        }

        // Delete storage files for any documents in this folder
        $documents = DB::table('data_room_documents')
            ->where('folder_id', $folder->id)
            ->get();

        foreach ($documents as $doc) {
            if ($doc->file_path && Storage::disk('private')->exists($doc->file_path)) {
                Storage::disk('private')->delete($doc->file_path);
            }
        }

        // DB cascade handles documents and subfolders (folder_id FK is cascadeOnDelete)
        DB::table('data_room_folders')->where('id', $folder->id)->delete();
    }

    public function down(): void
    {
        // Folder 12 was a legacy bucket — no restore needed
    }
};
