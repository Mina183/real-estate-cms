<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DataRoomService;

class CheckDataRoomAccess
{
    protected DataRoomService $dataRoomService;

    public function __construct(DataRoomService $dataRoomService)
    {
        $this->dataRoomService = $dataRoomService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin and internal users always have access
        if (in_array($user->role, ['admin', 'superadmin', 'data_room_administrator', 'document_owner'])) {
            return $next($request);
        }

        // Check if user is linked to an investor
        if (!$user->investor_id) {
            abort(403, 'You do not have Data Room access.');
        }

        $investor = $user->investor;

        // Check if investor has Data Room access
        if (!$investor->data_room_access_granted) {
            abort(403, 'Your Data Room access has not been granted yet.');
        }

        // Check if access has expired
        if ($investor->data_room_access_expires_at && $investor->data_room_access_expires_at->isPast()) {
            abort(403, 'Your Data Room access has expired.');
        }

        // Check if trying to access specific folder
        if ($request->route('folder')) {
            $folder = $request->route('folder');
            
            if (!$this->dataRoomService->canAccessFolder($investor, $folder)) {
                abort(403, 'You do not have permission to access this folder.');
            }
        }

        // Check if trying to download document
        if ($request->route('document') && $request->routeIs('*.download')) {
            $document = $request->route('document');
            
            if (!$this->dataRoomService->canDownloadFromFolder($investor, $document->folder)) {
                abort(403, 'You do not have permission to download from this folder.');
            }
        }

        // Update last login timestamp
        $investor->update(['data_room_last_login' => now()]);
        $investor->increment('data_room_login_count');

        return $next($request);
    }
}
