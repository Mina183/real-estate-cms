<?php

namespace App\Http\Controllers;

use App\Models\DocumentAccessLink;
use App\Models\DocumentAccessRequest;
use App\Models\DocumentPackage;
use App\Models\Investor;
use App\Notifications\DocumentAccessApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentAccessLinkController extends Controller
{
    public function index(Investor $investor)
    {
        $this->authorize('update', $investor);

        $links = $investor->documentAccessLinks()
            ->with(['package', 'accessRequests', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('document-access-links.index', compact('investor', 'links'));
    }

    public function create(Investor $investor)
    {
        $this->authorize('update', $investor);

        $packages = DocumentPackage::orderBy('name')->get();

        return view('document-access-links.create', compact('investor', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'investor_id'          => 'required|exists:investors,id',
            'document_package_id'  => 'nullable|exists:document_packages,id',
            'document_ids'         => 'nullable|array|min:1',
            'document_ids.*'       => 'exists:data_room_documents,id',
            'label'                => 'nullable|string|max:255',
        ]);

        $investor = Investor::findOrFail($request->investor_id);

        $this->authorize('update', $investor);

        if (! $request->document_package_id && empty($request->document_ids)) {
            return back()->withErrors(['document_package_id' => 'Select an existing package or choose individual documents.'])->withInput();
        }

        if ($request->document_package_id) {
            $packageId = $request->document_package_id;
        } else {
            $investorLabel = $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor #' . $investor->id;
            $package = DocumentPackage::create([
                'name'               => '[Custom] ' . $investorLabel . ' — ' . now()->format('d M Y'),
                'created_by_user_id' => auth()->user()->id,
            ]);
            foreach ($request->document_ids as $docId) {
                $package->items()->create(['data_room_document_id' => $docId]);
            }
            $packageId = $package->id;
        }

        DocumentAccessLink::create([
            'investor_id'         => $investor->id,
            'document_package_id' => $packageId,
            'label'               => $request->label ?? null,
            'token'               => Str::random(48),
            'created_by_user_id'  => auth()->user()->id,
        ]);

        return redirect(route('investors.show', $investor) . '#doc-links')
            ->with('success', 'Access link generated successfully.');
    }

    public function destroy(DocumentAccessLink $documentAccessLink)
    {
        $investor = $documentAccessLink->investor;

        if ($investor) {
            $this->authorize('update', $investor);
        } else {
            $this->authorize('manage-settings');
        }

        $documentAccessLink->delete();

        if ($investor) {
            return redirect(route('investors.show', $investor) . '#doc-links')
                ->with('success', 'Access link deleted.');
        }

        return redirect()->route('document-access-requests.index')
            ->with('success', 'Access link deleted.');
    }

    public function requests()
    {
        $query = DocumentAccessRequest::with(['link.investor', 'link.package', 'approvedBy']);

        $isFiltered = auth()->user()->role === 'relationship_manager';

        if ($isFiltered) {
            $query->whereHas('link.investor', function ($q) {
                $q->where('assigned_to_user_id', auth()->user()->id);
            });
        }

        $requests = $query
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('document-access-links.requests', compact('requests', 'isFiltered'));
    }

    public function approve(DocumentAccessRequest $documentAccessRequest)
    {
        $investor = $documentAccessRequest->link?->investor;

        if (auth()->user()->role === 'relationship_manager') {
            if (! $investor || $investor->assigned_to_user_id !== auth()->user()->id) {
                abort(403);
            }
        } else {
            $this->authorize('manage-settings');
        }

        $documentAccessRequest->update([
            'status'               => 'approved',
            'approved_by_user_id'  => auth()->user()->id,
            'approved_at'          => now(),
            'expires_at'           => now()->addHours(48),
        ]);

        $notifyUser = $documentAccessRequest->link?->package?->notifyUser;
        if ($notifyUser) {
            $notifyUser->notify(new DocumentAccessApprovedNotification($documentAccessRequest));
        }

        // If the investor hasn't confirmed DIFC DP consent, redirect RM to the Eligibility tab
        // to confirm it — requesting document access constitutes DIFC DP consent.
        if ($investor && ! $investor->difc_dp_consent) {
            return redirect(route('investors.edit', $investor))
                ->with('difc_consent_prompt', true)
                ->with('success', 'Request approved. Investor access expires in 48 hours.');
        }

        return back()->with('success', 'Request approved. Investor access expires in 48 hours.');
    }

    public function reject(DocumentAccessRequest $documentAccessRequest)
    {
        $investor = $documentAccessRequest->link?->investor;

        if (auth()->user()->role === 'relationship_manager') {
            if (! $investor || $investor->assigned_to_user_id !== auth()->user()->id) {
                abort(403);
            }
        } else {
            $this->authorize('manage-settings');
        }

        $documentAccessRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Request rejected.');
    }
}
