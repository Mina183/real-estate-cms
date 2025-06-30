<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leadSources = LeadSource::where('channel_partner_id', Auth::id())->get();
        return view('lead_sources.index', compact('leadSources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lead_sources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        LeadSource::create([
            'name' => $validated['name'],
            'channel_partner_id' => Auth::id(),
        ]);

        return redirect()->route('lead-sources.index')->with('success', 'Lead Source added successfully.');
    }
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
