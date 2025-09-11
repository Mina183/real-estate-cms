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
                'is_investor' => $client->is_investor === 1 ? 'User' : ($client->is_investor === 0 ? 'End User' : 'N/A'),
                'investor_type' => ucfirst($client->investor_type ?? 'N/A'),
                'name' => $client->name,
                'passport_number' => $client->passport_number ?? 'N/A',
                'phone' => $client->phone ?? 'N/A',
                'email' => $client->email,
                'contact_method' => ucfirst($client->contact_method ?? 'N/A'),
                'nationality' => $client->nationality ?? 'N/A',
                'language' => ucfirst($client->language ?? 'N/A'),
                'base_location' => $client->base_location ?? 'N/A',
                'property_type' => $client->preferred_property_type ?? 'N/A',
                'locations' => $client->locations ?? 'N/A',
                'investment_budget' => $client->investment_budget ?? 'N/A',
                'employment_source' => $client->employment_source ?? 'N/A',
                'funds_location' => $client->funds_location ?? 'N/A',
                'uae_visa_required' => $client->uae_visa_required === 1 ? 'Yes' : ($client->uae_visa_required === 0 ? 'No' : 'N/A'),
                'cp_remarks' => $client->cp_remarks ?? 'N/A',
                'funnel_stage' => $client->funnel_stage ?? 'N/A',
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
            'User/End User',
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
            'Source of Funds',
            'Funds Location',
            'UAE Visa',
            'CP Remarks',
            'Sales Funnel Stage',
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
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0e2442'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ],
        
        // Data rows styling
        'A2:X1000' => [  // X because you have 24 columns
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ],
    ];
}

    public function title(): string
    {
        return 'Clients Report';
    }
}
