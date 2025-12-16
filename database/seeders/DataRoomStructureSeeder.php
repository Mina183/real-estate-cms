<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataRoomStructureSeeder extends Seeder
{
    public function run(): void
    {
        $folders = [
            // Section 0
            ['number' => '0.0', 'name' => 'Read Me - How to Use This Data Room', 'description' => 'One-page PDF with purpose and contact details', 'parent' => null, 'level' => 'public'],
            ['number' => '0.1', 'name' => 'Document Index & Version Log', 'description' => 'Excel listing of all files', 'parent' => null, 'level' => 'public'],
            
            // Section 1
            ['number' => '1', 'name' => 'Fund Overview & Key Documents', 'parent' => null, 'level' => 'restricted'],
            ['number' => '1.1', 'name' => 'Executive Summary Deck', 'parent' => '1', 'level' => 'restricted'],
            ['number' => '1.2', 'name' => 'Private Placement Memorandum (PPM)', 'parent' => '1', 'level' => 'confidential'],
            ['number' => '1.3', 'name' => 'Term Sheet / Key Terms', 'parent' => '1', 'level' => 'restricted'],
            ['number' => '1.4', 'name' => 'One-Pager / Fact Sheet', 'parent' => '1', 'level' => 'restricted'],
            ['number' => '1.5', 'name' => 'FAQ for Investors', 'parent' => '1', 'level' => 'restricted'],
            
            // Section 2
            ['number' => '2', 'name' => 'Legal & Regulatory', 'parent' => null, 'level' => 'confidential'],
            ['number' => '2.1', 'name' => 'Fund Structure & Constitutional Docs', 'parent' => '2', 'level' => 'confidential'],
            ['number' => '2.2', 'name' => 'Regulatory Status & Licences', 'parent' => '2', 'level' => 'confidential'],
            ['number' => '2.3', 'name' => 'Material Agreements - Fund Level', 'parent' => '2', 'level' => 'highly_confidential'],
            ['number' => '2.4', 'name' => 'Policies & Manuals', 'parent' => '2', 'level' => 'confidential'],
            
            // Section 3
            ['number' => '3', 'name' => 'Governance & Management', 'parent' => null, 'level' => 'restricted'],
            ['number' => '3.1', 'name' => 'Board & Governance', 'parent' => '3', 'level' => 'confidential'],
            ['number' => '3.2', 'name' => 'Key Personnel Bios', 'parent' => '3', 'level' => 'restricted'],
            ['number' => '3.3', 'name' => 'Organisation Charts', 'parent' => '3', 'level' => 'restricted'],
            
            // Add more sections as needed...
            ['number' => '4', 'name' => 'Track Record & Case Studies', 'parent' => null, 'level' => 'restricted'],
            ['number' => '5', 'name' => 'Fund Economics & Waterfall', 'parent' => null, 'level' => 'confidential'],
            ['number' => '6', 'name' => 'Strategy, Pipeline & Investment Process', 'parent' => null, 'level' => 'confidential'],
            ['number' => '7', 'name' => 'Asset-Level Files', 'parent' => null, 'level' => 'confidential'],
            ['number' => '8', 'name' => 'Risk Management, ESG & Compliance', 'parent' => null, 'level' => 'confidential'],
            ['number' => '9', 'name' => 'Operations, Service Providers & Systems', 'parent' => null, 'level' => 'restricted'],
            ['number' => '10', 'name' => 'Reporting, DDQ & Q&A', 'parent' => null, 'level' => 'restricted'],
            ['number' => '11', 'name' => 'Marketing & Communications', 'parent' => null, 'level' => 'public'],
            ['number' => '12', 'name' => 'Investor-Specific (Tightly Permissioned)', 'parent' => null, 'level' => 'highly_confidential'],
        ];

        foreach ($folders as $index => $folder) {
            $parentId = null;
            if ($folder['parent']) {
                $parent = DB::table('data_room_folders')
                    ->where('folder_number', $folder['parent'])
                    ->first();
                $parentId = $parent?->id;
            }

            DB::table('data_room_folders')->insert([
                'folder_number' => $folder['number'],
                'folder_name' => $folder['name'],
                'parent_folder_id' => $parentId,
                'order' => $index,
                'description' => $folder['description'] ?? null,
                'access_level' => $folder['level'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
