{{-- resources/views/investor/partials/folder-tree.blade.php --}}

<div class="mb-1">
    {{-- Parent Folder Row --}}
    <div class="folder-row" onclick="toggleFolder('inv-{{ $folder->id }}')">
        <svg id="chevron-inv-{{ $folder->id }}" class="w-4 h-4 chevron {{ $folder->children->count() || $folder->documents->count() ? '' : 'opacity-0' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>

        <div class="flex items-center gap-2 min-w-0">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
            </svg>
            <span class="text-xs font-mono text-gray-400 flex-shrink-0">{{ $folder->folder_number }}</span>
            <span class="text-sm font-semibold text-gray-800 truncate">{{ $folder->folder_name }}</span>
        </div>

        <span class="text-xs text-gray-400">
            @php
                $totalDocs = $folder->documents->count() + $folder->children->sum(fn($c) => $c->documents->count());
            @endphp
            @if($totalDocs > 0) {{ $totalDocs }} {{ $totalDocs == 1 ? 'doc' : 'docs' }} @endif
        </span>
    </div>

    {{-- Folder Contents --}}
    <div id="folder-inv-{{ $folder->id }}" class="collapsible hidden ml-4 border-l border-gray-100 pl-2 mt-0.5 mb-1">

        {{-- Direct Documents in Parent --}}
        @foreach($folder->documents as $doc)
        <div class="doc-row" style="padding-left: 1.25rem;">
            <span class="file-icon">
                @if($doc->file_type === 'pdf') 📄
                @elseif(in_array($doc->file_type, ['xlsx','xls'])) 📊
                @elseif(in_array($doc->file_type, ['pptx','ppt'])) 📽️
                @elseif(in_array($doc->file_type, ['docx','doc'])) 📝
                @else 📎
                @endif
            </span>
            <span class="text-sm text-gray-700 truncate" title="{{ $doc->document_name }}">{{ $doc->document_name }}</span>
            <span class="text-xs text-gray-400">v{{ $doc->version }}</span>
            <span class="text-xs text-gray-400">{{ $doc->created_at->format('M d, Y') }}</span>
            <a href="{{ route('investor.documents.download', $doc->id) }}" class="download-btn" onclick="event.stopPropagation()">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download
            </a>
        </div>
        @endforeach

        {{-- Subfolders --}}
        @foreach($folder->children as $child)
            <div class="mb-0.5">
                <div class="subfolder-row" onclick="toggleFolder('inv-{{ $child->id }}')">
                    <svg id="chevron-inv-{{ $child->id }}" class="w-3.5 h-3.5 chevron {{ $child->documents->count() ? '' : 'opacity-0' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>

                    <div class="flex items-center gap-2 min-w-0">
                        <svg class="w-3.5 h-3.5 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                        <span class="text-xs font-mono text-gray-400 flex-shrink-0">{{ $child->folder_number }}</span>
                        <span class="text-sm text-gray-700 truncate">{{ $child->folder_name }}</span>
                    </div>

                    <span class="text-xs text-gray-400">
                        @if($child->documents->count() > 0) {{ $child->documents->count() }} docs @endif
                    </span>
                </div>

                @if($child->documents->count())
                    <div id="folder-inv-{{ $child->id }}" class="collapsible hidden ml-4 border-l border-gray-100 pl-2 mt-0.5">
                        @foreach($child->documents as $doc)
                        <div class="doc-row" style="padding-left: 1.25rem;">
                            <span class="file-icon">
                                @if($doc->file_type === 'pdf') 📄
                                @elseif(in_array($doc->file_type, ['xlsx','xls'])) 📊
                                @elseif(in_array($doc->file_type, ['pptx','ppt'])) 📽️
                                @elseif(in_array($doc->file_type, ['docx','doc'])) 📝
                                @else 📎
                                @endif
                            </span>
                            <span class="text-sm text-gray-700 truncate" title="{{ $doc->document_name }}">{{ $doc->document_name }}</span>
                            <span class="text-xs text-gray-400">v{{ $doc->version }}</span>
                            <span class="text-xs text-gray-400">{{ $doc->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('investor.documents.download', $doc->id) }}" class="download-btn" onclick="event.stopPropagation()">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Empty folder state --}}
        @if($folder->documents->isEmpty() && $folder->children->isEmpty())
            <div class="py-3 pl-4 text-xs text-gray-400 italic">No documents available yet.</div>
        @endif

    </div>
</div>