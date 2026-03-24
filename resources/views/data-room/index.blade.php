<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-brand-darker flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Secure Data Room</h2>
            </div>
        </div>
    </x-slot>

    <style>
        .dr-sidebar { width: 220px; flex-shrink: 0; }
        .dr-main { flex: 1; min-width: 0; }
        .folder-row {
            display: grid;
            grid-template-columns: 20px 1fr auto auto;
            gap: 0.75rem;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .folder-row:hover { background: #f1f5f9; }
        .folder-row.active { background: #e8f0fe; }
        .subfolder-row {
            display: grid;
            grid-template-columns: 20px 1fr auto auto;
            gap: 0.75rem;
            align-items: center;
            padding: 0.4rem 0.75rem 0.4rem 2rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .subfolder-row:hover { background: #f8fafc; }
        .doc-row {
            display: grid;
            grid-template-columns: 24px 1fr auto auto auto;
            gap: 0.5rem;
            align-items: center;
            padding: 0.5rem 0.75rem 0.5rem 3.5rem;
            border-radius: 6px;
            transition: background 0.15s;
        }
        .doc-row:hover { background: #f0f7ff; }
        .badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
            white-space: nowrap;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .badge-public             { background: #dcfce7; color: #166534; }
        .badge-restricted         { background: #dbeafe; color: #1e40af; }
        .badge-confidential       { background: #ffedd5; color: #9a3412; }
        .badge-highly_confidential{ background: #fee2e2; color: #991b1b; }
        .badge-approved           { background: #dcfce7; color: #166534; }
        .badge-pending            { background: #fef9c3; color: #854d0e; }
        .badge-draft              { background: #f1f5f9; color: #475569; }
        .badge-under_review       { background: #dbeafe; color: #1e40af; }
        .badge-superseded         { background: #f3e8ff; color: #6b21a8; }
        .badge-archived           { background: #f1f5f9; color: #94a3b8; }
        .tab-btn {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            border-bottom: 2px solid transparent;
            white-space: nowrap;
            transition: all 0.15s;
            cursor: pointer;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        .tab-btn:hover { color: #334155; border-bottom-color: #cbd5e1; }
        .tab-btn.active { color: #1e3a5f; border-bottom-color: #1e3a5f; font-weight: 600; }
        .chevron { transition: transform 0.2s; color: #94a3b8; flex-shrink: 0; }
        .chevron.open { transform: rotate(90deg); }
        .collapsible { overflow: hidden; }
        .file-icon { font-size: 15px; flex-shrink: 0; }
        .investor-group-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            cursor: pointer;
        }
        .investor-group-header:hover { background: #f1f5f9; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            background: #1e3a5f;
            color: white;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .download-btn:hover { background: #152d4a; }
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }
        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top Bar --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800">{{ \App\Models\DataRoomDocument::count() }}</div>
                            <div class="text-xs text-gray-500">Total Documents</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-green-50">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800">{{ \App\Models\DataRoomFolder::count() }}</div>
                            <div class="text-xs text-gray-500">Folders</div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button onclick="showReadMe()"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Guide
                    </button>

                    <a href="{{ route('data-room.export-index') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Index
                    </a>

                    @can('upload-documents')
                    <button onclick="showUploadModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm text-white bg-brand-darker rounded-lg hover:opacity-90 transition font-semibold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Document
                    </button>
                    @endcan
                </div>
            </div>

            {{-- Main Container --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 px-4 flex gap-1 overflow-x-auto">
                    <button class="tab-btn active" data-tab="marketing" onclick="switchTab('marketing', this)">
                        📢 Marketing
                    </button>
                    <button class="tab-btn" data-tab="constitutional" onclick="switchTab('constitutional', this)">
                        📋 Fund Documents
                    </button>
                    <button class="tab-btn" data-tab="offering" onclick="switchTab('offering', this)">
                        📄 Subscription
                    </button>
                    <button class="tab-btn" data-tab="reporting" onclick="switchTab('reporting', this)">
                        📊 Reporting
                    </button>
                    <button class="tab-btn" data-tab="investor-specific" onclick="switchTab('investor-specific', this)">
                        🔐 Investor Documents
                    </button>
                </div>

                <div class="p-4">

                    {{-- MARKETING TAB — Folder 1 --}}
                    <div id="tab-marketing" class="tab-pane">
                        @foreach($folders->where('folder_number', '1') as $folder)
                            @include('data-room.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                        @if($folders->where('folder_number', '1')->isEmpty())
                            <div class="empty-state">No folders in this section yet.</div>
                        @endif
                    </div>

                    {{-- FUND DOCUMENTS TAB — Folder 2 --}}
                    <div id="tab-constitutional" class="tab-pane hidden">
                        @foreach($folders->where('folder_number', '2') as $folder)
                            @include('data-room.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                        @if($folders->where('folder_number', '2')->isEmpty())
                            <div class="empty-state">No folders in this section yet.</div>
                        @endif
                    </div>

                    {{-- SUBSCRIPTION TAB — Folder 3 --}}
                    <div id="tab-offering" class="tab-pane hidden">
                        @foreach($folders->where('folder_number', '3') as $folder)
                            @include('data-room.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                        @if($folders->where('folder_number', '3')->isEmpty())
                            <div class="empty-state">No folders in this section yet.</div>
                        @endif
                    </div>

                    {{-- REPORTING TAB — Folder 4 --}}
                    <div id="tab-reporting" class="tab-pane hidden">
                        @foreach($folders->where('folder_number', '4') as $folder)
                            @include('data-room.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                        @if($folders->where('folder_number', '4')->isEmpty())
                            <div class="empty-state">No folders in this section yet.</div>
                        @endif
                    </div>

                    {{-- INVESTOR DOCUMENTS TAB --}}
                    <div id="tab-investor-specific" class="tab-pane hidden">
                        <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg mb-4">
                            <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <p class="text-sm text-amber-800">
                                <strong>Investor Documents:</strong> Private folders created automatically for each investor. Only visible to the assigned investor and staff.
                            </p>
                        </div>

                        @forelse($investorFolders as $investorFolder)
                            <div class="mb-3">
                                <div class="investor-group-header" onclick="toggleInvestorGroup({{ $investorFolder->id }})">
                                    <svg class="w-4 h-4 text-gray-500 chevron" id="inv-chevron-{{ $investorFolder->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <div class="w-7 h-7 rounded-full bg-brand-darker flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($investorFolder->folder_name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-sm text-gray-800">{{ $investorFolder->folder_name }}</span>
                                    <span class="ml-auto text-xs text-gray-500">{{ $investorFolder->children->count() }} folders</span>
                                </div>

                                <div id="inv-group-{{ $investorFolder->id }}" class="collapsible hidden pl-4 mt-1 space-y-1">
                                    @foreach($investorFolder->children as $subFolder)
                                        <div class="border border-gray-100 rounded-lg">
                                            <div class="subfolder-row" onclick="toggleFolder('sub-{{ $subFolder->id }}')">
                                                <svg class="w-3.5 h-3.5 chevron" id="chevron-sub-{{ $subFolder->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                                <span class="text-sm text-gray-700 font-medium">📁 {{ $subFolder->folder_name }}</span>
                                                <span class="text-xs text-gray-400">{{ $subFolder->documents->count() }} docs</span>
                                            </div>

                                            <div id="folder-sub-{{ $subFolder->id }}" class="hidden">
                                                @forelse($subFolder->documents as $doc)
                                                    <div class="doc-row">
                                                        <span class="file-icon">
                                                            @if($doc->file_type === 'pdf') 📄
                                                            @elseif(in_array($doc->file_type, ['xlsx','xls'])) 📊
                                                            @elseif(in_array($doc->file_type, ['pptx','ppt'])) 📽️
                                                            @elseif(in_array($doc->file_type, ['docx','doc'])) 📝
                                                            @else 📎
                                                            @endif
                                                        </span>
                                                        <span class="text-sm text-gray-700 truncate">{{ $doc->document_name }}</span>
                                                        <span class="text-xs text-gray-400">v{{ $doc->version }}</span>
                                                        <span class="badge badge-{{ $doc->status === 'approved' ? 'approved' : ($doc->status === 'pending_review' ? 'pending' : 'draft') }}">
                                                            {{ $doc->status === 'pending_review' ? 'Pending' : ucfirst($doc->status) }}
                                                        </span>
                                                        <a href="{{ route('data-room.download', $doc->id) }}" class="download-btn">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            Download
                                                        </a>
                                                    </div>
                                                @empty
                                                    <p class="text-xs text-gray-400 px-4 py-2">No documents yet.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <p class="text-sm font-medium text-gray-400">No investor folders yet</p>
                                <p class="text-xs text-gray-400 mt-1">Folders are created automatically when an investor profile is added.</p>
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- Footer Legend --}}
                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 flex items-center gap-4 text-xs text-gray-500">
                    <span class="font-medium text-gray-400 uppercase tracking-wide text-[10px]">Access Levels:</span>
                    <span class="badge badge-public">Public</span>
                    <span class="badge badge-restricted">Restricted</span>
                    <span class="badge badge-confidential">Confidential</span>
                    <span class="badge badge-highly_confidential">Highly Confidential</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Upload Document</h3>
                </div>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-5">
                @if(session('upload_success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ session('upload_success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('data-room.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Folder <span class="text-red-500">*</span>
                        </label>
                        <select name="folder_id" id="folder_id" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">— Select folder —</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}" data-folder-number="{{ $folder->folder_number }}">
                                    {{ $folder->folder_number }} · {{ $folder->folder_name }}
                                </option>
                                @foreach($folder->children as $child)
                                    <option value="{{ $child->id }}" data-folder-number="{{ $child->folder_number }}">
                                        &nbsp;&nbsp;&nbsp;└ {{ $child->folder_number }} · {{ $child->folder_name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    {{-- Investor selection — shown only for Folder 5 --}}
                    <div id="investor-selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Assign to Investor
                            <span class="text-xs text-gray-400 font-normal ml-1">(Folder 5 — Investor Personal Documents)</span>
                        </label>
                        <select name="investor_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Available to all —</option>
                            @foreach(\App\Models\Investor::orderBy('organization_name')->get() as $investor)
                                <option value="{{ $investor->id }}">{{ $investor->organization_name ?? $investor->legal_entity_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Document Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="document_name" required
                               placeholder="e.g., Q4 2025 NAV Report"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="document" required
                               accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx,.ppt"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700">
                        <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, PowerPoint · Max 10MB</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Version</label>
                            <input type="text" name="version" value="1.0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                                  placeholder="Brief description..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeUploadModal()"
                                class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-sm text-white bg-brand-darker rounded-lg hover:opacity-90 transition font-semibold shadow-sm">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Read Me Modal --}}
    <div id="readMeModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">How to Use This Data Room</h3>
                <button onclick="closeReadMe()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4 text-sm text-gray-600">
                <p><strong class="text-gray-800">Purpose:</strong> Centralised repository for all fund-related documents, organised by category.</p>
                <div>
                    <p class="font-medium text-gray-800 mb-2">Folders:</p>
                    <ul class="space-y-1 text-gray-500">
                        <li>📢 <strong>Marketing</strong> — Teaser, deck, term sheet, investment thesis</li>
                        <li>📋 <strong>Fund Documents</strong> — Constitutional docs, COI, AOA</li>
                        <li>📄 <strong>Subscription</strong> — PPM, sub docs, KYC/AML templates</li>
                        <li>📊 <strong>Reporting</strong> — Quarterly NAV reports, newsletters</li>
                        <li>🔐 <strong>Investor-Specific</strong> — Personal documents per investor</li>
                    </ul>
                </div>
                <div>
                    <p class="font-medium text-gray-800 mb-2">Navigation:</p>
                    <ul class="space-y-1 text-gray-500">
                        <li>• Use tabs to browse by category</li>
                        <li>• Click folder arrows to expand/collapse</li>
                        <li>• Upload button adds documents to selected folder</li>
                        <li>• Investor-specific docs require investor assignment</li>
                    </ul>
                </div>
            </div>
            <div class="px-5 pb-5">
                <button onclick="closeReadMe()"
                        class="w-full py-2 text-sm font-medium text-white bg-brand-darker rounded-lg hover:opacity-90 transition">
                    Got it
                </button>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Request Revision</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="rejectForm" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reason for revision *</label>
                    <textarea name="rejection_reason" rows="3" required
                              placeholder="Explain what needs to be changed..."
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 font-semibold">
                        Request Revision
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.remove('hidden');
        if (btn) btn.classList.add('active');
        else document.querySelector(`[data-tab="${name}"]`)?.classList.add('active');
    }

    function toggleFolder(id) {
        const content = document.getElementById('folder-' + id);
        const chevron = document.getElementById('chevron-' + id);
        if (content) {
            content.classList.toggle('hidden');
            chevron?.classList.toggle('open');
        }
    }

    function toggleInvestorGroup(id) {
        const content = document.getElementById('inv-group-' + id);
        const chevron = document.getElementById('inv-chevron-' + id);
        if (content) {
            content.classList.toggle('hidden');
            chevron?.classList.toggle('open');
        }
    }

    function showUploadModal() { document.getElementById('uploadModal').classList.remove('hidden'); }
    function closeUploadModal() { document.getElementById('uploadModal').classList.add('hidden'); }

    // Show investor field only for Folder 5
    document.getElementById('folder_id')?.addEventListener('change', function() {
        const num = this.options[this.selectedIndex]?.getAttribute('data-folder-number') || '';
        document.getElementById('investor-selection').classList.toggle('hidden', num !== '5');
    });

    function showReadMe() { document.getElementById('readMeModal').classList.remove('hidden'); }
    function closeReadMe() { document.getElementById('readMeModal').classList.add('hidden'); }

    function showRejectModal(docId) {
        document.getElementById('rejectForm').action = '/data-room/documents/' + docId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    ['uploadModal', 'readMeModal', 'rejectModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    @if($errors->any() || session('upload_success'))
        showUploadModal();
    @endif
    </script>

</x-app-layout>