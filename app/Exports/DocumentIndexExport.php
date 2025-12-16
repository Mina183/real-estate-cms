<?php

namespace App\Exports;

use App\Models\DataRoomDocument;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DocumentIndexExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return DataRoomDocument::with('folder')
            ->orderBy('folder_id')
            ->get()
            ->map(function ($doc) {
                return [
                    'section' => $doc->folder->folder_number ?? 'N/A',
                    'folder_name' => $doc->folder->folder_name ?? 'N/A',
                    'document_name' => $doc->document_name,
                    'version' => $doc->version ?? 'N/A',
                    'status' => ucfirst($doc->status),
                    'file_type' => strtoupper($doc->file_type),
                    'file_size' => $this->formatFileSize($doc->file_size),
                    'uploaded_date' => $doc->created_at->format('Y-m-d'),
                    'last_updated' => $doc->updated_at->format('Y-m-d'),
                    'description' => $doc->description ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Section',
            'Folder Name',
            'Document Name',
            'Version',
            'Status',
            'File Type',
            'File Size',
            'Uploaded Date',
            'Last Updated',
            'Description',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50'], // Dark blue-gray
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Add borders to all cells
            'A1:J' . ($this->collection()->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Section
            'B' => 35,  // Folder Name
            'C' => 45,  // Document Name
            'D' => 10,  // Version
            'E' => 15,  // Status
            'F' => 12,  // File Type
            'G' => 12,  // File Size
            'H' => 15,  // Uploaded Date
            'I' => 15,  // Last Updated
            'J' => 50,  // Description
        ];
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}