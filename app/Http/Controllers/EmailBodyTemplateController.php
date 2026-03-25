<?php

namespace App\Http\Controllers;

use App\Models\EmailBodyTemplate;
use Illuminate\Http\Request;

class EmailBodyTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailBodyTemplate::orderBy('created_at', 'desc')->get();
        return view('email-body-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email-body-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_suggestion' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        EmailBodyTemplate::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('email-body-templates.index')
            ->with('success', 'Template created.');
    }

    public function edit(EmailBodyTemplate $emailBodyTemplate)
    {
        return view('email-body-templates.edit', compact('emailBodyTemplate'));
    }

    public function update(Request $request, EmailBodyTemplate $emailBodyTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_suggestion' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $emailBodyTemplate->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('email-body-templates.index')
            ->with('success', 'Template updated.');
    }

    public function destroy(EmailBodyTemplate $emailBodyTemplate)
    {
        $emailBodyTemplate->delete();
        return redirect()->route('email-body-templates.index')
            ->with('success', 'Template deleted.');
    }
}