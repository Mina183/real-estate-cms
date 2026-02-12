<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\CapitalCallController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\PaymentTransactionController;
use App\Exports\DocumentIndexExport;
use App\Models\DataRoomDocument;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Approval pending (placeholder - za invite sistem u Phase 7)
    Route::get('/approval-pending', function () {
        return view('auth.approval-pending');
    })->name('approval.pending');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Investor Management
    |--------------------------------------------------------------------------
    */
    Route::resource('investors', InvestorController::class);

    Route::get('investors/{investor}/change-stage',
        [InvestorController::class, 'changeStageForm'])
        ->name('investors.change-stage.form');

    Route::post('investors/{investor}/change-stage',
        [InvestorController::class, 'changeStage'])
        ->name('investors.change-stage');

    Route::get('investors/{investor}/activity',
        [InvestorController::class, 'activityLog'])
        ->name('investors.activity');

    /*
    |--------------------------------------------------------------------------
    | Capital Calls
    |--------------------------------------------------------------------------
    */
    Route::resource('capital-calls', CapitalCallController::class);

    Route::post('capital-calls/{capitalCall}/issue',
        [CapitalCallController::class, 'issue'])
        ->name('capital-calls.issue');

    /*
    |--------------------------------------------------------------------------
    | Distributions
    |--------------------------------------------------------------------------
    */
    Route::resource('distributions', DistributionController::class);

    Route::post('distributions/{distribution}/approve',
        [DistributionController::class, 'approve'])
        ->name('distributions.approve');

    Route::post('distributions/{distribution}/process',
        [DistributionController::class, 'process'])
        ->name('distributions.process');

    /*
    |--------------------------------------------------------------------------
    | Payment Transactions
    |--------------------------------------------------------------------------
    */
    Route::post('payments/{payment}/mark-paid',
        [PaymentTransactionController::class, 'markAsPaid'])
        ->name('payments.mark-paid');

    Route::post('payments/{payment}/mark-failed',
        [PaymentTransactionController::class, 'markAsFailed'])
        ->name('payments.mark-failed');

    Route::post('payments/{payment}/reverse',
        [PaymentTransactionController::class, 'reverse'])
        ->name('payments.reverse');

    Route::put('payments/{payment}',
        [PaymentTransactionController::class, 'update'])
        ->name('payments.update');

    Route::delete('payments/{payment}',
        [PaymentTransactionController::class, 'destroy'])
        ->name('payments.destroy');

    Route::post('payments/bulk/mark-paid',
        [PaymentTransactionController::class, 'bulkMarkAsPaid'])
        ->name('payments.bulk-mark-paid');

    /*
    |--------------------------------------------------------------------------
    | Data Room
    |--------------------------------------------------------------------------
    */
    Route::get('/data-room', function () {
        $folders = \App\Models\DataRoomFolder::whereNull('parent_folder_id')
            ->with(['children.documents', 'documents'])
            ->orderBy('order')
            ->get();
        return view('data-room.index', compact('folders'));
    })->name('data-room.index');

    Route::get('/data-room/export-index', function () {
        // Gate check - can user export data?
        if (!auth()->user()->can('export-data')) {
            abort(403, 'Unauthorized to export data');
        }
        
        $fileName = 'Document_Index_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new DocumentIndexExport, $fileName);
    })->name('data-room.export-index');

    Route::get('/data-room/download/{document}', function ($documentId) {
        $document = DataRoomDocument::findOrFail($documentId);
        
        // Policy check - can user download this document?
        if (!auth()->user()->can('download', $document)) {
            abort(403, 'Unauthorized to download this document');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        $mimeType = $mimeTypes[$document->file_type] ?? 'application/octet-stream';

        return Storage::disk('public')->download(
            $document->file_path,
            $document->document_name,
            ['Content-Type' => $mimeType]
        );
    })->name('data-room.download');

  Route::post('/data-room/upload', function (Request $request) {
    // Policy check - can user upload documents?
    if (!auth()->user()->can('upload', \App\Models\DataRoomDocument::class)) {
        abort(403, 'Unauthorized to upload documents');
    }
        $request->validate([
            'folder_id'     => 'required|exists:data_room_folders,id',
            'document_name' => 'required|string|max:255',
            'document'      => 'required|file|max:10240',
            'version'       => 'nullable|string',
            'description'   => 'nullable|string',
        ]);

        $file    = $request->file('document');
        $folder  = \App\Models\DataRoomFolder::findOrFail($request->folder_id);
        $storagePath = 'data-room/' . $folder->folder_number;
        $fileName    = $file->getClientOriginalName();
        $filePath    = $file->storeAs($storagePath, $fileName, 'public');

        DataRoomDocument::create([
            'folder_id'     => $request->folder_id,
            'document_name' => $request->document_name,
            'file_path'     => $filePath,
            'file_type'     => $file->getClientOriginalExtension(),
            'file_size'     => $file->getSize(),
            'version'       => $request->version ?? '1.0',
            'description'   => $request->description,
            'status'        => 'approved',
            'uploaded_by'   => auth()->id(),
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
        ]);

        return redirect()->route('data-room.index')
            ->with('upload_success', 'Document uploaded: ' . $request->document_name);
    })->name('data-room.upload');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| TEMPORARY - Remove before production
|--------------------------------------------------------------------------
*/
Route::get('/test-policies', function () {
    if (!auth()->check()) {
        return 'Please login first';
    }

    $user  = auth()->user();
    $tests = [
        'Current User' => $user->email,
        'Current Role' => $user->role,
        '---'          => '---',
        'Can view investors?'       => $user->can('viewAny', App\Models\Investor::class) ? '✅ YES' : '❌ NO',
        'Can create investor?'      => $user->can('create', App\Models\Investor::class) ? '✅ YES' : '❌ NO',
        'Can create capital call?'  => $user->can('create', App\Models\CapitalCall::class) ? '✅ YES' : '❌ NO',
        'Can create distribution?'  => $user->can('create', App\Models\Distribution::class) ? '✅ YES' : '❌ NO',
    ];

    $investor = App\Models\Investor::first();
    if ($investor) {
        $tests['Can update investor #' . $investor->id . '?'] = $user->can('update', $investor) ? '✅ YES' : '❌ NO';
        $tests['Can delete investor #' . $investor->id . '?'] = $user->can('delete', $investor) ? '✅ YES' : '❌ NO';
    }

    $capitalCall = App\Models\CapitalCall::first();
    if ($capitalCall) {
        $tests['Can issue capital call #' . $capitalCall->id . '?'] = $user->can('issue', $capitalCall) ? '✅ YES' : '❌ NO';
    }

    $output  = '<h1>Policy Tests</h1>';
    $output .= '<table border="1" cellpadding="10" style="border-collapse:collapse;">';
    foreach ($tests as $test => $result) {
        $output .= '<tr><td><strong>' . $test . '</strong></td><td>' . $result . '</td></tr>';
    }
    $output .= '</table>';

    return $output;
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';