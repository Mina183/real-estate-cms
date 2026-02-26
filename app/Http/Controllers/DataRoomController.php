<?php

namespace App\Http\Controllers;

use App\Models\DataRoomFolder;
use App\Models\DataRoomDocument;
use App\Exports\DocumentIndexExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DataRoomController extends Controller
{
    /**
     * Display data room index with folder structure
     */
    public function index()
    {
        $folders = DataRoomFolder::whereNull('parent_folder_id')
            ->with(['children.documents', 'documents'])
            ->orderBy('order')
            ->get();

        return view('data-room.index', compact('folders'));
    }

    /**
     * Export document index to Excel
     */
    public function exportIndex()
    {
        // Gate check - can user export data?
        if (!auth()->user()->can('export-data')) {
            abort(403, 'Unauthorized to export data');
        }

        $fileName = 'Document_Index_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new DocumentIndexExport, $fileName);
    }

    /**
     * Download a specific document
     */
    public function download($documentId)
    {
        $document = DataRoomDocument::findOrFail($documentId);

        // Policy check - can user download this document?
        $this->authorize('download', $document);

        app(\App\Services\DataRoomService::class)->logActivity(
            null,
            $document->folder,
            $document,
            'download',
            ['downloaded_by' => auth()->id()]
        );

        if (!Storage::disk('private')->exists($document->file_path)) {
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

        $downloadName = $document->document_name;

        // Dodaj ekstenziju ako je nema
        if ($document->file_type && !str_ends_with(strtolower($downloadName), '.' . $document->file_type)) {
            $downloadName = $downloadName . '.' . $document->file_type;
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $downloadName,
            ['Content-Type' => $mimeType]
        );
    }

    /**
     * Upload a new document
     */
    public function upload(Request $request)
    {
        // Policy check - can user upload documents?
        $this->authorize('upload', DataRoomDocument::class);

        $request->validate([
            'folder_id'     => 'required|exists:data_room_folders,id',
            'investor_id'   => 'nullable|exists:investors,id', 
            'document_name' => 'required|string|max:255',
            'document'      => 'required|file|max:10240',
            'version'       => 'nullable|string',
            'description'   => 'nullable|string',
        ]);

        $file        = $request->file('document');
        $folder      = DataRoomFolder::findOrFail($request->folder_id);
        $storagePath = 'data-room/' . $folder->folder_number;
        $fileName    = $file->getClientOriginalName();
        $filePath    = $file->storeAs($storagePath, $fileName, 'private');

        DataRoomDocument::create([
            'folder_id'     => $request->folder_id,
            'investor_id'   => $request->investor_id,
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
    }
}