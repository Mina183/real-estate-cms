<?php

namespace App\Http\Controllers;

use App\Models\EmailDraft;
use App\Models\EmailSignature;
use App\Models\EmailOnBehalf;
use App\Models\EmailBodyTemplate;
use App\Models\Investor;
use App\Models\DataRoomDocument;
use App\Models\DataRoomFolder;
use App\Models\DocumentSendLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailDraftController extends Controller
{
    /**
     * Show all drafts — for admin approval queue
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', EmailDraft::class);

        $pendingQuery = EmailDraft::where('status', 'pending_approval')
            ->with(['investor', 'createdBy', 'onBehalfOf'])
            ->orderBy('created_at', 'desc');

        if ($pendingSearch = $request->get('pending_search')) {
            $pendingQuery->where(function ($q) use ($pendingSearch) {
                $q->where('subject', 'like', "%{$pendingSearch}%")
                  ->orWhereHas('investor', fn ($iq) => $iq
                      ->where('organization_name', 'like', "%{$pendingSearch}%")
                      ->orWhere('legal_entity_name', 'like', "%{$pendingSearch}%"));
            });
        }

        $pendingDrafts = $pendingQuery->where('is_bulk', false)->get();
        $pendingBulk   = $pendingQuery->newQuery()
            ->where('status', 'pending_approval')
            ->where('is_bulk', true)
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        $myQuery = EmailDraft::where('created_by_user_id', auth()->id())
            ->with(['investor', 'onBehalfOf'])
            ->orderBy('created_at', 'desc');

        if ($mySearch = $request->get('my_search')) {
            $myQuery->where(function ($q) use ($mySearch) {
                $q->where('subject', 'like', "%{$mySearch}%")
                  ->orWhereHas('investor', fn ($iq) => $iq
                      ->where('organization_name', 'like', "%{$mySearch}%")
                      ->orWhere('legal_entity_name', 'like', "%{$mySearch}%"));
            });
        }

        if ($myStatus = $request->get('my_status')) {
            $myQuery->where('status', $myStatus);
        } else {
            $myQuery->whereIn('status', ['draft', 'approved']);
        }

        $myDrafts    = $myQuery->where('is_bulk', false)->get();
        $myBulkDrafts = EmailDraft::where('created_by_user_id', auth()->id())
            ->where('is_bulk', true)
            ->whereIn('status', ['draft', 'approved', 'pending_approval'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('email-drafts.index', compact('pendingDrafts', 'pendingBulk', 'myDrafts', 'myBulkDrafts'));
    }

    /**
     * Show compose form for new draft
     */
    public function create(Request $request)
    {
        $investor = Investor::findOrFail($request->investor_id);
        $this->authorize('update', $investor);

        $signatures = EmailSignature::where('is_active', true)->get();
        $onBehalfOptions = EmailOnBehalf::where('is_active', true)->get();
        $bodyTemplates = EmailBodyTemplate::where('is_active', true)->get();
        $documents = DataRoomDocument::where('status', 'approved')
            ->where('file_type', '!=', 'eml')
            ->where(function($q) use ($investor) {
                $q->whereNull('investor_id')
                  ->orWhere('investor_id', $investor->id);
            })
            ->whereHas('folder', function($q) {
                $q->where('folder_name', '!=', 'Communication Log');
            })
            ->with('folder')
            ->get();

        return view('email-drafts.create', compact(
            'investor', 'signatures', 'onBehalfOptions', 'bodyTemplates', 'documents'
        ));
    }

    /**
     * Store new draft
     */
    public function store(Request $request)
    {
        // ── Bulk draft (from bulk email form) ──────────────────────────────
        if ($request->boolean('is_bulk')) {
            $request->validate([
                'subject'            => 'required|string|max:255',
                'body'               => 'required|string',
                'document_ids'       => 'nullable|array',
                'document_ids.*'     => 'exists:data_room_documents,id',
                'bulk_recipient_type'=> 'required|in:all,stage',
                'bulk_recipient_ids' => 'required|string',
            ]);

            $recipientIds = json_decode($request->input('bulk_recipient_ids'), true) ?? [];

            EmailDraft::create([
                'investor_id'              => null,
                'template_key'             => 'custom',
                'subject'                  => $request->subject,
                'body'                     => $request->body,
                'document_ids'             => $request->input('document_ids', []),
                'cc_emails'                => [],
                'status'                   => 'pending_approval',
                'created_by_user_id'       => auth()->id(),
                'is_bulk'                  => true,
                'bulk_recipient_type'      => $request->bulk_recipient_type,
                'bulk_recipient_stage'     => $request->bulk_recipient_stage,
                'bulk_assigned_to_user_id' => $request->boolean('bulk_assigned_to_me') ? auth()->id() : null,
                'bulk_recipient_ids'       => $recipientIds,
                'bulk_recipient_count'     => count($recipientIds),
            ]);

            return redirect()->route('email-drafts.index')
                ->with('success', 'Bulk email draft submitted for approval. It will be sent to ' . count($recipientIds) . ' investor(s) once approved.');
        }

        // ── Single draft ───────────────────────────────────────────────────
        $validated = $request->validate([
            'investor_id'        => 'required|exists:investors,id',
            'on_behalf_of_id'    => 'nullable|exists:email_on_behalf,id',
            'signature_id'       => 'nullable|exists:email_signatures,id',
            'subject'            => 'required|string|max:255',
            'body'               => 'required|string',
            'document_ids'       => 'nullable|array',
            'document_ids.*'     => 'exists:data_room_documents,id',
            'submit_for_approval'=> 'boolean',
            'cc_custom_email'    => 'nullable|email|max:255',
        ]);

        $investor = Investor::find($validated['investor_id']);
        $ccEmails = [];
        if ($request->boolean('cc_placement_agent') && $investor?->placement_agent_email) {
            $ccEmails[] = $investor->placement_agent_email;
        }
        if ($request->filled('cc_custom_email')) {
            $ccEmails[] = $request->input('cc_custom_email');
        }

        $draft = EmailDraft::create([
            'investor_id'        => $validated['investor_id'],
            'template_key'       => 'custom',
            'on_behalf_of_id'    => $validated['on_behalf_of_id'] ?? null,
            'signature_id'       => $validated['signature_id'] ?? null,
            'subject'            => $validated['subject'],
            'body'               => $validated['body'],
            'document_ids'       => $validated['document_ids'] ?? [],
            'cc_emails'          => $ccEmails,
            'status'             => $request->boolean('submit_for_approval') ? 'pending_approval' : 'draft',
            'created_by_user_id' => auth()->user()->id,
        ]);

        if ($draft->status === 'pending_approval') {
            return redirect()->route('email-drafts.index')
                ->with('success', 'Draft submitted for approval.');
        }

        if ($request->boolean('preview_draft')) {
            return redirect()->route('email-drafts.preview', $draft);
        }

        return redirect()->route('email-drafts.index')
            ->with('success', 'Draft saved.');
    }

    /**
     * Show draft for editing
     */
    public function edit(EmailDraft $emailDraft)
    {
        $this->authorize('update', $emailDraft);

        $signatures = EmailSignature::where('is_active', true)->get();
        $onBehalfOptions = EmailOnBehalf::where('is_active', true)->get();
        $bodyTemplates = EmailBodyTemplate::where('is_active', true)->get();
        $documents = DataRoomDocument::where('status', 'approved')
            ->where('file_type', '!=', 'eml')
            ->where(function($q) use ($emailDraft) {
                $q->whereNull('investor_id')
                  ->orWhere('investor_id', $emailDraft->investor_id);
            })
            ->whereHas('folder', function($q) {
                $q->where('folder_name', '!=', 'Communication Log');
            })
            ->with('folder')
            ->get();

        return view('email-drafts.edit', compact(
            'emailDraft', 'signatures', 'onBehalfOptions', 'bodyTemplates', 'documents'
        ));
    }

    /**
     * Update draft
     */
    public function update(Request $request, EmailDraft $emailDraft)
    {
        $this->authorize('update', $emailDraft);

        $validated = $request->validate([
            'on_behalf_of_id'     => 'nullable|exists:email_on_behalf,id',
            'signature_id'        => 'nullable|exists:email_signatures,id',
            'subject'             => 'required|string|max:255',
            'body'                => 'required|string',
            'document_ids'        => 'nullable|array',
            'document_ids.*'      => 'exists:data_room_documents,id',
            'submit_for_approval' => 'boolean',
            'cc_custom_email'     => 'nullable|email|max:255',
        ]);

        $user = auth()->user();
        $isAdmin = in_array($user->role, ['superadmin', 'admin']);

        if ($request->boolean('submit_for_approval')) {
            $newStatus = 'pending_approval';
        } elseif ($isAdmin) {
            // Admins saving edits preserve the current approval status
            $newStatus = $emailDraft->status;
        } else {
            // Non-admin edits reset to draft — requires re-approval
            $newStatus = 'draft';
        }

        $investor = $emailDraft->investor;
        $ccEmails = [];
        if ($request->boolean('cc_placement_agent') && $investor?->placement_agent_email) {
            $ccEmails[] = $investor->placement_agent_email;
        }
        if ($request->filled('cc_custom_email')) {
            $ccEmails[] = $request->input('cc_custom_email');
        }

        $emailDraft->update([
            'on_behalf_of_id' => $validated['on_behalf_of_id'] ?? null,
            'signature_id'    => $validated['signature_id'] ?? null,
            'subject'         => $validated['subject'],
            'body'            => $validated['body'],
            'document_ids'    => $validated['document_ids'] ?? [],
            'cc_emails'       => $ccEmails,
            'status'          => $newStatus,
        ]);

        $message = $request->boolean('submit_for_approval') ? 'Draft submitted for approval.' : 'Draft updated.';

        return redirect()->route('email-drafts.index')
            ->with('success', $message);
    }

    /**
     * Submit draft for approval — creator only, draft status only
     */
    public function submitForApproval(EmailDraft $emailDraft)
    {
        $this->authorize('update', $emailDraft);

        if ($emailDraft->status !== 'draft') {
            return back()->with('error', 'Only drafts can be submitted for approval.');
        }

        $emailDraft->update(['status' => 'pending_approval']);

        return back()->with('success', 'Draft submitted for approval.');
    }

    /**
     * Delete a draft — only draft status, creator or admin
     */
    public function destroy(EmailDraft $emailDraft)
    {
        $this->authorize('delete', $emailDraft);

        $emailDraft->delete();

        return redirect()->route('email-drafts.index')
            ->with('success', 'Draft deleted.');
    }

    /**
     * Approve draft — admin/superadmin only
     */
    public function approve(EmailDraft $emailDraft)
    {
        $this->authorize('approve', $emailDraft);

        $emailDraft->update([
            'status'              => 'approved',
            'approved_by_user_id' => auth()->id(),
            'approved_at'         => now(),
        ]);

        return redirect()->route('email-drafts.index')
            ->with('success', 'Draft approved. Ready to send.');
    }

    /**
     * Send approved draft
     */
    public function send(EmailDraft $emailDraft)
    {
        $this->authorize('send', $emailDraft);

        if ($emailDraft->status !== 'approved') {
            return back()->with('error', 'Only approved drafts can be sent.');
        }

        // ── Bulk send ──────────────────────────────────────────────────────
        if ($emailDraft->is_bulk) {
            return $this->sendBulk($emailDraft);
        }

        $investor = $emailDraft->investor;
        $primaryContact = $investor->contacts->where('is_primary', true)->first()
            ?? $investor->contacts->first();

        if (!$primaryContact || !$primaryContact->email) {
            return back()->with('error', 'No primary contact with email found.');
        }

        $documents = collect();
        if ($emailDraft->document_ids) {
            $documents = DataRoomDocument::whereIn('id', $emailDraft->document_ids)->get();
        }

        $signature = $emailDraft->signature;
        $onBehalf = $emailDraft->onBehalfOf;

        $body = $this->replacePlaceholders($emailDraft->body, $investor);
        $subject = $this->replacePlaceholders($emailDraft->subject, $investor);

        try {
            $ccEmails = $emailDraft->cc_emails ?? [];
            Mail::send([], [], function ($message) use ($emailDraft, $primaryContact, $documents, $signature, $onBehalf, $subject, $body, $ccEmails) {
                $message->to($primaryContact->email, $primaryContact->full_name)
                    ->subject($subject);
                if (!empty($ccEmails)) {
                    $message->cc($ccEmails);
                }
                $message
                    ->html(
                        view('emails.draft', [
                            'body'      => $body,
                            'signature' => $signature,
                            'onBehalf'  => $onBehalf,
                        ])->render()
                    );

                foreach ($documents as $doc) {
                    if (\Storage::disk('private')->exists($doc->file_path)) {
                        $message->attachData(
                            \Storage::disk('private')->get($doc->file_path),
                            $doc->document_name . '.' . $doc->file_type
                        );
                    }
                }
            });

            $emailDraft->update(['status' => 'sent']);

        } catch (\Exception $e) {
            \Log::error('Failed to send draft email', ['draft_id' => $emailDraft->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }

        // Log in sent emails history
        DocumentSendLog::create([
            'investor_id'              => $investor->id,
            'document_id'              => null,
            'document_name'            => null,
            'template'                 => 'custom_draft',
            'email_subject'            => $subject,
            'sent_by_user_id'          => auth()->user()->id,
            'sent_to_email'            => $primaryContact->email,
            'document_version'         => null,
            'requires_acknowledgement' => false,
            'sent_at'                  => now(),
        ]);

        // Archive .eml to investor's Communication Log folder
        $this->saveEmailToDataRoom(
            $investor,
            $subject,
            view('emails.draft', ['body' => $body, 'signature' => $signature, 'onBehalf' => $onBehalf])->render(),
            $primaryContact->email,
            $documents->isNotEmpty() ? $documents : null
        );

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Email sent successfully.');
    }

    private function sendBulk(EmailDraft $emailDraft): \Illuminate\Http\RedirectResponse
    {
        $investors = \App\Models\Investor::with('contacts')
            ->whereIn('id', $emailDraft->bulk_recipient_ids ?? [])
            ->get();

        $documents = collect();
        if ($emailDraft->document_ids) {
            $documents = DataRoomDocument::whereIn('id', $emailDraft->document_ids)->get();
        }

        $signature = $emailDraft->signature;
        $onBehalf  = $emailDraft->onBehalfOf;
        $sent      = 0;
        $skipped   = 0;

        foreach ($investors as $investor) {
            $contact = $investor->contacts->where('is_primary', true)->first()
                       ?? $investor->contacts->first();

            if (!$contact || !$contact->email) {
                $skipped++;
                continue;
            }

            $body    = $this->replacePlaceholders($emailDraft->body, $investor);
            $subject = $this->replacePlaceholders($emailDraft->subject, $investor);

            try {
                Mail::send([], [], function ($message) use ($contact, $subject, $body, $signature, $onBehalf, $documents) {
                    $message->to($contact->email, $contact->full_name)->subject($subject);
                    $message->html(view('emails.draft', ['body' => $body, 'signature' => $signature, 'onBehalf' => $onBehalf])->render());
                    foreach ($documents as $doc) {
                        if (\Storage::disk('private')->exists($doc->file_path)) {
                            $message->attachData(\Storage::disk('private')->get($doc->file_path), $doc->document_name . '.' . $doc->file_type);
                        }
                    }
                });

                DocumentSendLog::create([
                    'investor_id'              => $investor->id,
                    'template'                 => 'bulk_draft',
                    'email_subject'            => $subject,
                    'sent_by_user_id'          => auth()->id(),
                    'sent_to_email'            => $contact->email,
                    'requires_acknowledgement' => false,
                    'sent_at'                  => now(),
                ]);

                $this->saveEmailToDataRoom(
                    $investor,
                    $subject,
                    view('emails.draft', ['body' => $body, 'signature' => $signature, 'onBehalf' => $onBehalf])->render(),
                    $contact->email,
                    $documents->isNotEmpty() ? $documents : null
                );

                $sent++;
            } catch (\Exception $e) {
                \Log::error('Bulk email send failed for investor ' . $investor->id, ['error' => $e->getMessage()]);
                $skipped++;
            }
        }

        $emailDraft->update(['status' => 'sent']);

        $msg = "Bulk email sent to {$sent} investor(s).";
        if ($skipped > 0) $msg .= " {$skipped} skipped (no contact email).";

        return redirect()->route('email-drafts.index')->with('success', $msg);
    }

    /**
     * Preview the rendered email
     */
    public function preview(EmailDraft $emailDraft)
    {
        $this->authorize('update', $emailDraft);

        $investor       = $emailDraft->investor->load('contacts');
        $body           = $this->replacePlaceholders($emailDraft->body, $investor);
        $subject        = $this->replacePlaceholders($emailDraft->subject, $investor);
        $signature      = $emailDraft->signature;
        $onBehalf       = $emailDraft->onBehalfOf;
        $primaryContact = $investor->contacts->where('is_primary', true)->first()
                          ?? $investor->contacts->first();

        return view('email-drafts.preview', compact(
            'emailDraft', 'investor', 'body', 'subject', 'signature', 'onBehalf', 'primaryContact'
        ));
    }

    private function saveEmailToDataRoom(Investor $investor, string $subject, string $htmlContent, string $to, $documents = null): void
    {
        $folder = DataRoomFolder::where('investor_id', $investor->id)
            ->where('folder_name', 'Communication Log')
            ->first();

        if (!$folder) return;

        $date     = now()->format('D, d M Y H:i:s O');
        $from     = auth()->user()->email;
        $boundary = md5(uniqid());

        $emlContent = "Date: {$date}\r\n"
            . "From: {$from}\r\n"
            . "To: {$to}\r\n"
            . "Subject: {$subject}\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n"
            . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "\r\n"
            . $htmlContent . "\r\n";

        if ($documents) {
            foreach ($documents as $doc) {
                if (Storage::disk('private')->exists($doc->file_path)) {
                    $fileContent  = base64_encode(Storage::disk('private')->get($doc->file_path));
                    $emlContent  .= "--{$boundary}\r\n"
                        . "Content-Type: application/{$doc->file_type}; name=\"{$doc->document_name}.{$doc->file_type}\"\r\n"
                        . "Content-Transfer-Encoding: base64\r\n"
                        . "Content-Disposition: attachment; filename=\"{$doc->document_name}.{$doc->file_type}\"\r\n"
                        . "\r\n"
                        . $fileContent . "\r\n";
                }
            }
        }

        $emlContent .= "--{$boundary}--";

        $fileName    = now()->format('Y-m-d_His') . '_' . Str::slug($subject) . '.eml';
        $storagePath = 'data-room/' . $folder->folder_number . '/' . $fileName;

        Storage::disk('private')->put($storagePath, $emlContent);

        DataRoomDocument::create([
            'folder_id'     => $folder->id,
            'investor_id'   => $investor->id,
            'document_name' => $subject . ' — ' . now()->format('d M Y H:i'),
            'file_path'     => $storagePath,
            'file_type'     => 'eml',
            'file_size'     => strlen($emlContent),
            'version'       => '1.0',
            'description'   => 'Auto-archived sent email',
            'status'        => 'approved',
            'uploaded_by'   => auth()->user()->id,
        ]);
    }

    private function replacePlaceholders(string $body, Investor $investor): string
    {
        $investorName = $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor';

        // Use regex so {{ investor_name }} with spaces also matches
        return preg_replace('/\{\{\s*investor_name\s*\}\}/', $investorName, $body);
    }
}