<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PartnersExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return User::where('role', 'channel_partner')
                  ->withCount(['clients', 'leadSources'])
                  ->get()
                  ->map(function ($partner) {
                      return [
                          'name' => $partner->name,
                          'email' => $partner->email,
                          'phone' => $partner->phone ?? 'N/A',
                          'clients_count' => $partner->clients_count ?? 0,
                          'lead_sources_count' => $partner->lead_sources_count ?? 0,
                          'is_approved' => $partner->is_approved ? 'Yes' : 'No',
                          'registered_date' => $partner->created_at->format('Y-m-d H:i'),
                          'last_login' => $partner->last_login_at ? $partner->last_login_at->format('Y-m-d H:i') : 'Never',
                      ];
                  });
    }

    public function headings(): array
    {
        return [
            'Partner Name',
            'Email',
            'Phone',
            'Total Clients',
            'Lead Sources',
            'Approved',
            'Registered Date',
            'Last Login'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0e2442'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Data rows
            'A:H' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Partners Report';
    }
}
