<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\DataRoomDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvestorEmailController extends Controller
{
    protected array $templates = [
        'teaser' => [
            'name' => 'Template 1 - Fund Teaser',
            'subject' => 'Triton Real Estate Fund – Fund Teaser',
            'stages' => ['prospect'],
        ],
        'executive_summary' => [
            'name' => 'Template 2 - Executive Summary & Term Sheet',
            'subject' => 'Triton Real Estate Fund – Executive Summary & Term Sheet',
            'stages' => ['eligibility_review'],
        ],
        'investor_presentation' => [
            'name' => 'Template 3 - Investor Presentation',
            'subject' => 'Triton Real Estate Fund – Investor Presentation',
            'stages' => ['eligibility_review'],
        ],
        'eligibility_questionnaire' => [
            'name' => 'Template 4 - Eligibility & Professional Client Questionnaire',
            'subject' => 'Triton Real Estate Fund – Investor Eligibility Questionnaire',
            'stages' => ['prospect', 'eligibility_review'],
        ],
    ];

    /**
     * Show compose form for single investor
     */
    public function compose(Investor $investor)
    {
        $documents = DataRoomDocument::where('status', 'approved')
            ->with('folder')
            ->orderBy('document_name')
            ->get();

        return view('investors.send-email', [
            'investor' => $investor,
            'templates' => $this->templates,
            'documents' => $documents,
            'recipients' => 'single',
        ]);
    }

    /**
     * Show compose form for bulk send
     */
    public function composeBulk(Request $request)
    {
        $stage = $request->get('stage');

        $investorsQuery = Investor::with('contacts');
        
        if ($stage) {
            $investorsQuery->where('stage', $stage);
        }

        $investors = $investorsQuery->get();

        $documents = DataRoomDocument::where('status', 'approved')
            ->with('folder')
            ->orderBy('document_name')
            ->get();

        $stages = [
            'prospect', 'eligibility_review', 'ppm_issued', 
            'kyc_in_progress', 'subscription_signed', 'approved', 
            'funded', 'active', 'monitored'
        ];

        return view('investors.send-email', [
            'investor' => null,
            'templates' => $this->templates,
            'documents' => $documents,
            'recipients' => $stage ? 'stage' : 'all',
            'selectedStage' => $stage,
            'investors' => $investors,
            'stages' => $stages,
        ]);
    }

    /**
     * Send email
     */
    public function send(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:data_room_documents,id',
            'recipient_type' => 'required|in:single,stage,all',
            'investor_id' => 'required_if:recipient_type,single|exists:investors,id',
            'stage' => 'required_if:recipient_type,stage',
            // Custom template fields
            'custom_subject' => 'required_if:template,custom|nullable|string|max:255',
            'custom_body' => 'required_if:template,custom|nullable|string',
            'requires_acknowledgement' => 'boolean',
        ]);

        $template = $this->templates[$request->template] ?? null;
        $isCustom = $request->template === 'custom';

        if (!$template && !$isCustom) {
            return back()->withErrors(['template' => 'Invalid template selected.']);
        }

        $documents = collect();
        if ($request->document_ids) {
            $documents = DataRoomDocument::whereIn('id', $request->document_ids)->get();
        }

        // Get recipients
        $investors = collect();
        if ($request->recipient_type === 'single') {
            $investors = Investor::where('id', $request->investor_id)->get();
        } elseif ($request->recipient_type === 'stage') {
            $investors = Investor::where('stage', $request->stage)->get();
        } elseif ($request->recipient_type === 'all') {
            $investors = Investor::all();
        }

        $sent = 0;
        $failed = 0;

        foreach ($investors as $investor) {
            $primaryContact = $investor->contacts->where('is_primary', true)->first()
                ?? $investor->contacts->first();

            if (!$primaryContact || !$primaryContact->email) {
                $failed++;
                continue;
            }

            // Generate acknowledgement token
            $token = \Str::uuid()->toString();
            $acknowledgementUrl = route('email.acknowledge', $token);
            $requiresAck = $request->boolean('requires_acknowledgement', !$isCustom);

            $subject = $isCustom 
                ? $request->custom_subject 
                : $template['subject'] . ' | ' . $primaryContact->full_name;

            $viewData = [
                'investor' => $investor,
                'contact' => $primaryContact,
                'documents' => $documents,
                'senderName' => auth()->user()->name,
                'senderTitle' => auth()->user()->title ?? 'Investor Relations',
                'senderEmail' => auth()->user()->email,
                'senderPhone' => auth()->user()->phone ?? '',
                'acknowledgementUrl' => $requiresAck ? $acknowledgementUrl : null,
                'customBody' => $isCustom ? $request->custom_body : null,
            ];

            try {
                Mail::send(
                    $isCustom ? 'emails.investor.custom' : 'emails.investor.' . $request->template,
                    $viewData,
                    function ($message) use ($primaryContact, $subject, $documents) {
                        $message->to($primaryContact->email, $primaryContact->full_name)
                            ->subject($subject);

                        foreach ($documents as $doc) {
                            if (\Storage::disk('private')->exists($doc->file_path)) {
                                $message->attachData(
                                    \Storage::disk('private')->get($doc->file_path),
                                    $doc->document_name . '.' . $doc->file_type
                                );
                            }
                        }
                    }
                );

                // Log send - one record per investor
                $logId = \DB::table('document_send_logs')->insertGetId([
                    'investor_id' => $investor->id,
                    'document_id' => $documents->first()?->id,
                    'document_name' => $documents->pluck('document_name')->implode(', '),
                    'template' => $request->template,
                    'email_subject' => $subject,
                    'sent_by_user_id' => auth()->id(),
                    'sent_to_email' => $primaryContact->email,
                    'document_version' => $documents->first()?->version,
                    'acknowledgement_token' => $requiresAck ? $token : null,
                    'requires_acknowledgement' => $requiresAck,
                    'sent_at' => now(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to send investor email', [
                    'investor_id' => $investor->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('investors.index')
            ->with('success', "Email sent to {$sent} investor(s)." . ($failed > 0 ? " {$failed} failed." : ''));
    }

        public function preview(Request $request)
        {
            $templateKey = $request->get('template', 'teaser');
            $template = $this->templates[$templateKey] ?? $this->templates['teaser'];

            $fakeContact = (object) ['full_name' => 'John Smith'];

            return view('emails.investor.' . $templateKey, [
                'investor' => null,
                'contact' => $fakeContact,
                'documents' => collect(),
                'senderName' => auth()->user()->name,
                'senderTitle' => auth()->user()->title ?? 'Investor Relations',
                'senderEmail' => auth()->user()->email,
                'senderPhone' => auth()->user()->phone ?? '',
                'acknowledgementUrl' => '#preview-only',
                'customBody' => null,
            ]);
        }

        public function acknowledge(string $token)
        {
            $log = \DB::table('document_send_logs')
                ->where('acknowledgement_token', $token)
                ->first();

            if (!$log) {
                abort(404, 'Invalid acknowledgement link.');
            }

            if ($log->acknowledged_at) {
                return view('emails.acknowledged', ['alreadyAcknowledged' => true]);
            }

            \DB::table('document_send_logs')
                ->where('acknowledgement_token', $token)
                ->update(['acknowledged_at' => now()]);

            return view('emails.acknowledged', ['alreadyAcknowledged' => false]);
        }
}