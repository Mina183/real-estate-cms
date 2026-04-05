<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $this->authorize('manage-settings');

        return view('reports.index');
    }

    public function placementAgentsExport()
    {
        $this->authorize('manage-settings');

        $investors = Investor::where('source_of_introduction', 'placement_agent')
            ->with('fund')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'placement-agents-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($investors) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens it correctly
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Investor Name',
                'Type',
                'Jurisdiction',
                'Stage',
                'Fund',
                'Target Commitment',
                'Currency',
                'Placement Agent Name',
                'Placement Agent Email',
                'Date Added',
            ]);

            foreach ($investors as $inv) {
                fputcsv($file, [
                    $inv->organization_name ?? $inv->legal_entity_name ?? '—',
                    ucfirst(str_replace('_', ' ', $inv->investor_type ?? '')),
                    $inv->jurisdiction ?? '—',
                    ucwords(str_replace('_', ' ', $inv->stage ?? '')),
                    $inv->fund->fund_name ?? '—',
                    $inv->target_commitment_amount ?? '',
                    $inv->currency ?? '',
                    $inv->placement_agent_name ?? '—',
                    $inv->placement_agent_email ?? '—',
                    $inv->created_at?->format('Y-m-d') ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
