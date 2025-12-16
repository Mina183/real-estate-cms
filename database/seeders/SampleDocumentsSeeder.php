<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        // Find folders by their numbers
        $folders = [
            '1.1' => DB::table('data_room_folders')->where('folder_number', '1.1')->first(),
            '1.2' => DB::table('data_room_folders')->where('folder_number', '1.2')->first(),
            '2.1' => DB::table('data_room_folders')->where('folder_number', '2.1')->first(),
            '3.2' => DB::table('data_room_folders')->where('folder_number', '3.2')->first(),
        ];

        $documents = [
            // Executive Summary Deck
            [
                'folder_id' => $folders['1.1']->id,
                'document_name' => 'Triton Fund III - Executive Summary.pdf',
                'file_path' => 'data-room/1.1/executive-summary-v2.0.pdf',
                'file_type' => 'pdf',
                'file_size' => 2458624, // ~2.4MB
                'version' => '2.0',
                'description' => 'Updated executive summary with Q4 2024 performance data',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subDays(5),
                'approved_by' => 1,
            ],
            [
                'folder_id' => $folders['1.1']->id,
                'document_name' => 'Investment Highlights Presentation.pptx',
                'file_path' => 'data-room/1.1/investment-highlights.pptx',
                'file_type' => 'pptx',
                'file_size' => 5242880, // 5MB
                'version' => '1.5',
                'description' => 'Key investment highlights and fund differentiation',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subDays(10),
                'approved_by' => 1,
            ],
            
            // PPM
            [
                'folder_id' => $folders['1.2']->id,
                'document_name' => 'Private Placement Memorandum - Triton Fund III.pdf',
                'file_path' => 'data-room/1.2/ppm-triton-fund-iii-final.pdf',
                'file_type' => 'pdf',
                'file_size' => 15728640, // 15MB
                'version' => '3.1',
                'description' => 'Official PPM - Final version dated December 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subDays(30),
                'approved_by' => 1,
            ],
            [
                'folder_id' => $folders['1.2']->id,
                'document_name' => 'PPM Supplement - Risk Factors.pdf',
                'file_path' => 'data-room/1.2/ppm-supplement-risks.pdf',
                'file_type' => 'pdf',
                'file_size' => 1048576, // 1MB
                'version' => '1.0',
                'description' => 'Additional risk factor disclosures',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subDays(25),
                'approved_by' => 1,
            ],
            
            // Legal & Constitutional
            [
                'folder_id' => $folders['2.1']->id,
                'document_name' => 'Certificate of Incorporation - DIFC.pdf',
                'file_path' => 'data-room/2.1/certificate-incorporation.pdf',
                'file_type' => 'pdf',
                'file_size' => 524288, // 512KB
                'version' => '1.0',
                'description' => 'Official DIFC registration certificate',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subMonths(3),
                'approved_by' => 1,
            ],
            [
                'folder_id' => $folders['2.1']->id,
                'document_name' => 'Fund Memorandum and Articles.pdf',
                'file_path' => 'data-room/2.1/memorandum-articles.pdf',
                'file_type' => 'pdf',
                'file_size' => 3145728, // 3MB
                'version' => '2.0',
                'description' => 'Amended M&A effective November 2024',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subMonths(2),
                'approved_by' => 1,
            ],
            
            // Key Personnel
            [
                'folder_id' => $folders['3.2']->id,
                'document_name' => 'Management Team Biographies.pdf',
                'file_path' => 'data-room/3.2/team-bios.pdf',
                'file_type' => 'pdf',
                'file_size' => 2097152, // 2MB
                'version' => '1.2',
                'description' => 'Detailed bios of fund management team',
                'status' => 'approved',
                'uploaded_by' => 1,
                'approved_at' => now()->subDays(15),
                'approved_by' => 1,
            ],
            
            // Some pending documents
            [
                'folder_id' => $folders['1.1']->id,
                'document_name' => 'Q1 2025 Performance Update - DRAFT.pdf',
                'file_path' => 'data-room/1.1/q1-2025-draft.pdf',
                'file_type' => 'pdf',
                'file_size' => 1572864,
                'version' => '0.9',
                'description' => 'Draft quarterly update pending final review',
                'status' => 'pending_review',
                'uploaded_by' => 1,
                'approved_at' => null,
                'approved_by' => null,
            ],
        ];

        foreach ($documents as $doc) {
            DB::table('data_room_documents')->insert(array_merge($doc, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Add some activity logs to show usage
        $this->addSampleActivityLogs();
    }

    private function addSampleActivityLogs()
    {
        $documents = DB::table('data_room_documents')->get();
        
        $activities = [
            [
                'user_id' => 1,
                'activity_type' => 'view',
                'resource_type' => 'document',
                'resource_id' => $documents->first()->id,
                'ip_address' => '192.168.1.100',
                'activity_at' => now()->subHours(2),
            ],
            [
                'user_id' => 1,
                'activity_type' => 'download',
                'resource_type' => 'document',
                'resource_id' => $documents->first()->id,
                'ip_address' => '192.168.1.100',
                'activity_at' => now()->subHours(1),
            ],
        ];

        foreach ($activities as $activity) {
            DB::table('data_room_activity_log')->insert(array_merge($activity, [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'metadata' => json_encode(['session_id' => 'demo-session-123']),
            ]));
        }
    }
}