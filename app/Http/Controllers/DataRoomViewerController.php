<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DataRoomViewerController extends Controller
{
    public function index()
    {
        $viewers = InvestorUser::whereHas('investor', fn($q) => $q->where('data_room_access_level', 'viewer'))
            ->with('investor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('data-room-viewers.index', compact('viewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:investor_users,email',
            'password' => 'required|string|min:8',
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
            'is_active'   => true,
        ]);

        return back()->with('success', "Viewer account created for {$validated['name']}.");
    }

    public function destroy($id)
    {
        $viewer = InvestorUser::findOrFail($id);
        $viewer->update(['is_active' => false]);

        return back()->with('success', 'Account deactivated.');
    }
}
