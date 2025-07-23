<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientCommunication;
use App\Models\ClientDocument;
use App\Models\LeadSource;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = Client::with(['communications', 'documents', 'leadSource'])
        ->where('channel_partner_id', auth()->id());

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('phone')) {
        $query->where('phone', 'like', '%' . $request->phone . '%');
    }

    if ($request->filled('passport_number')) {
    $query->where('passport_number', 'like', '%' . $request->passport_number . '%');
    }

    if ($request->filled('nationality')) {
        $query->where('nationality', 'like', '%' . $request->nationality . '%');
    }

    if ($request->filled('language')) {
        $query->where('language', 'like', '%' . $request->language . '%');
    }

    if ($request->filled('base_location')) {
        $query->where('base_location', 'like', '%' . $request->base_location . '%');
    }

    if ($request->filled('lead_source_id')) {
        $query->where('lead_source_id', $request->lead_source_id);
    }

    if ($request->filled('is_investor')) {
        $query->where('is_investor', $request->is_investor);
    }

    if ($request->filled('investor_type')) {
        $query->where('investor_type', 'like', '%' . $request->investor_type . '%');
    }

    if ($request->filled('preferred_property_type')) {
        $query->where('preferred_property_type', 'like', '%' . $request->preferred_property_type . '%');
    }

    if ($request->filled('preferred_location')) {
        $query->where('preferred_location', 'like', '%' . $request->preferred_location . '%');
    }

    if ($request->filled('uae_visa_required')) {
        $query->where('uae_visa_required', $request->uae_visa_required);
    }

    if ($request->filled('funnel_stage')) {
        $query->where('funnel_stage', 'like', '%' . $request->funnel_stage . '%');
    }

    if ($request->filled('best_contact_method')) {
        $query->where('best_contact_method', 'like', '%' . $request->best_contact_method . '%');
    }

    if ($request->filled('property_detail_type')) {
        $query->where('property_detail_type', 'like', '%' . $request->property_detail_type . '%');
    }

    if ($request->filled('investment_type')) {
        $query->where('investment_type', 'like', '%' . $request->investment_type . '%');
    }

    if ($request->filled('investment_budget')) {
        $query->where('investment_budget', 'like', '%' . $request->investment_budget . '%');
    }

    if ($request->filled('employment_source')) {
        $query->where('employment_source', 'like', '%' . $request->employment_source . '%');
    }

    if ($request->filled('funds_location')) {
        $query->where('funds_location', 'like', '%' . $request->funds_location . '%');
    }

    if ($request->filled('client_id')) {
    $query->where('id', $request->client_id);
}

    $clients = $query->paginate(20);
    foreach ($clients as $client) {
        $client->paginatedCommunications = $client->communications()
            ->latest()
            ->paginate(5, ['*'], "page_comm_{$client->id}");

        $client->paginatedDocuments = $client->documents()
            ->latest()
            ->paginate(5, ['*'], "page_docs_{$client->id}");
    }
    $leadSources = LeadSource::all();

    return view('clients.index', compact('clients', 'leadSources'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leadSources = LeadSource::all();

            $funnelStages = [
                'inquiry' => 'Inquiry',
                'showed interest' => 'Showed Interest',
                'site visit' => 'Site Visit',
                'negotiation' => 'Negotiation',
                'closed' => 'Closed',
            ];
        return view('clients.create', compact('leadSources', 'funnelStages' ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // 1. Custom check for duplicate passport number
        $existing = Client::where('passport_number', $request->passport_number)->first();

        if ($existing) {
            return back()->withErrors([
                'passport_number' => 'This passport number is already registered for ' 
                    . $existing->name . ' (' . $existing->email . ').',
            ])->withInput();
        }

         $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'passport_number' => 'required|string|max:255',
        'nationality' => 'nullable|string|max:100',
        'language' => 'nullable|string|max:100',
        'base_location' => 'nullable|string|max:255',
        'lead_source_id' => 'nullable|exists:lead_sources,id',
        'is_investor' => 'nullable|boolean',
        'investor_type' => 'nullable|string|max:255',
        'preferred_property_type' => 'nullable|string|max:255',
        'preferred_location' => 'nullable|string|max:255',
        'uae_visa_required' => 'nullable|boolean',
        'funnel_stage' => 'nullable|string|max:50',
        'best_contact_method' => 'nullable|string|max:100',
        'property_detail_type' => 'nullable|string|max:255',
        'investment_type' => 'nullable|string|max:255',
        'investment_budget' => 'nullable|string|max:255',
        'employment_source' => 'nullable|string|max:255',
        'funds_location' => 'nullable|string|max:255',
    ]);
        $data['channel_partner_id'] = auth()->id();

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $leadSources = LeadSource::all();

         $funnelStages = [
        'inquiry' => 'Inquiry',
        'showed_interest' => 'Showed Interest',
        'site_visit' => 'Site Visit',
        'negotiation' => 'Negotiation',
        'closed' => 'Closed',
    ];
        return view('clients.edit', compact('client', 'leadSources', 'funnelStages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {

        $existing = Client::where('passport_number', $request->passport_number)
                  ->where('id', '!=', $client->id)
                  ->first();

            if ($existing) {
                return back()->withErrors([
                    'passport_number' => 'This passport number is already registered for '
                        . $existing->name . ' (' . $existing->email . ').',
                ])->withInput();
            }
        $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'passport_number' => 'required|string|max:255',
        'nationality' => 'nullable|string|max:100',
        'language' => 'nullable|string|max:100',
        'base_location' => 'nullable|string|max:255',
        'lead_source_id' => 'nullable|exists:lead_sources,id',
        'is_investor' => 'nullable|boolean',
        'investor_type' => 'nullable|string|max:255',
        'preferred_property_type' => 'nullable|string|max:255',
        'preferred_location' => 'nullable|string|max:255',
        'uae_visa_required' => 'nullable|boolean',
        'funnel_stage' => 'nullable|string|max:50',
        'best_contact_method' => 'nullable|string|max:100',
        'property_detail_type' => 'nullable|string|max:255',
        'investment_type' => 'nullable|string|max:255',
        'investment_budget' => 'nullable|string|max:255',
        'employment_source' => 'nullable|string|max:255',
        'funds_location' => 'nullable|string|max:255',
    ]);

        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Client deleted successfully.');
    }

    public function storeDocument(Request $request, Client $client)
    {
        $request->validate([
            'document' => 'required|file|max:5120', // max 5MB
        ]);

        $file = $request->file('document');
        $path = $file->store('documents');

        $client->documents()->create([
            'filename' => $file->getClientOriginalName(), // original file name
            'path' => $path, // stored path (e.g., documents/filename.docx)
        ]);

        return redirect()->route('clients.index', ['tab' => 'documents']);
    }

    public function storeCommunication(Request $request, Client $client)
    {
        $request->validate([
            'date' => 'required|date',
            'action' => 'nullable|string',
            'update' => 'nullable|string',
            'feedback' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $client->communications()->create($request->only([
            'date', 'action', 'update', 'feedback', 'outcome',
        ]));

        return redirect()->route('clients.index', ['tab' => 'contact']);
    }

    public function editCommunication(Client $client, ClientCommunication $communication)
    {
        if ($communication->client_id !== $client->id) {
        abort(403); // prevent access if communication does not belong to client
    }
        return view('clients.edit_communication', compact('client', 'communication'));
    }

    public function updateCommunication(Request $request, Client $client, ClientCommunication $communication)
    {
    
        if ($communication->client_id !== $client->id) {
        abort(403); // protect against tampering
    }
        $request->validate([
            'date' => 'required|date',
            'action' => 'nullable|string',
            'update' => 'nullable|string',
            'feedback' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $communication->update($request->only(['date', 'action', 'update', 'feedback', 'outcome']));

        return redirect()->route('clients.index', ['tab' => 'contact'])->with('success', 'Communication updated.');
    }
}
