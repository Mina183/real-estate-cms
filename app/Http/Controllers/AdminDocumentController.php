<?php

namespace App\Http\Controllers;

use App\Models\PartnerDocument;
use App\Models\PartnerDocumentResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Partner;

class AdminDocumentController extends Controller
{
    // Show the upload form
public function create()
{
    $partners = User::where('role', 'channel_partner')->get();
    $triggeringSharedDocs = collect(); // Use collections for consistency
    $triggeringDocs = collect();       // For debug display
    $showRedDot = false;

    // Get all shared documents
    $sharedDocs = PartnerDocument::whereNull('partner_id')->get();

    foreach ($sharedDocs as $doc) {
        foreach ($partners as $partner) {
            // Find partner's response for this shared doc
            $response = PartnerDocumentResponse::where('document_id', $doc->id)
                ->where('partner_id', $partner->id)
                ->first();

            if (! $response || in_array($response->status, [
                'waiting_partner_action',
                'review_only',
                'waiting_admin_approval', // ✅ NEW status included
                null
            ])) {
                // Add this doc once
                if (! $triggeringSharedDocs->contains('id', $doc->id)) {
                    $triggeringSharedDocs->push($doc);
                }

                // Add actual or fake response for debug display
                if ($response) {
                    $triggeringDocs->push($response);
                } else {
                    $fake = new \App\Models\PartnerDocumentResponse();
                    $fake->id = 0;
                    $fake->document_id = $doc->id;
                    $fake->status = null;
                    $fake->partner = $partner;
                    $triggeringDocs->push($fake);
                }
            }
        }
    }

    $showRedDot = $triggeringSharedDocs->isNotEmpty();

    return view('admin.documents.create', compact(
        'partners',
        'showRedDot',
        'triggeringSharedDocs',
        'triggeringDocs'
    ));
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx|max:5120',
        'partner_id' => 'nullable|exists:users,id',
        'requires_response' => 'nullable|boolean',
    ]);

    try {
        $storedPath = $request->file('file')->store('partner_documents', 'public');

        PartnerDocument::create([
            'title' => $data['title'],
            'filename' => $request->file('file')->getClientOriginalName(),
            'file_path' => $storedPath,
            'uploaded_by' => auth()->id(),
            'partner_id' => $data['partner_id'] ?? null,
            'status' => $request->has('requires_response') ? 'waiting_partner_action' : 'review_only',
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Document uploaded successfully!');
    } catch (\Exception $e) {
        \Log::error('Document upload failed: ' . $e->getMessage());
        return back()->with('error', 'Upload failed. Please try again.');
    }
}

public function approve($id)
{
    $doc = PartnerDocument::findOrFail($id);

    if ($doc->status !== 'waiting_admin_approval') {
        return redirect()->back()->with('error', 'Only documents waiting for approval can be marked complete.');
    }

    $doc->status = 'complete';
    $doc->reviewed_at = now();
    $doc->save();

    return redirect()->back()->with('success', 'Document marked as complete.');
}

public function approveResponse($id)
{
    $response = PartnerDocumentResponse::findOrFail($id);

    if ($response->status !== 'waiting_admin_approval') {
        return back()->with('error', 'This response is already approved.');
    }

    $response->status = 'complete';
    $response->save();

    return back()->with('success', 'Partner response approved successfully.');
}

    // Show all uploaded documents
public function index()
{
    $documents = PartnerDocument::with(['partner', 'responses'])->latest()->paginate(20);
    $totalPartners = User::where('role', 'channel_partner')->count();
    return view('admin.documents.index', compact('documents', 'totalPartners'));
}

    public function show($id)
{
    $doc = PartnerDocument::with('responses.partner')->findOrFail($id);

    return view('admin.documents.show', compact('doc'));
}
}