<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\DataRoomPermission;
use App\Models\DataRoomFolder;
use App\Models\DataRoomActivityLog;
use Carbon\Carbon;

class DataRoomService
{
    /**
     * Folder access mapping by level
     */
    protected array $accessLevelFolders = [
        'none' => [],
        'prospect' => [
            '0.0', // Read Me First
            '1.1', // Executive Summary / Teaser
        ],
        'qualified' => [
            '0.0', '1.1', '1.2', '1.3', // All of Section 1
            '2.1', '2.2', '2.3', '2.4', // Fund Documents
            '3.1', '3.2', // Legal & Governance (partial)
            '4.1', // Portfolio Summary
        ],
        'subscribed' => [
            // All folders from qualified +
            '3.3', '3.4', // Full Legal
            '4.2', '4.3', // Detailed Portfolio
            '5.1', '5.2', '5.3', // Financial Statements
            '6.1', '6.2', // Tax & Regulatory
            '7.1', // Reports
            '8.1', // Investor Relations
        ],
        'internal' => ['*'], // All folders
        'external' => ['*'], // All folders (time-limited)
    ];

    /**
     * Grant Data Room access to investor
     */
    public function grantAccess(
        Investor $investor, 
        string $accessLevel, 
        ?string $reason = null,
        ?Carbon $expiresAt = null
    ): bool {
        // Update investor record
        $investor->update([
            'data_room_access_level' => $accessLevel,
            'data_room_access_granted' => true,
            'data_room_access_granted_at' => Carbon::now(),
            'data_room_access_expires_at' => $expiresAt,
        ]);

        // Get folders for this access level
        $folderNumbers = $this->getFoldersForAccessLevel($accessLevel);

        if ($accessLevel === 'internal' || $accessLevel === 'external') {
            // Grant access to ALL folders
            $folders = DataRoomFolder::all();
        } else {
            // Grant access to specific folders
            $folders = DataRoomFolder::whereIn('folder_number', $folderNumbers)->get();
        }

        // Create permissions for each folder
        foreach ($folders as $folder) {
            DataRoomPermission::updateOrCreate(
                [
                    'investor_id' => $investor->id,
                    'folder_id' => $folder->id,
                ],
                [
                    'can_view' => true,
                    'can_download' => $this->canDownloadForLevel($accessLevel),
                    'can_print' => false, // Default: no printing
                    'can_upload' => false,
                    'can_edit' => false,
                    'can_delete' => false,
                    'granted_by_user_id' => auth()->id(),
                    'granted_at' => Carbon::now(),
                    'expires_at' => $expiresAt,
                    'access_reason' => $reason ?? "Access level: {$accessLevel}",
                    'is_active' => true,
                ]
            );
        }

        // Log activity
        $this->logActivity($investor, null, null, 'permission_granted', [
            'access_level' => $accessLevel,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Upgrade investor's Data Room access
     */
    public function upgradeAccess(Investor $investor, string $newAccessLevel, ?string $reason = null): bool
    {
        $oldLevel = $investor->data_room_access_level;

        // Revoke old permissions
        DataRoomPermission::where('investor_id', $investor->id)
            ->update(['is_active' => false, 'revoked_at' => Carbon::now()]);

        // Grant new access
        $this->grantAccess($investor, $newAccessLevel, $reason ?? "Upgraded from {$oldLevel}");

        return true;
    }

    /**
     * Revoke Data Room access
     */
    public function revokeAccess(Investor $investor, ?string $reason = null): bool
    {
        // Update investor
        $investor->update([
            'data_room_access_level' => 'none',
            'data_room_access_granted' => false,
        ]);

        // Revoke all permissions
        DataRoomPermission::where('investor_id', $investor->id)
            ->update([
                'is_active' => false,
                'revoked_at' => Carbon::now(),
                'revoked_by_user_id' => auth()->id(),
            ]);

        // Log activity
        $this->logActivity($investor, null, null, 'permission_revoked', [
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Check if investor can access specific folder
     */
    public function canAccessFolder(Investor $investor, DataRoomFolder $folder): bool
    {
        // Check if investor has active permission for this folder
        $permission = DataRoomPermission::where('investor_id', $investor->id)
            ->where('folder_id', $folder->id)
            ->where('is_active', true)
            ->first();

        if (!$permission) {
            return false;
        }

        // Check if permission has expired
        if ($permission->expires_at && $permission->expires_at->isPast()) {
            return false;
        }

        return $permission->can_view;
    }

    /**
     * Check if investor can download from folder
     */
    public function canDownloadFromFolder(Investor $investor, DataRoomFolder $folder): bool
    {
        $permission = DataRoomPermission::where('investor_id', $investor->id)
            ->where('folder_id', $folder->id)
            ->where('is_active', true)
            ->first();

        if (!$permission) {
            return false;
        }

        if ($permission->expires_at && $permission->expires_at->isPast()) {
            return false;
        }

        return $permission->can_download;
    }

    /**
     * Log Data Room activity
     */
    public function logActivity(
        ?Investor $investor,
        ?int $documentId,
        ?int $folderId,
        string $action,
        ?array $metadata = null
    ): void {
        DataRoomActivityLog::create([
            'user_id' => auth()->id(),
            'investor_id' => $investor?->id,
            'document_id' => $documentId,
            'folder_id' => $folderId,
            'activity_type' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
            'activity_at' => Carbon::now(),
        ]);
    }

    /**
     * Get folders accessible for access level
     */
    protected function getFoldersForAccessLevel(string $accessLevel): array
    {
        return $this->accessLevelFolders[$accessLevel] ?? [];
    }

    /**
     * Determine if download is allowed for access level
     */
    protected function canDownloadForLevel(string $accessLevel): bool
    {
        return match($accessLevel) {
            'prospect' => false, // View only
            'qualified' => true,
            'subscribed' => true,
            'internal' => true,
            'external' => true,
            default => false,
        };
    }

    /**
     * Get investor's accessible folders
     */
    public function getAccessibleFolders(Investor $investor): array
    {
        $permissions = DataRoomPermission::where('investor_id', $investor->id)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('folder')
            ->get();

        return $permissions->pluck('folder')->toArray();
    }

    /**
     * Track document view
     */
    public function trackDocumentView(Investor $investor, int $documentId): void
    {
        // Increment view count
        $investor->increment('data_room_documents_viewed');
        $investor->update(['data_room_last_login' => Carbon::now()]);

        // Log activity
        $this->logActivity($investor, $documentId, null, 'view');
    }

    /**
     * Track document download
     */
    public function trackDocumentDownload(Investor $investor, int $documentId): void
    {
        // Log activity
        $this->logActivity($investor, $documentId, null, 'download');
    }
}