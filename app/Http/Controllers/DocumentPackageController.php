<?php

namespace App\Http\Controllers;

use App\Models\DocumentPackage;
use App\Models\DataRoomDocument;
use Illuminate\Http\Request;

class DocumentPackageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DocumentPackage::class, 'documentPackage');
    }

    public function index()
    {
        $packages = DocumentPackage::with(['createdBy'])
            ->withCount(['items', 'accessLinks'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('document-packages.index', compact('packages'));
    }

    public function create()
    {
        $documents = DataRoomDocument::where('status', 'approved')
            ->with('folder')
            ->orderBy('document_name')
            ->get();

        return view('document-packages.create', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'document_ids'   => 'required|array|min:1',
            'document_ids.*' => 'exists:data_room_documents,id',
        ]);

        $package = DocumentPackage::create([
            'name'                => $validated['name'],
            'description'         => $validated['description'] ?? null,
            'created_by_user_id'  => auth()->id(),
        ]);

        foreach ($validated['document_ids'] as $docId) {
            $package->items()->create(['data_room_document_id' => $docId]);
        }

        return redirect()->route('document-packages.show', $package)
            ->with('success', 'Document package created successfully.');
    }

    public function show(DocumentPackage $documentPackage)
    {
        $documentPackage->load(['items.document.folder', 'accessLinks.investor', 'accessLinks.accessRequests', 'createdBy']);

        return view('document-packages.show', compact('documentPackage'));
    }

    public function edit(DocumentPackage $documentPackage)
    {
        $documents = DataRoomDocument::where('status', 'approved')
            ->with('folder')
            ->orderBy('document_name')
            ->get();

        $documentPackage->load('items');
        $selectedIds = $documentPackage->items->pluck('data_room_document_id')->toArray();

        return view('document-packages.edit', compact('documentPackage', 'documents', 'selectedIds'));
    }

    public function update(Request $request, DocumentPackage $documentPackage)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'document_ids'   => 'required|array|min:1',
            'document_ids.*' => 'exists:data_room_documents,id',
        ]);

        $documentPackage->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $documentPackage->items()->delete();
        foreach ($validated['document_ids'] as $docId) {
            $documentPackage->items()->create(['data_room_document_id' => $docId]);
        }

        return redirect()->route('document-packages.show', $documentPackage)
            ->with('success', 'Document package updated successfully.');
    }

    public function destroy(DocumentPackage $documentPackage)
    {
        $documentPackage->delete();

        return redirect()->route('document-packages.index')
            ->with('success', 'Document package deleted.');
    }
}
