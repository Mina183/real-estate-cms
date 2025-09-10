<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ClientsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $partnerId;
    protected $leadSourceId;
    protected $name;

    public function __construct($partnerId = null, $leadSourceId = null, $name = null)
    {
        $this->partnerId = $partnerId;
        $this->leadSourceId = $leadSourceId;
        $this->name = $name;
    }

    public function collection()
    {
        $query = Client::with(['communications', 'documents', 'leadSource', 'channelPartner']);

        // Apply filters if provided
        if ($this->partnerId) {
            $query->where('channel_partner_id', $this->partnerId);
        }

        if ($this->leadSourceId) {
            $query->where('lead_source_id', $this->leadSourceId);
        }

        if ($this->name) {
            $query->where('name', 'like', '%' . $this->name . '%');
        }

        return $query->get()->map(function ($client) {
            return [
                'partner' => $client->channelPartner ? $client->channelPartner->name : 'N/A',
                'lead_source' => $client->leadSource ? $client->leadSource->name : 'N/A',
                'user_type' => ucfirst($client->user_end_user ?? 'N/A'),
                'investor_type' => ucfirst($client->investor_type ?? 'N/A'),
                'name' => $client->name,
                'passport_number' => $client->passport_number ?? 'N/A',
                'phone' => $client->phone ?? 'N/A',
                'email' => $client->email,
                'contact_method' => ucfirst($client->contact_method ?? 'N/A'),
                'nationality' => $client->nationality ?? 'N/A',
                'language' => ucfirst($client->language ?? 'N/A'),
                'resident_country' => $client->resident_country ?? 'N/A',
                'property_type' => $client->preferred_property_type ?? 'N/A',
                'locations' => $client->locations ?? 'N/A',
                'investment_budget' => $client->investment_budget ?? 'N/A',
                'communications_count' => $client->communications->count(),
                'last_communication' => $client->communications->last()?->created_at?->format('Y-m-d H:i') ?? 'Never',
                'documents_count' => $client->documents->count(),
                'registered_date' => $client->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Partner',
            'Lead Source', 
            'User Type',
            'Investor Type',
            'Name',
            'Passport Number',
            'Phone',
            'Email',
            'Contact Method',
            'Nationality',
            'Language',
            'Resident Country',
            'Property Type',
            'Locations',
            'Investment Budget',
            'Communications',
            'Last Communication',
            'Documents',
            'Registered Date'
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
            // Auto-size columns
            'A:S' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Clients Report';
    }
}
