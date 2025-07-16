<?php

namespace App\Http\Controllers;

use App\Models\PartnerDocument;
use App\Models\PartnerDocumentResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    // Show the upload form
public function create()
{
    $partners = User::where('role', 'channel_partner')->get();

    $triggeringSharedDocs = collect(); // For shared documents
    $triggeringAssignedDocs = collect(); // ✅ For assigned documents
    $triggeringDocs = collect(); // For debug display
    $showRedDot = false;

    // === Shared Documents Check ===
    $sharedDocs = PartnerDocument::whereNull('partner_id')->get();

    foreach ($sharedDocs as $doc) {
        foreach ($partners as $partner) {
            $response = PartnerDocumentResponse::where('document_id', $doc->id)
                ->where('partner_id', $partner->id)
                ->first();

            if (! $response || in_array($response->status, [
                'waiting_partner_action',
                'review_only',
                'waiting_admin_approval',
                null
            ])) {
                if (! $triggeringSharedDocs->contains('id', $doc->id)) {
                    $triggeringSharedDocs->push($doc);
                }

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

    // === ✅ Assigned Documents Check (new) ===
    $assignedDocs = PartnerDocument::whereNotNull('partner_id')
        ->whereIn('status', ['waiting_partner_action', 'review_only', 'waiting_admin_approval'])
        ->get();

    foreach ($assignedDocs as $doc) {
        if (! $triggeringAssignedDocs->contains('id', $doc->id)) {
            $triggeringAssignedDocs->push($doc);
        }
    }

    // === Final red dot flag ===
    $showRedDot = $triggeringSharedDocs->isNotEmpty() || $triggeringAssignedDocs->isNotEmpty();

    return view('admin.documents.create', compact(
        'partners',
        'showRedDot',
        'triggeringSharedDocs',
        'triggeringAssignedDocs',
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
        $storedPath = $request->file('file')->store('partner_documents');

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
public function destroy($id)
{
    Log::info("Entered destroy() method for document ID: {$id}");
    $doc = PartnerDocument::findOrFail($id);

    // Delete shared responses if they exist
    if ($doc->partner_id === null) {
        PartnerDocumentResponse::where('document_id', $doc->id)->delete();
    }

    // Delete the original file if path looks correct
    if ($doc->file_path) {
        Log::info('Attempting to delete file: ' . $doc->file_path);

        if (Storage::exists($doc->file_path)) {
            Storage::delete($doc->file_path);
            Log::info('File deleted.');
        } else {
            Log::warning('File not found: ' . $doc->file_path);
        }
    }

    // Same for response file
    if ($doc->response_file_path) {
        Log::info('Attempting to delete response file: ' . $doc->response_file_path);

        if (Storage::exists($doc->response_file_path)) {
            Storage::delete($doc->response_file_path);
            Log::info('Response file deleted.');
        } else {
            Log::warning('Response file not found: ' . $doc->response_file_path);
        }
    }

    $doc->delete();

    return redirect()->route('admin.documents.index')->with('success', 'Document deleted successfully.');
}
}