<?php

namespace App\Http\Controllers;

use App\Models\DocumentAccessLink;
use App\Models\DocumentAccessRequest;
use App\Models\DocumentPackage;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentAccessLinkController extends Controller
{
    public function index(Investor $investor)
    {
        $links = $investor->documentAccessLinks()
            ->with(['package', 'accessRequests', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('document-access-links.index', compact('investor', 'links'));
    }

    public function create(Investor $investor)
    {
        $packages = DocumentPackage::orderBy('name')->get();

        return view('document-access-links.create', compact('investor', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_id'          => 'required|exists:investors,id',
            'document_package_id'  => 'required|exists:document_packages,id',
            'label'                => 'nullable|string|max:255',
        ]);

        DocumentAccessLink::create([
            'investor_id'          => $validated['investor_id'],
            'document_package_id'  => $validated['document_package_id'],
            'label'                => $validated['label'] ?? null,
            'token'                => Str::random(48),
            'created_by_user_id'   => auth()->id(),
        ]);

        $investor = Investor::findOrFail($validated['investor_id']);

        return redirect()->route('document-access-links.index', $investor)
            ->with('success', 'Access link generated successfully.');
    }

    public function destroy(DocumentAccessLink $documentAccessLink)
    {
        $investor = $documentAccessLink->investor;
        $documentAccessLink->delete();

        if ($investor) {
            return redirect()->route('document-access-links.index', $investor)
                ->with('success', 'Access link deleted.');
        }

        return redirect()->route('document-access-requests.index')
            ->with('success', 'Access link deleted.');
    }

    public function requests()
    {
        $requests = DocumentAccessRequest::with(['link.investor', 'link.package', 'approvedBy'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('document-access-links.requests', compact('requests'));
    }

    public function approve(DocumentAccessRequest $documentAccessRequest)
    {
        $documentAccessRequest->update([
            'status'               => 'approved',
            'approved_by_user_id'  => auth()->id(),
            'approved_at'          => now(),
            'expires_at'           => now()->addHours(48),
        ]);

        return back()->with('success', 'Request approved. Investor access expires in 48 hours.');
    }

    public function reject(DocumentAccessRequest $documentAccessRequest)
    {
        $documentAccessRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Request rejected.');
    }
}
