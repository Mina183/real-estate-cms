<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataRoomStructureSeeder extends Seeder
{
        public function run(): void
        {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('data_room_documents')->truncate();
            DB::table('data_room_folders')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $folders = [
            [
                'number' => '1',
                'name' => 'Marketing Materials',
                'description' => 'Teaser, Executive Summary, Presentation Deck, Term Sheet, Investment Thesis, Market Views, Newsletter',
                'parent' => null,
                'level' => 'public',
            ],
            [
                'number' => '2',
                'name' => 'Fund Constitutional Documents',
                'description' => 'Certificate of Incorporation, Certificate of Incumbency, Articles of Association, Offering Letter',
                'parent' => null,
                'level' => 'public',
            ],
            [
                'number' => '3',
                'name' => 'Offering & Subscription Documents',
                'description' => 'PPM, Subscription Agreement, Application Form, Self Certification Form, KYC/AML Questionnaire',
                'parent' => null,
                'level' => 'public',
            ],
            [
                'number' => '4',
                'name' => 'Reporting',
                'description' => 'Quarterly NAV Reports and investor communications',
                'parent' => null,
                'level' => 'public',
            ],
            [
                'number' => '5',
                'name' => 'Investor Personal Documents',
                'description' => 'Documents specific to individual investors — on request only',
                'parent' => null,
                'level' => 'highly_confidential',
            ],
        ];

        foreach ($folders as $index => $folder) {
            DB::table('data_room_folders')->insert([
                'folder_number' => $folder['number'],
                'folder_name'   => $folder['name'],
                'parent_folder_id' => null,
                'order'         => $index + 1,
                'description'   => $folder['description'],
                'access_level'  => $folder['level'],
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}