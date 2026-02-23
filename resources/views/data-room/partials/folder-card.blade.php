{{-- resources/views/data-room/partials/folder-card.blade.php --}}

<div class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
    <div class="flex items-center p-4 bg-gray-50">
        <span class="font-mono text-sm text-gray-600 font-semibold mr-3 w-16">{{ $folder->folder_number }}</span>
        <span class="font-semibold text-gray-900 flex-1">{{ $folder->folder_name }}</span>
        @if($folder->documents->count() > 0)
            <span class="text-xs text-gray-600 mr-3">
                {{ $folder->documents->count() }} {{ $folder->documents->count() == 1 ? 'document' : 'documents' }}
            </span>
        @endif
        <span class="ml-auto px-3 py-1 text-xs rounded-full font-semibold
            @if($folder->access_level === 'public') bg-green-100 text-green-800
            @elseif($folder->access_level === 'restricted') bg-blue-100 text-blue-800
            @elseif($folder->access_level === 'confidential') bg-orange-100 text-orange-800
            @else bg-red-100 text-red-800
            @endif">
            {{ ucfirst(str_replace('_', ' ', $folder->access_level)) }}
        </span>
    </div>
    
    @if($folder->children->count())
        <div class="bg-white">
            @foreach($folder->children as $child)
                <div class="border-t border-gray-100">
                    <div class="flex items-center p-3 hover:bg-gray-50">
                        <span class="font-mono text-xs text-gray-500 mr-3 w-16 ml-4">{{ $child->folder_number }}</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $child->folder_name }}</span>
                        @if($child->documents->count() > 0)
                            <span class="text-xs text-gray-500 mr-3">
                                {{ $child->documents->count() }} docs
                            </span>
                        @endif
                        <span class="ml-auto px-2 py-1 text-xs rounded-full
                            @if($child->access_level === 'public') bg-green-50 text-green-700
                            @elseif($child->access_level === 'restricted') bg-blue-50 text-blue-700
                            @elseif($child->access_level === 'confidential') bg-orange-50 text-orange-700
                            @else bg-red-50 text-red-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $child->access_level)) }}
                        </span>
                    </div>
                    
                    {{-- Documents List --}}
                    @if($child->documents->count() > 0)
                        <div class="ml-20 mb-2">
                            @foreach($child->documents as $doc)
                                <div class="flex items-center p-2 text-xs hover:bg-blue-50 rounded">
                                    <span class="mr-2">
                                        @if($doc->file_type === 'pdf') 📄
                                        @elseif($doc->file_type === 'xlsx') 📊
                                        @elseif($doc->file_type === 'pptx') 📽️
                                        @elseif($doc->file_type === 'docx' || $doc->file_type === 'doc') 📝
                                        @else 📎
                                        @endif
                                    </span>
                                    <span class="flex-1 text-gray-700">{{ $doc->document_name }}</span>
                                    <span class="text-gray-500 mr-2">v{{ $doc->version }}</span>
                                    <span class="px-2 py-1 rounded text-xs mr-2
                                        @if($doc->status === 'approved') bg-green-100 text-green-700
                                        @elseif($doc->status === 'pending_review') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                    </span>
                                    
                                    {{-- DOWNLOAD BUTTON --}}
                                    <a href="{{ route('data-room.download', $doc->id) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold transition"
                                       title="Download {{ $doc->document_name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>
    @endif
</div>