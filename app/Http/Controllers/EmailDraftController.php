<?php

namespace App\Http\Controllers;

use App\Models\EmailDraft;
use App\Models\EmailSignature;
use App\Models\EmailOnBehalf;
use App\Models\EmailBodyTemplate;
use App\Models\Investor;
use App\Models\DataRoomDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailDraftController extends Controller
{
    /**
     * Show all drafts — for admin approval queue
     */
    public function index()
    {
        $this->authorize('viewAny', EmailDraft::class);

        $pendingDrafts = EmailDraft::where('status', 'pending_approval')
            ->with(['investor', 'createdBy', 'onBehalfOf'])
            ->orderBy('created_at', 'desc')
            ->get();

        $myDrafts = EmailDraft::where('created_by_user_id', auth()->id())
            ->whereIn('status', ['draft', 'approved'])
            ->with(['investor', 'onBehalfOf'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('email-drafts.index', compact('pendingDrafts', 'myDrafts'));
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
            ->whereNull('investor_id')
            ->orWhere('investor_id', $investor->id)
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
        $validated = $request->validate([
            'investor_id'       => 'required|exists:investors,id',
            'on_behalf_of_id'   => 'nullable|exists:email_on_behalf,id',
            'signature_id'      => 'nullable|exists:email_signatures,id',
            'subject'           => 'required|string|max:255',
            'body'              => 'required|string',
            'document_ids'      => 'nullable|array',
            'document_ids.*'    => 'exists:data_room_documents,id',
            'submit_for_approval' => 'boolean',
        ]);

        $draft = EmailDraft::create([
            'investor_id'       => $validated['investor_id'],
            'template_key'      => 'custom',
            'on_behalf_of_id'   => $validated['on_behalf_of_id'] ?? null,
            'signature_id'      => $validated['signature_id'] ?? null,
            'subject'           => $validated['subject'],
            'body'              => $validated['body'],
            'document_ids'      => $validated['document_ids'] ?? [],
            'status'            => $request->boolean('submit_for_approval') ? 'pending_approval' : 'draft',
            'created_by_user_id' => auth()->id(),
        ]);

        if ($draft->status === 'pending_approval') {
            return redirect()->route('email-drafts.index')
                ->with('success', 'Draft submitted for approval.');
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
            ->where(function($q) use ($emailDraft) {
                $q->whereNull('investor_id')
                  ->orWhere('investor_id', $emailDraft->investor_id);
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
        ]);

        $emailDraft->update([
            'on_behalf_of_id' => $validated['on_behalf_of_id'] ?? null,
            'signature_id'    => $validated['signature_id'] ?? null,
            'subject'         => $validated['subject'],
            'body'            => $validated['body'],
            'document_ids'    => $validated['document_ids'] ?? [],
            'status'          => $request->boolean('submit_for_approval') ? 'pending_approval' : 'draft',
        ]);

        return redirect()->route('email-drafts.index')
            ->with('success', $request->boolean('submit_for_approval') ? 'Draft submitted for approval.' : 'Draft updated.');
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

        try {
            Mail::send([], [], function ($message) use ($emailDraft, $primaryContact, $documents, $signature, $onBehalf) {
                $message->to($primaryContact->email, $primaryContact->full_name)
                    ->subject($emailDraft->subject)
                    ->html(
                        view('emails.investor.draft', [
                            'body' => $body,
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

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Email sent successfully.');
    }

    private function replacePlaceholders(string $body, Investor $investor): string
        {
            $investorName = $investor->organization_name ?? $investor->legal_entity_name ?? 'Investor';
            
            return str_replace(
                ['{{investor_name}}'],
                [$investorName],
                $body
            );
        }
}