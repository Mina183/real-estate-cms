<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    public function index(Request $request)
{
    $query = Client::with(['communications', 'documents', 'leadSource', 'channelPartner']);

    // Optional partner filter
    if ($request->filled('channel_partner_id')) {
        $query->where('channel_partner_id', $request->channel_partner_id);
    }

    if ($request->filled('lead_source_id')) {
        $query->where('lead_source_id', $request->lead_source_id);
    }

    // Other filters if needed (name, email, etc.)
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    $clients = $query->paginate(20);

    // Load partner list for filter dropdown
    $partners = \App\Models\User::where('role', 'channel_partner')->get();
    $leadSources = \App\Models\LeadSource::all();

    return view('admin.clients.index', compact('clients', 'partners', 'leadSources'));
}
}
