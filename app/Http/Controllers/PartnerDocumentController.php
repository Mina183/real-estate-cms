<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PartnerDocument;
use App\Models\PartnerDocumentResponse;
use Illuminate\Support\Facades\Auth;
use App\Mail\PartnerResponseSubmitted;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PartnerDocumentController extends Controller
{
    /**
     * Show documents available to the current partner.
     */
public function index()
{
    $partnerId = Auth::id();

    // Get relevant documents (assigned to this partner or global)
    $documents = PartnerDocument::where(function ($q) use ($partnerId) {
            $q->whereNull('partner_id') // shared with all
              ->orWhere('partner_id', $partnerId); // specific
        })
        ->whereIn('status', [
            'waiting_partner_action',
            'waiting_admin_approval',
            'review_only',
            'acknowledged',
            'complete'
        ]) // show relevant statuses
        ->latest()
        ->paginate(10);

    // Mark as seen (only docs in the result, not all unseen blindly)
    PartnerDocument::whereIn('id', $documents->pluck('id')->toArray())
        ->whereNull('seen_by_partner_at')
        ->update(['seen_by_partner_at' => now()]);

    return view('partners.documents.index', compact('documents'));
}

    /**
     * Allow the partner to upload a response to a document.
     */
    public function uploadResponse(Request $request, $id)
{
    $doc = PartnerDocument::findOrFail($id);

    // Shared document → allow all partners to respond
    if (is_null($doc->partner_id)) {
        $partnerId = auth()->id();

        // Validate
        $request->validate([
            'response_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx|max:5120',
        ]);

        // Store file
        $path = $request->file('response_file')->store('partner_responses');

        // Store or update response
        PartnerDocumentResponse::updateOrCreate(
            ['document_id' => $doc->id, 'partner_id' => $partnerId],
            [
                'response_file_path' => $path,
                'response_uploaded_at' => now(),
                'status' => 'waiting_admin_approval',
            ]
        );

        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        $partner = auth()->user();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new PartnerResponseSubmitted($doc, $partner, $admin));
        }

        return redirect()->route('partner.documents.index')->with('success', 'Response uploaded!');
    }

    // If document is specific to this partner
    if ($doc->partner_id !== auth()->id()) {
        return redirect()->back()->with('error', 'You are not allowed to respond to this document.');
    }

    // For specific partner documents, still support legacy direct response
    $request->validate([
        'response_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx|max:5120',
    ]);

    $path = $request->file('response_file')->store('partner_responses');

    $doc->update([
        'response_file_path' => $path,
        'response_uploaded_at' => now(),
        'status' => 'waiting_admin_approval',
    ]);

    // Add this email notification:
    $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
    $partner = auth()->user();
    foreach ($admins as $admin) {
        Mail::to($admin->email)->send(new PartnerResponseSubmitted($doc, $partner, $admin));
    }

    return redirect()->route('partner.documents.index')->with('success', 'Response uploaded!');
}

public function acknowledge($id)
{
    $doc = PartnerDocument::findOrFail($id);
    $userId = auth()->id();

    // CASE 1: Shared with ALL partners
    if ($doc->partner_id === null) {
        $response = $doc->responses()->firstOrNew(['partner_id' => $userId]);

        if ($response->status === null || $response->status === 'review_only') {
            $response->status = 'acknowledged';
            $response->response_uploaded_at = now(); // you can skip or keep this
            $response->save();
        }
    }
    // CASE 2: Uploaded to ONE partner only
    elseif ($doc->partner_id === $userId && $doc->status === 'review_only') {
        $doc->status = 'acknowledged';
        $doc->seen_by_partner_at = now();
        $doc->save();
    }
    // else: not authorized, ignore

    return redirect()->back()->with('success', 'Document marked as reviewed.');
}
}