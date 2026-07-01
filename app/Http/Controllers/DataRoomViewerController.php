<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DataRoomViewerController extends Controller
{
    public function index(Request $request)
    {
        $query = InvestorUser::whereHas('investor', fn ($q) => $q->where('data_room_access_level', 'viewer'))
            ->with('investor')
            ->orderBy('created_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->get('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->get('status') === 'deactivated') {
            $query->where('is_active', false);
        }

        $viewers = $query->get();

        return view('data-room-viewers.index', compact('viewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:investor_users,email',
            'password' => 'required|string|min:8',
            'pin'      => 'nullable|digits:6',
        ]);

        $investor = Investor::create([
            'legal_entity_name'             => $validated['name'],
            'investor_type'                 => 'individual',
            'jurisdiction'                  => 'N/A',
            'data_room_access_level'        => 'viewer',
            'data_room_access_granted'      => true,
            'data_room_access_granted_at'   => now(),
            'stage'                         => 'prospect',
            'is_professional_client'        => true,
            'difc_dp_consent'               => true,
            'agreed_confidentiality'        => true,
            'acknowledged_ppm_confidential' => true,
            'confirmed_professional_client' => true,
        ]);

        InvestorUser::create([
            'investor_id' => $investor->id,
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'portal_pin'  => $request->filled('pin') ? Hash::make($validated['pin']) : null,
            'is_active'   => true,
        ]);

        $msg = "Viewer account created for {$validated['name']}.";
        if ($request->filled('pin')) {
            $msg .= ' A 6-digit PIN has been set for this account.';
        }

        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $viewer = InvestorUser::findOrFail($id);
        $viewer->update(['is_active' => false]);

        return back()->with('success', 'Account deactivated.');
    }
}
