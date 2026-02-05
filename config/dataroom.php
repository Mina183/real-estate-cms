<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Data Room Access Matrix
    |--------------------------------------------------------------------------
    |
    | This matrix defines which security levels each role can access.
    | Security Levels: public, restricted, confidential, highly_confidential
    |
    */
    
    'access_matrix' => [
        
        // INTERNAL STAFF
        'superadmin' => [
            'public',
            'restricted',
            'confidential',
            'highly_confidential',
        ],
        
        'admin' => [
            'public',
            'restricted',
            'confidential',
            'highly_confidential',
        ],
        
        'operations' => [
            'public',
            'restricted',
            // Cannot see: confidential, highly_confidential
        ],
        
        'compliance_officer' => [
            'public',
            'restricted',
            'confidential',
            // Cannot see: highly_confidential (board docs, legal agreements)
        ],
        
        'relationship_manager' => [
            'public',
            'restricted',
            // Cannot see: confidential, highly_confidential
        ],
        
        'data_room_administrator' => [
            'public',
            'restricted',
            'confidential',
            // Cannot see: highly_confidential
        ],
        
        'document_owner' => [
            'public',
            'restricted',
            'confidential',
            // Access depends on which documents they own
        ],
        
        'internal_director' => [
            'public',
            'restricted',
            'confidential',
            'highly_confidential',
        ],
        
        // EXTERNAL
        'external_counsel' => [
            'public',
            'restricted',
            'confidential',
            // Time-limited access, cannot see highly_confidential
        ],
        
        'auditor' => [
            'public',
            'restricted',
            'confidential',
            'highly_confidential',
            // Read-only access to everything
        ],
        
        // INVESTORS (Phase 5 - Portal)
        'investor_subscribed' => [
            'public',
            'restricted', // Limited to specific folders
            // Cannot see: confidential, highly_confidential
        ],
        
        'investor_qualified' => [
            'public',
            // Limited restricted access (PPM only)
        ],
        
        'investor_prospect' => [
            'public', // Very limited - teaser docs only
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Folder-Level Exceptions
    |--------------------------------------------------------------------------
    |
    | Some folders require special permissions beyond security levels.
    | For example, Section 12 (Investor-Specific) is restricted per investor.
    |
    */
    
    'folder_exceptions' => [
        // Section 12: Investor-Specific Documents
        // Only accessible by: superadmin, admin, and the specific investor
        12 => [
            'allowed_roles' => ['superadmin', 'admin'],
            'requires_investor_match' => true,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Upload Permissions
    |--------------------------------------------------------------------------
    |
    | Defines which roles can upload documents to the Data Room.
    |
    */
    
    'upload_permissions' => [
        'superadmin',
        'admin',
        'data_room_administrator',
        'document_owner',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Download Restrictions
    |--------------------------------------------------------------------------
    |
    | Additional download restrictions beyond view access.
    |
    */
    
    'download_restrictions' => [
        // Roles that can only view but not download
        'view_only_roles' => [
            // 'auditor', // Uncomment if auditors should only view
        ],
        
        // Document types that require special approval to download
        'restricted_file_types' => [
            // 'highly_confidential' => ['superadmin', 'admin'],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Activity Logging
    |--------------------------------------------------------------------------
    |
    | Defines which actions should be logged.
    |
    */
    
    'log_actions' => [
        'view_document',
        'download_document',
        'upload_document',
        'delete_document',
        'share_document',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Security Alerts
    |--------------------------------------------------------------------------
    |
    | Suspicious activity thresholds that trigger alerts.
    |
    */
    
    'alerts' => [
        'max_downloads_per_hour' => 50,
        'max_failed_access_attempts' => 5,
        'suspicious_download_patterns' => true,
    ],
];
