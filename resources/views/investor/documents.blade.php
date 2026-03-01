@extends('layouts.investor-base')

@php
    $header = '<h2 class="font-semibold text-2xl text-brand-darker leading-tight">My Documents</h2>
        <p class="mt-1 text-sm text-brand-dark">
            Access Level: <span class="font-semibold">' . ucfirst($accessLevel) . '</span>
        </p>';
@endphp

@section('content')
<style>
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
    .badge-public         { background: #dcfce7; color: #166534; }
    .badge-restricted     { background: #dbeafe; color: #1e40af; }
    .badge-confidential   { background: #ffedd5; color: #9a3412; }
    .badge-highly_confidential { background: #fee2e2; color: #991b1b; }
    .badge-approved       { background: #dcfce7; color: #166534; }
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
    .chevron {
        transition: transform 0.2s;
        color: #94a3b8;
        flex-shrink: 0;
    }
    .chevron.open { transform: rotate(90deg); }
    .collapsible { overflow: hidden; }
    .file-icon { font-size: 15px; flex-shrink: 0; }
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
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
</style>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($accessLevel === 'none')
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <h3 class="text-lg font-semibold text-yellow-800">Document Access Not Yet Granted</h3>
                <p class="mt-2 text-sm text-yellow-700">Your document access is currently being processed. You will be notified once access has been granted.</p>
            </div>

        @else

            {{-- Stats --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="stat-card">
                    <div class="stat-icon bg-blue-50">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-800">
                            {{ $folders->sum(fn($f) => $f->documents->count() + $f->children->sum(fn($c) => $c->documents->count())) }}
                        </div>
                        <div class="text-xs text-gray-500">Documents Available</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-green-50">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-800">{{ $folders->count() }}</div>
                        <div class="text-xs text-gray-500">Folders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-indigo-50">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-800">{{ ucfirst($accessLevel) }}</div>
                        <div class="text-xs text-gray-500">Access Level</div>
                    </div>
                </div>
            </div>

            {{-- Main Container --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 px-4 flex gap-1 overflow-x-auto">
                    @php
                        $marketingFolders = $folders->filter(fn($f) => in_array($f->folder_number, ['0.1', '11']));
                        $fundFolders      = $folders->filter(fn($f) => in_array($f->folder_number, ['1', '0.0']));
                        $dueDiligFolders  = $folders->filter(fn($f) => in_array($f->folder_number, ['3', '4', '5', '8']));
                        $legalFolders     = $folders->filter(fn($f) => in_array($f->folder_number, ['2', '6', '7']));
                        $opsFolders       = $folders->filter(fn($f) => in_array($f->folder_number, ['9', '10']));
                        $myFolders        = $folders->filter(fn($f) => str_starts_with($f->folder_number, '12'));

                        // Determine first non-empty tab
                        $firstTab = 'marketing';
                    @endphp

                    <button class="tab-btn active" data-tab="marketing" onclick="switchTab('marketing', this)">
                        📢 Marketing
                    </button>
                    @if($fundFolders->isNotEmpty())
                    <button class="tab-btn" data-tab="fund" onclick="switchTab('fund', this)">
                        📁 Fund Overview
                    </button>
                    @endif
                    @if($dueDiligFolders->isNotEmpty())
                    <button class="tab-btn" data-tab="diligence" onclick="switchTab('diligence', this)">
                        🔍 Due Diligence
                    </button>
                    @endif
                    @if($legalFolders->isNotEmpty())
                    <button class="tab-btn" data-tab="legal" onclick="switchTab('legal', this)">
                        ⚖️ Legal
                    </button>
                    @endif
                    @if($opsFolders->isNotEmpty())
                    <button class="tab-btn" data-tab="operations" onclick="switchTab('operations', this)">
                        📊 Reporting
                    </button>
                    @endif
                    @if($myFolders->isNotEmpty())
                    <button class="tab-btn" data-tab="my-docs" onclick="switchTab('my-docs', this)">
                        🔐 My Documents
                    </button>
                    @endif
                </div>

                <div class="p-4">

                    {{-- MARKETING TAB --}}
                    <div id="tab-marketing" class="tab-pane">
                        @forelse($marketingFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @empty
                            <div class="empty-state">No documents available in this section yet.</div>
                        @endforelse
                    </div>

                    {{-- FUND OVERVIEW TAB --}}
                    @if($fundFolders->isNotEmpty())
                    <div id="tab-fund" class="tab-pane hidden">
                        @foreach($fundFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                    </div>
                    @endif

                    {{-- DUE DILIGENCE TAB --}}
                    @if($dueDiligFolders->isNotEmpty())
                    <div id="tab-diligence" class="tab-pane hidden">
                        @foreach($dueDiligFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                    </div>
                    @endif

                    {{-- LEGAL TAB --}}
                    @if($legalFolders->isNotEmpty())
                    <div id="tab-legal" class="tab-pane hidden">
                        @foreach($legalFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                    </div>
                    @endif

                    {{-- OPERATIONS TAB --}}
                    @if($opsFolders->isNotEmpty())
                    <div id="tab-operations" class="tab-pane hidden">
                        @foreach($opsFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                    </div>
                    @endif

                    {{-- MY DOCUMENTS TAB --}}
                    @if($myFolders->isNotEmpty())
                    <div id="tab-my-docs" class="tab-pane hidden">
                        <div class="flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                            <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-blue-800">These documents have been prepared specifically for you.</p>
                        </div>
                        @foreach($myFolders as $folder)
                            @include('investor.partials.folder-tree', ['folder' => $folder])
                        @endforeach
                    </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 text-xs text-gray-500">
                    These documents are confidential and for your review only. Please do not share without authorization.
                </div>
            </div>

        @endif
    </div>
</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.remove('hidden');
    if (btn) btn.classList.add('active');
}

function toggleFolder(id) {
    const content = document.getElementById('folder-' + id);
    const chevron = document.getElementById('chevron-' + id);
    if (content) {
        content.classList.toggle('hidden');
        chevron?.classList.toggle('open');
    }
}
</script>
@endsection