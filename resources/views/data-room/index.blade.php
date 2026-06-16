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
            grid-template-columns: 24px 1fr auto auto auto auto;
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
        @media (max-width: 639px) {
            .folder-row    { grid-template-columns: 20px 1fr auto; }
            .folder-row    > :last-child { display: none; }
            .subfolder-row { grid-template-columns: 20px 1fr auto; }
            .doc-row       { grid-template-columns: 20px 1fr auto; padding-left: 2rem; }
            .doc-row       > :nth-child(3),
            .doc-row       > :nth-child(4) { display: none; }
        }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top Bar --}}
            @php
                $typeCounts = \App\Models\DataRoomDocument::selectRaw('file_type, count(*) as cnt')
                    ->groupBy('file_type')
                    ->pluck('cnt', 'file_type');
                $pdfCount   = (int) $typeCounts->get('pdf', 0);
                $wordCount  = (int) $typeCounts->get('doc', 0) + (int) $typeCounts->get('docx', 0);
                $excelCount = (int) $typeCounts->get('xls', 0) + (int) $typeCounts->get('xlsx', 0);
                $emlCount   = (int) $typeCounts->get('eml', 0);
                $totalCount = (int) $typeCounts->sum();
                $otherCount = $totalCount - $pdfCount - $wordCount - $excelCount - $emlCount;
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div class="flex items-start gap-3 flex-wrap">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800">{{ $totalCount }}</div>
                            <div class="text-xs text-gray-500 mb-1.5">Total Documents</div>
                            <div class="flex flex-wrap gap-1">
                                @if($pdfCount)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700">📄 PDF {{ $pdfCount }}</span>
                                @endif
                                @if($wordCount)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">📝 Word {{ $wordCount }}</span>
                                @endif
                                @if($excelCount)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">📊 Excel {{ $excelCount }}</span>
                                @endif
                                @if($emlCount)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-700">✉️ Comm. Log {{ $emlCount }}</span>
                                @endif
                                @if($otherCount > 0)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">📎 Other {{ $otherCount }}</span>
                                @endif
                            </div>
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

                <div class="flex flex-wrap gap-2">
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

                    @can('upload', App\Models\DataRoomDocument::class)
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

                {{-- Tab Navigation — dynamic from DB --}}
                @php
                    $tabIcons = [
                        '1'  => '📢', '2'  => '📋', '3'  => '👥', '4'  => '🛡️',
                        '5'  => '🔍', '6'  => '📈', '7'  => '🤝', '8'  => '💧',
                        '9'  => '📄', '10' => '📊', '11' => '⚙️', '12' => '🎯', '13' => '🔐',
                    ];
                    $tabLabels = [
                        '1'  => 'Marketing',   '2'  => 'Fund Legal',   '3'  => 'Governance',
                        '4'  => 'Compliance',  '5'  => 'Research & Evidence',     '6'  => 'Fin. Models',
                        '7'  => 'Neptune',     '8'  => 'Waterfall',    '9'  => 'Subscription',
                        '10' => 'Reporting',   '11' => 'Operations',   '12' => 'Targeting',
                        '13' => 'Inv. Docs',
                    ];
                @endphp
                <div class="border-b border-gray-200 px-4 flex gap-1 overflow-x-auto">
                    @foreach($folders as $folder)
                        @php
                            $tabSlug = 'folder-' . $folder->folder_number;
                            $icon    = $tabIcons[$folder->folder_number]  ?? '📁';
                            $label   = $tabLabels[$folder->folder_number] ?? $folder->folder_name;
                        @endphp
                        <button class="tab-btn {{ $loop->first ? 'active' : '' }}"
                                data-tab="{{ $tabSlug }}"
                                onclick="switchTab('{{ $tabSlug }}', this)">
                            {{ $icon }} {{ $label }}
                        </button>
                    @endforeach
                    <button class="tab-btn" data-tab="investor-specific" onclick="switchTab('investor-specific', this)">
                        🔐 Investor Docs
                    </button>
                </div>

                <div class="p-4">

                    {{-- Dynamic folder tabs --}}
                    @foreach($folders as $folder)
                        @php $tabSlug = 'folder-' . $folder->folder_number; @endphp
                        <div id="tab-{{ $tabSlug }}" class="tab-pane {{ $loop->first ? '' : 'hidden' }}">
                            @include('data-room.partials.folder-tree', ['folder' => $folder])
                        </div>
                    @endforeach

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
                                                        <div class="flex items-center gap-1">
                                                            <a href="{{ route('data-room.download', $doc->id) }}" class="download-btn">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                </svg>
                                                                Download
                                                            </a>
                                                            @can('approve', $doc)
                                                                <form method="POST" action="{{ route('data-room.destroy', $doc->id) }}" onclick="event.stopPropagation()">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="download-btn" style="background:#991b1b;"
                                                                            onclick="return confirm('Permanently delete \"{{ addslashes($doc->document_name) }}\"?')">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
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
                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 flex flex-wrap items-center gap-2 text-xs text-gray-500">
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
                    <h3 class="font-semibold text-gray-800">Upload Documents</h3>
                </div>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- Folder --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Folder <span class="text-red-500">*</span>
                    </label>
                    <select id="folder_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">— Select folder —</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" data-investor-id="" data-investor-name="">
                                {{ $folder->folder_number }} · {{ $folder->folder_name }}
                            </option>
                            @foreach($folder->children as $child)
                                <option value="{{ $child->id }}" data-investor-id="" data-investor-name="">
                                    &nbsp;&nbsp;&nbsp;└ {{ $child->folder_number }} · {{ $child->folder_name }}
                                </option>
                            @endforeach
                        @endforeach
                        @foreach($investorFolders as $invRoot)
                            @php $uploadable = $invRoot->children; @endphp
                            @if($uploadable->isNotEmpty())
                                <optgroup label="── {{ $invRoot->folder_name }} ──">
                                    @foreach($uploadable as $subFolder)
                                        <option value="{{ $subFolder->id }}"
                                                data-investor-id="{{ $subFolder->investor_id }}"
                                                data-investor-name="{{ $invRoot->folder_name }}">
                                            📁 {{ $subFolder->folder_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div id="investor-folder-info" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                    Uploading to investor folder: <strong id="investor-folder-name"></strong>
                </div>

                {{-- Drag & drop zone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Files <span class="text-red-500">*</span>
                    </label>
                    <input type="file" id="batchFileInput" multiple
                           accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx,.ppt,.eml"
                           class="hidden">
                    <div id="dropZone"
                         onclick="document.getElementById('batchFileInput').click()"
                         class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl px-4 py-8 cursor-pointer transition hover:border-blue-400 hover:bg-blue-50 select-none">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-600">Drag files here, or click to browse</p>
                        <p class="text-xs text-gray-400">PDF, Word, Excel, PowerPoint, EML · Max 500 MB per file</p>
                    </div>
                </div>

                {{-- File queue — populated by JS when files are chosen --}}
                <ul id="fileQueue" class="hidden space-y-2 max-h-48 overflow-y-auto pr-1"></ul>

                {{-- Version + Access Level --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Version</label>
                        <input type="text" id="batch_version" value="1.0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Access Level</label>
                        <select id="batch_access_level"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="public" selected>🟢 Public</option>
                            <option value="restricted">🔵 Restricted</option>
                            <option value="confidential">🟠 Confidential</option>
                            <option value="highly_confidential">🔴 Highly Confidential</option>
                        </select>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea id="batch_description" rows="2"
                              placeholder="Brief description (applies to all files)..."
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <div id="uploadError" class="hidden p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg"></div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="uploadCancelBtn" onclick="closeUploadModal()"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">
                        Cancel
                    </button>
                    <button type="button" id="uploadSubmitBtn" onclick="startBatchUpload()"
                            class="px-5 py-2 text-sm text-white bg-brand-darker rounded-lg hover:opacity-90 transition font-semibold shadow-sm">
                        Upload
                    </button>
                </div>
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
                        @foreach($folders as $f)
                            @php $icon = $tabIcons[$f->folder_number] ?? '📁'; @endphp
                            <li>{{ $icon }} <strong>{{ $f->folder_name }}</strong></li>
                        @endforeach
                        <li>🔐 <strong>Investor Personal Documents</strong> — Private folders per investor</li>
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

    // ── Upload modal helpers ──────────────────────────────────────────────────

    function fileIcon(ext) {
        if (ext === 'pdf')                    return '📄';
        if (['xlsx','xls'].includes(ext))     return '📊';
        if (['pptx','ppt'].includes(ext))     return '📽️';
        if (['docx','doc'].includes(ext))     return '📝';
        if (ext === 'eml')                    return '✉️';
        return '📎';
    }

    // Drag & drop on the drop zone
    (function () {
        const zone = document.getElementById('dropZone');
        if (!zone) return;

        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('border-blue-400', 'bg-blue-50');
        });
        zone.addEventListener('dragleave', function (e) {
            if (!zone.contains(e.relatedTarget)) {
                zone.classList.remove('border-blue-400', 'bg-blue-50');
            }
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('border-blue-400', 'bg-blue-50');
            const dt    = e.dataTransfer;
            const input = document.getElementById('batchFileInput');
            // Transfer the dropped FileList into the hidden input via DataTransfer
            try {
                const transfer = new DataTransfer();
                Array.from(dt.files).forEach(function (f) { transfer.items.add(f); });
                input.files = transfer.files;
            } catch (_) {
                // fallback: set directly (works in most browsers)
                input.files = dt.files;
            }
            input.dispatchEvent(new Event('change'));
        });
    })();

    document.getElementById('batchFileInput')?.addEventListener('change', function () {
        const queue = document.getElementById('fileQueue');
        const zone  = document.getElementById('dropZone');
        queue.innerHTML = '';
        if (!this.files.length) {
            queue.classList.add('hidden');
            if (zone) zone.querySelector('p.font-medium').textContent = 'Drag files here, or click to browse';
            return;
        }
        if (zone) {
            const n = this.files.length;
            zone.querySelector('p.font-medium').textContent = n + ' file' + (n > 1 ? 's' : '') + ' selected — drop more or click to change';
        }
        queue.classList.remove('hidden');
        Array.from(this.files).forEach((file, i) => {
            const ext = file.name.split('.').pop().toLowerCase();
            const li  = document.createElement('li');
            li.id     = 'queue-item-' + i;
            li.className = 'flex items-center gap-3 p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm';
            li.innerHTML =
                '<span class="text-base flex-shrink-0">' + fileIcon(ext) + '</span>' +
                '<div class="flex-1 min-w-0">' +
                    '<div class="font-medium text-gray-700 truncate">' + file.name + '</div>' +
                    '<div class="text-xs text-gray-400">' + (file.size / 1024 / 1024).toFixed(1) + ' MB</div>' +
                    '<div id="progress-area-' + i + '" class="hidden mt-1">' +
                        '<div class="flex justify-between text-xs text-gray-400 mb-0.5">' +
                            '<span id="lbl-' + i + '">Waiting…</span>' +
                            '<span id="pct-' + i + '">0%</span>' +
                        '</div>' +
                        '<div class="w-full bg-gray-200 rounded-full h-1.5">' +
                            '<div id="bar-' + i + '" class="bg-blue-500 h-1.5 rounded-full transition-all duration-150" style="width:0%"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<span id="status-' + i + '" class="text-xs flex-shrink-0">⏳</span>';
            queue.appendChild(li);
        });
        const n = this.files.length;
        document.getElementById('uploadSubmitBtn').textContent = 'Upload ' + n + ' File' + (n > 1 ? 's' : '');
    });

    function showUploadModal() { document.getElementById('uploadModal').classList.remove('hidden'); }

    function closeUploadModal() {
        if (document.getElementById('uploadSubmitBtn').disabled) return;
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('batchFileInput').value = '';
        document.getElementById('fileQueue').innerHTML  = '';
        document.getElementById('fileQueue').classList.add('hidden');
        const zone = document.getElementById('dropZone');
        if (zone) zone.querySelector('p.font-medium').textContent = 'Drag files here, or click to browse';
        document.getElementById('batch_version').value      = '1.0';
        document.getElementById('batch_description').value  = '';
        document.getElementById('batch_access_level').value = 'public';
        document.getElementById('uploadError').classList.add('hidden');
        document.getElementById('uploadSubmitBtn').disabled    = false;
        document.getElementById('uploadSubmitBtn').textContent = 'Upload';
        document.getElementById('folder_id').value = '';
        document.getElementById('investor-folder-info').classList.add('hidden');
    }

    async function startBatchUpload() {
        const folderId    = document.getElementById('folder_id').value;
        const files       = document.getElementById('batchFileInput').files;
        const version     = document.getElementById('batch_version').value      || '1.0';
        const desc        = document.getElementById('batch_description').value  || null;
        const accessLevel = document.getElementById('batch_access_level').value || 'public';
        const submitBtn  = document.getElementById('uploadSubmitBtn');
        const cancelBtn  = document.getElementById('uploadCancelBtn');
        const errorBox   = document.getElementById('uploadError');
        const selected   = document.getElementById('folder_id').selectedOptions[0];
        const investorId = selected?.getAttribute('data-investor-id') || null;

        errorBox.classList.add('hidden');

        if (!folderId) {
            errorBox.textContent = 'Please select a folder.';
            errorBox.classList.remove('hidden');
            return;
        }
        if (!files.length) {
            errorBox.textContent = 'Please select at least one file.';
            errorBox.classList.remove('hidden');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;

        submitBtn.disabled  = true;
        cancelBtn.disabled  = true;
        submitBtn.textContent = 'Uploading 0/' + files.length + '…';

        let successCount = 0;
        const errors = [];

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            document.getElementById('progress-area-' + i)?.classList.remove('hidden');
            const lbl        = document.getElementById('lbl-' + i);
            const pct        = document.getElementById('pct-' + i);
            const bar        = document.getElementById('bar-' + i);
            const statusIcon = document.getElementById('status-' + i);

            if (lbl) lbl.textContent = 'Requesting URL…';
            if (statusIcon) statusIcon.textContent = '🔄';

            try {
                // Step 1 — presign
                const presignRes = await fetch("{{ route('data-room.presign') }}", {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body:    JSON.stringify({ folder_id: folderId, file_name: file.name, file_size: file.size }),
                });
                if (!presignRes.ok) {
                    const err = await presignRes.json().catch(() => ({}));
                    throw new Error(err.message || 'Could not get upload URL');
                }
                const { url, key } = await presignRes.json();

                // Step 2 — upload to R2
                if (lbl) lbl.textContent = 'Uploading…';
                await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('PUT', url, true);
                    xhr.upload.onprogress = (ev) => {
                        if (ev.lengthComputable) {
                            const p = Math.round((ev.loaded / ev.total) * 100);
                            if (bar) bar.style.width = p + '%';
                            if (pct) pct.textContent = p + '%';
                        }
                    };
                    xhr.onload  = () => xhr.status === 200 ? resolve() : reject(new Error('Storage error HTTP ' + xhr.status));
                    xhr.onerror = () => reject(new Error('Network / CORS error'));
                    xhr.send(file);
                });

                // Step 3 — confirm metadata
                if (lbl) lbl.textContent = 'Saving…';
                if (bar) bar.style.width = '100%';
                if (pct) pct.textContent = '100%';

                const ext        = file.name.split('.').pop().toLowerCase();
                const docName    = file.name.replace(/\.[^.]+$/, '');
                const confirmRes = await fetch("{{ route('data-room.confirm') }}", {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body:    JSON.stringify({
                        folder_id:     folderId,
                        investor_id:   investorId || null,
                        document_name: docName,
                        file_path:     key,
                        file_type:     ext,
                        file_size:     file.size,
                        version:       version,
                        description:   desc,
                        access_level:  accessLevel,
                    }),
                });
                if (!confirmRes.ok) {
                    const err = await confirmRes.json().catch(() => ({}));
                    throw new Error(err.message || 'Failed to save document record');
                }

                successCount++;
                if (lbl) lbl.textContent = 'Done';
                if (bar) { bar.style.width = '100%'; bar.classList.replace('bg-blue-500', 'bg-green-500'); }
                if (statusIcon) statusIcon.textContent = '✅';

            } catch (err) {
                errors.push(file.name + ': ' + err.message);
                if (lbl) lbl.textContent = 'Failed';
                if (bar) bar.classList.replace('bg-blue-500', 'bg-red-400');
                if (statusIcon) statusIcon.textContent = '❌';
            }

            submitBtn.textContent = 'Uploading ' + (i + 1) + '/' + files.length + '…';
        }

        submitBtn.disabled  = false;
        cancelBtn.disabled  = false;
        submitBtn.textContent = 'Done';

        if (errors.length) {
            errorBox.innerHTML = '<strong>' + errors.length + ' file(s) failed:</strong><br>' +
                errors.map(function(e) { return '• ' + e; }).join('<br>');
            errorBox.classList.remove('hidden');
        }

        if (successCount > 0) {
            setTimeout(function () { window.location.reload(); }, 900);
        }
    }

    // Show investor-folder banner when an investor sub-folder is picked
    document.getElementById('folder_id')?.addEventListener('change', function () {
        const selected     = this.options[this.selectedIndex];
        const investorId   = selected?.getAttribute('data-investor-id')   || '';
        const investorName = selected?.getAttribute('data-investor-name') || '';
        const infoBox      = document.getElementById('investor-folder-info');
        if (investorId) {
            document.getElementById('investor-folder-name').textContent = investorName;
            infoBox.classList.remove('hidden');
        } else {
            infoBox.classList.add('hidden');
        }
    });

    function showReadMe() { document.getElementById('readMeModal').classList.remove('hidden'); }
    function closeReadMe() { document.getElementById('readMeModal').classList.add('hidden'); }

    function showRejectModal(docId) {
        document.getElementById('rejectForm').action = '/data-room/documents/' + docId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    // Backdrop click closes modals (upload modal blocked while uploading)
    document.getElementById('uploadModal')?.addEventListener('click', function (e) {
        if (e.target === this && !document.getElementById('uploadSubmitBtn').disabled) closeUploadModal();
    });
    ['readMeModal', 'rejectModal'].forEach(function (id) {
        document.getElementById(id)?.addEventListener('click', function (e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });
    </script>

</x-app-layout>