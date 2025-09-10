<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PartnerClientsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $partnerId;

    public function __construct($partnerId)
    {
        $this->partnerId = $partnerId;
    }

    public function collection()
    {
        return Client::where('channel_partner_id', $this->partnerId)
                    ->with(['communications', 'documents', 'leadSource'])
                    ->get()
                    ->map(function ($client) {
                        return [
                            'name' => $client->name,
                            'email' => $client->email,
                            'phone' => $client->phone ?? 'N/A',
                            'nationality' => $client->nationality ?? 'N/A',
                            'lead_source' => $client->leadSource ? $client->leadSource->name : 'N/A',
                            'investment_budget' => $client->investment_budget ?? 'N/A',
                            'property_type' => $client->preferred_property_type ?? 'N/A',
                            'communications_count' => $client->communications->count(),
                            'last_communication' => $client->communications->last()?->created_at?->format('Y-m-d H:i') ?? 'Never',
                            'registered_date' => $client->created_at->format('Y-m-d H:i'),
                        ];
                    });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email', 
            'Phone',
            'Nationality',
            'Lead Source',
            'Investment Budget',
            'Property Type',
            'Communications',
            'Last Communication',
            'Registered Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0e2442'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'My Clients Report';
    }
}
