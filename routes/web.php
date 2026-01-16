<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\PartnerDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use App\Exports\DocumentIndexExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DataRoomDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\InvestorController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/send-test-email', function () {
    Mail::to('your-email@example.com')->send(new TestEmail());
    return 'Test email sent!';
});

Route::get('/demo', function () {
    return view('demo');
})->name('demo.video');

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Show pending approval view for guests
    Route::get('/approval-pending', function () {
        return view('auth.approval-pending');
    })->name('approval.pending');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{client}/documents', [ClientController::class, 'storeDocument'])->name('clients.documents.store');
    Route::post('/clients/{client}/communications', [ClientController::class, 'storeCommunication'])->name('clients.communications.store');

    Route::get('/data-room', function() {
    $folders = \App\Models\DataRoomFolder::whereNull('parent_folder_id')
                ->with(['children.documents', 'documents'])
                ->orderBy('order')
                ->get();
    return view('data-room.index', compact('folders'));
})->middleware('auth')->name('data-room.index');

// Document Index Download Route
Route::get('/data-room/export-index', function() {
    $fileName = 'Document_Index_' . date('Y-m-d') . '.xlsx';
    return Excel::download(new DocumentIndexExport, $fileName);
})->middleware('auth')->name('data-room.export-index');

// Test upload route (temporary - for testing Bradley Cooper 😂)
Route::post('/data-room/test-upload', function(Request $request) {
    $request->validate([
        'folder_id' => 'required|exists:data_room_folders,id',
        'document_name' => 'required|string|max:255',
        'document' => 'required|file|max:10240', // 10MB max
        'version' => 'nullable|string',
        'description' => 'nullable|string',
    ]);

    // Store file
    $file = $request->file('document');
    $folder = \App\Models\DataRoomFolder::findOrFail($request->folder_id);
    
    // Create storage path based on folder number
    $storagePath = 'data-room/' . $folder->folder_number;
    
    // Store file with original name
    $fileName = $file->getClientOriginalName();
    $filePath = $file->storeAs($storagePath, $fileName, 'public');
    
    // Create database record
    DataRoomDocument::create([
        'folder_id' => $request->folder_id,
        'document_name' => $request->document_name,
        'file_path' => $filePath,
        'file_type' => $file->getClientOriginalExtension(),
        'file_size' => $file->getSize(),
        'version' => $request->version ?? '1.0',
        'description' => $request->description,
        'status' => 'approved',
        'uploaded_by' => auth()->id(),
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return redirect()->route('data-room.index')
        ->with('upload_success', 'Document uploaded successfully: ' . $request->document_name);
    
})->middleware('auth')->name('data-room.test-upload');


// Download route
Route::get('/data-room/download/{document}', function($documentId) {
    $document = DataRoomDocument::findOrFail($documentId);
    
    if (!Storage::disk('public')->exists($document->file_path)) {
        abort(404, 'File not found');
    }
    
    // Get proper MIME type based on file extension
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];
    
    $mimeType = $mimeTypes[$document->file_type] ?? 'application/octet-stream';
    
    return Storage::disk('public')->download(
        $document->file_path, 
        $document->document_name,
        ['Content-Type' => $mimeType]
    );
})->middleware('auth')->name('data-room.download');

    /*
    |--------------------------------------------------------------------------
    | Admin Routs (Optional)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,superadmin')
    ->prefix('admin')
    ->group(function () {
        Route::get('/users', [ApprovalController::class, 'index'])->name('approve_users');
        Route::patch('/users/{user}/approve', [ApprovalController::class, 'approve'])->name('approve_user');
        Route::get('/clients', [AdminClientController::class, 'index'])->name('admin.clients.index');
        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
        Route::get('/documents/create', [AdminDocumentController::class, 'create'])->name('admin.documents.create');
        Route::post('/documents', [AdminDocumentController::class, 'store'])->name('admin.documents.store');
        Route::patch('/documents/{id}/approve', [AdminDocumentController::class, 'approve'])->name('admin.documents.approve');
        Route::delete('/documents/{id}', [AdminDocumentController::class, 'destroy'])->name('admin.documents.destroy');
        Route::patch('/responses/{id}/approve', [AdminDocumentController::class, 'approveResponse'])->name('admin.responses.approve');
        Route::get('documents/{id}', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
        Route::resource('meetings', CalendarController::class)->except(['index', 'show']);

        // Meeting proposal management routes
        Route::get('/meeting-proposals', [CalendarController::class, 'proposals'])->name('admin.meeting.proposals');
        Route::post('/meetings/{meeting}/approve', [CalendarController::class, 'approveProposal'])->name('meetings.approve');
        Route::delete('/meetings/{meeting}/reject', [CalendarController::class, 'rejectProposal'])->name('meetings.reject');

        Route::get('/export/clients', [AdminClientController::class, 'exportClients'])->name('admin.export.clients');
        Route::get('/export/partners', [AdminClientController::class, 'exportPartners'])->name('admin.export.partners');
    });

        /*
    |--------------------------------------------------------------------------
    | Channel Partner Routs (Optional)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:channel_partner')
    ->prefix('partner') // ✅ NEW PREFIX
    ->group(function () {
        Route::resource('lead-sources', \App\Http\Controllers\LeadSourceController::class);
        Route::get('/lead-sources', [LeadSourceController::class, 'index'])->name('lead-sources.index');
        Route::post('/lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
        Route::get('/documents', [PartnerDocumentController::class, 'index'])->name('partner.documents.index');
        Route::post('/documents/{id}/upload-response', [PartnerDocumentController::class, 'uploadResponse'])->name('partner.documents.uploadResponse');
        Route::post('/documents/{id}/acknowledge', [PartnerDocumentController::class, 'acknowledge'])->name('partner.documents.acknowledge');
        Route::post('/meetings/{meeting}/respond', [CalendarController::class, 'respond'])->name('meetings.respond');
        Route::get('/clients/{client}/communications/{communication}/edit', [ClientController::class, 'editCommunication'])->name('clients.communications.edit');
        Route::put('/clients/{client}/communications/{communication}', [ClientController::class, 'updateCommunication'])->name('clients.communications.update');

        // Meeting proposal routes
        Route::get('/meetings/propose', [CalendarController::class, 'createProposal'])->name('meetings.create.proposal');
        Route::post('/meetings/propose', [CalendarController::class, 'storeProposal'])->name('meetings.store.proposal');

        Route::get('/export/clients', [ClientController::class, 'exportMyClients'])->name('partner.export.clients');
    });
    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

      /*
    |-------------------------------------------------------------------------- 
    | Calendar Routes (Shared)
    |-------------------------------------------------------------------------- 
    */
    Route::get('/calendar/fetch', [\App\Http\Controllers\CalendarController::class, 'fetchMeetings']);
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
});

// Optional standalone route (not protected)
    Route::get('/test-role', function () {
        return 'Role middleware works!';
    })->middleware('role:admin,superadmin');

/*
|--------------------------------------------------------------------------
| Investor Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::resource('investors', App\Http\Controllers\InvestorController::class);
    // Stage management routes
    Route::get('investors/{investor}/change-stage', [App\Http\Controllers\InvestorController::class, 'changeStageForm'])->name('investors.change-stage.form');
    Route::post('investors/{investor}/change-stage', [App\Http\Controllers\InvestorController::class, 'changeStage'])->name('investors.change-stage');

    // Activity log route
    Route::get('investors/{investor}/activity', [App\Http\Controllers\InvestorController::class, 'activityLog'])->name('investors.activity');
});

    /*
    |--------------------------------------------------------------------------
    | Auth Routes (Laravel Breeze or Fortify)
    |--------------------------------------------------------------------------
    */
    require __DIR__.'/auth.php';

