<?php

namespace App\Http\Controllers;

use App\Models\DataRoomDocument;
use App\Models\DocumentAccessLink;
use App\Models\DocumentAccessRequest;
use App\Notifications\NewAccessRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentPublicAccessController extends Controller
{
    /**
     * Show the public access page.
     * If the session holds an active approved request for this token, show downloads.
     * Otherwise show the request form.
     */
    public function show(Request $request, string $token)
    {
        $link = DocumentAccessLink::where('token', $token)
            ->with('package.items.document')
            ->firstOrFail();

        $accessRequest = $this->resolveSessionRequest($token);

        // If no session, check DB for any active approved request on this link
        if (! $accessRequest || ! $accessRequest->isActive()) {
            $accessRequest = DocumentAccessRequest::where('document_access_link_id', $link->id)
                ->where('status', 'approved')
                ->where('expires_at', '>', now())
                ->latest('approved_at')
                ->first();

            if ($accessRequest) {
                session(["doc_access_{$token}" => $accessRequest->id]);
            }
        }

        if ($accessRequest && $accessRequest->isActive()) {
            if (is_null($accessRequest->first_accessed_at)) {
                $accessRequest->update(['first_accessed_at' => now()]);
            }
            return view('doc-access.downloads', compact('link', 'accessRequest'));
        }

        return view('doc-access.form', compact('link'));
    }

    /**
     * Handle the access request form submission.
     */
    public function submit(Request $request, string $token)
    {
        $link = DocumentAccessLink::where('token', $token)->with('createdBy')->firstOrFail();

        $validated = $request->validate([
            'requester_name'  => 'required|string|max:255',
            'requester_email' => 'required|email|max:255',
        ]);

        // Check if a request already exists for this email on this link
        $existing = DocumentAccessRequest::where('document_access_link_id', $link->id)
            ->where('requester_email', $validated['requester_email'])
            ->latest()
            ->first();

        if ($existing) {
            session(["doc_access_{$token}" => $existing->id]);

            if ($existing->isActive()) {
                return redirect()->route('doc-access.show', $token);
            }

            return redirect()->route('doc-access.confirmation', $token);
        }

        // Only record consent fields on the very first submission — by email, across all links
        $hasPriorConsent = DocumentAccessRequest::where('requester_email', $validated['requester_email'])
            ->whereNotNull('consent_recorded_at')
            ->exists();

        $accessRequest = DocumentAccessRequest::create(array_merge([
            'document_access_link_id' => $link->id,
            'requester_name'          => $validated['requester_name'],
            'requester_email'         => $validated['requester_email'],
            'status'                  => 'pending',
            'ip_address'              => $request->ip(),
            'user_agent'              => $request->header('User-Agent') ?? $request->userAgent(),
        ], $hasPriorConsent ? [] : [
            'consent_recorded_at'    => now(),
            'consent_source'         => 'document_access_request',
            'dp_notice_version'      => config('compliance.dp_notice_version'),
            'privacy_notice_version' => config('compliance.privacy_notice_version'),
        ]));

        session(["doc_access_{$token}" => $accessRequest->id]);

        // Notify the link creator about the new request
        $linkCreator = $accessRequest->link?->createdBy;
        if ($linkCreator) {
            $linkCreator->notify(new NewAccessRequestNotification($accessRequest));
        }

        return redirect()->route('doc-access.confirmation', $token);
    }

    /**
     * Show the confirmation / status page after submitting a request.
     */
    public function confirmation(string $token)
    {
        $link = DocumentAccessLink::where('token', $token)->firstOrFail();

        $accessRequest = $this->resolveSessionRequest($token);

        return view('doc-access.confirmation', compact('link', 'accessRequest'));
    }

    /**
     * Serve a document file if the session request is approved and not expired.
     */
    public function download(Request $request, string $token, int $documentId)
    {
        $link = DocumentAccessLink::where('token', $token)
            ->with('package.items')
            ->firstOrFail();

        // Ensure document belongs to this package
        $packageDocIds = $link->package->items->pluck('data_room_document_id');
        if (! $packageDocIds->contains($documentId)) {
            abort(403, 'This document is not part of the requested package.');
        }

        // Verify session-based access
        $accessRequest = $this->resolveSessionRequest($token);

        if (! $accessRequest || ! $accessRequest->isActive()) {
            return redirect()->route('doc-access.show', $token)
                ->with('error', 'Your access has expired or has not been approved yet.');
        }

        $document = DataRoomDocument::findOrFail($documentId);

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $downloadName = $document->document_name;
        if ($document->file_type && ! str_ends_with(strtolower($downloadName), '.' . $document->file_type)) {
            $downloadName .= '.' . $document->file_type;
        }

        $accessRequest->increment('download_count');
        $accessRequest->update([
            'last_downloaded_at'       => now(),
            'last_download_ip'         => $request->ip(),
            'last_download_user_agent' => $request->userAgent(),
        ]);

        return Storage::disk('private')->download($document->file_path, $downloadName);
    }

    /**
     * Check if a given email already has a consent record on file.
     * Used by the public form to adapt the consent notice text.
     */
    public function consentStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->query('email', '');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['has_consent' => false, 'consented_at' => null]);
        }

        $record = DocumentAccessRequest::where('requester_email', $email)
            ->whereNotNull('consent_recorded_at')
            ->latest('consent_recorded_at')
            ->first();

        return response()->json([
            'has_consent'  => (bool) $record,
            'consented_at' => $record?->consent_recorded_at?->format('d M Y'),
        ]);
    }

    private function resolveSessionRequest(string $token): ?DocumentAccessRequest
    {
        $requestId = session("doc_access_{$token}");

        if (! $requestId) {
            return null;
        }

        return DocumentAccessRequest::find($requestId);
    }
}
