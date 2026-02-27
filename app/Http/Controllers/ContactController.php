<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Investor;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request, Investor $investor)
    {
        $this->authorize('update', $investor);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'role' => 'required|string',
            'is_primary' => 'boolean',
            'can_sign_documents' => 'boolean',
            'receives_capital_calls' => 'boolean',
            'receives_distributions' => 'boolean',
            'receives_reports' => 'boolean',
            'title' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
        ]);

        // If new contact is primary, remove primary from others
        if ($request->boolean('is_primary')) {
            $investor->contacts()->update(['is_primary' => false]);
        }

        $investor->contacts()->create($request->all());

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Contact added successfully.');
    }

    public function destroy(Investor $investor, Contact $contact)
    {
        $this->authorize('update', $investor);

        $contact->delete();

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Contact removed.');
    }
}
