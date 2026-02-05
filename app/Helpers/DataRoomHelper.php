<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\DataRoomFolder;
use App\Models\DataRoomDocument;

class DataRoomHelper
{
    /**
     * Check if user can access a specific folder based on security level
     */
    public static function canAccessFolder(User $user, DataRoomFolder $folder): bool
    {
        // Superadmin has access to everything
        if ($user->role === 'superadmin') {
            return true;
        }
        
        // Check for folder-level exceptions (e.g., Section 12)
        if (self::hasFolderException($user, $folder)) {
            return self::checkFolderException($user, $folder);
        }
        
        // Get allowed security levels for this user's role
        $allowedLevels = config('dataroom.access_matrix.' . $user->role, []);
        
        // Check if folder's security level is in allowed list
        return in_array($folder->access_level, $allowedLevels);
    }
    
    /**
     * Check if user can access a specific document
     */
    public static function canAccessDocument(User $user, DataRoomDocument $document): bool
    {
        // Document inherits security from its folder
        return self::canAccessFolder($user, $document->folder);
    }
    
    /**
     * Check if user can download a document (may have additional restrictions)
     */
    public static function canDownloadDocument(User $user, DataRoomDocument $document): bool
    {
        // First check if they can access it at all
        if (!self::canAccessDocument($user, $document)) {
            return false;
        }
        
        // Check view-only restrictions
        $viewOnlyRoles = config('dataroom.download_restrictions.view_only_roles', []);
        if (in_array($user->role, $viewOnlyRoles)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if user can upload documents to data room
     */
    public static function canUploadDocuments(User $user): bool
    {
        $uploadRoles = config('dataroom.upload_permissions', []);
        return in_array($user->role, $uploadRoles);
    }
    
    /**
     * Get all folders accessible by user
     */
    public static function getAccessibleFolders(User $user)
    {
        if ($user->role === 'superadmin') {
            return DataRoomFolder::all();
        }
        
        $allowedLevels = config('dataroom.access_matrix.' . $user->role, []);
        
        return DataRoomFolder::whereIn('access_level', $allowedLevels)->get();
    }
    
    /**
     * Get all documents accessible by user
     */
    public static function getAccessibleDocuments(User $user)
    {
        $accessibleFolders = self::getAccessibleFolders($user);
        $folderIds = $accessibleFolders->pluck('id');
        
        return DataRoomDocument::whereIn('folder_id', $folderIds)->get();
    }
    
    /**
     * Check if folder has special exception rules
     */
    protected static function hasFolderException(User $user, DataRoomFolder $folder): bool
    {
        $exceptions = config('dataroom.folder_exceptions', []);
        
        // Check if this folder (or its parent) has exceptions
        return isset($exceptions[$folder->id]) || 
               ($folder->parent_folder_id && isset($exceptions[$folder->parent_folder_id]));
    }
    
    /**
     * Check folder exception rules
     */
    protected static function checkFolderException(User $user, DataRoomFolder $folder): bool
    {
        $exceptions = config('dataroom.folder_exceptions', []);
        $folderId = $folder->id;
        
        // Check parent folder if current folder doesn't have exception
        if (!isset($exceptions[$folderId]) && $folder->parent_folder_id) {
            $folderId = $folder->parent_folder_id;
        }
        
        if (!isset($exceptions[$folderId])) {
            return false;
        }
        
        $exception = $exceptions[$folderId];
        
        // Check if user's role is in allowed roles
        if (isset($exception['allowed_roles']) && 
            in_array($user->role, $exception['allowed_roles'])) {
            return true;
        }
        
        // Check if requires investor match (Section 12 - Investor-Specific)
        if (isset($exception['requires_investor_match']) && 
            $exception['requires_investor_match'] === true) {
            // User must be linked to an investor
            return $user->investor_id !== null;
        }
        
        return false;
    }
    
    /**
     * Get security level badge HTML class
     */
    public static function getSecurityBadgeClass(string $securityLevel): string
    {
        return match($securityLevel) {
            'public' => 'bg-green-100 text-green-800',
            'restricted' => 'bg-blue-100 text-blue-800',
            'confidential' => 'bg-orange-100 text-orange-800',
            'highly_confidential' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    
    /**
     * Get security level emoji
     */
    public static function getSecurityEmoji(string $securityLevel): string
    {
        return match($securityLevel) {
            'public' => '🟢',
            'restricted' => '🔵',
            'confidential' => '🟠',
            'highly_confidential' => '🔴',
            default => '⚪',
        };
    }
    
    /**
     * Get human-readable security level name
     */
    public static function getSecurityLevelName(string $securityLevel): string
    {
        return match($securityLevel) {
            'public' => 'Public',
            'restricted' => 'Restricted',
            'confidential' => 'Confidential',
            'highly_confidential' => 'Highly Confidential',
            default => ucfirst(str_replace('_', ' ', $securityLevel)),
        };
    }
}
