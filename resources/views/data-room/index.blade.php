<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔒 Secure Data Room
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- ============================================ --}}
                {{-- NOVI KOD 1: TEST UPLOAD FORM (samo za tebe) --}}
                {{-- ============================================ --}}
                @if(auth()->user()->email === 'mk@poseidonhumancapital.com') {{-- PROMENI OVO U SVOJ EMAIL! --}}
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-sm text-gray-700 mb-3">🧪 Test Upload (Admin Only)</h4>
                    
                    <form action="{{ route('data-room.test-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Folder:</label>
                            <select name="folder_id" class="border border-gray-300 rounded px-3 py-2 text-sm w-full max-w-md">
                                <option value="">-- Select Folder --</option>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}">{{ $folder->folder_number }} - {{ $folder->folder_name }}</option>
                                    @foreach($folder->children as $child)
                                        <option value="{{ $child->id }}">  └─ {{ $child->folder_number }} - {{ $child->folder_name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Document Name:</label>
                            <input type="text" name="document_name" placeholder="e.g., Bradley Cooper - Chief Compliance Officer.docx" 
                                   class="border border-gray-300 rounded px-3 py-2 text-sm w-full max-w-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File:</label>
                            <input type="file" name="document" accept=".pdf,.doc,.docx,.xlsx,.pptx" 
                                   class="border border-gray-300 rounded px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Version:</label>
                            <input type="text" name="version" value="1.0" 
                                   class="border border-gray-300 rounded px-3 py-2 text-sm w-32">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional):</label>
                            <textarea name="description" rows="2" 
                                      class="border border-gray-300 rounded px-3 py-2 text-sm w-full max-w-md"
                                      placeholder="Professional bio of our Chief Compliance Officer"></textarea>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">
                            Upload Test Document
                        </button>
                    </form>

                    @if(session('upload_success'))
                        <div class="mt-3 p-3 bg-green-100 text-green-800 rounded text-sm">
                            ✅ {{ session('upload_success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mt-3 p-3 bg-red-100 text-red-800 rounded text-sm">
                            ❌ {{ $errors->first() }}
                        </div>
                    @endif
                </div>
                @endif
                {{-- ============================================ --}}
                {{-- KRAJ NOVOG KODA 1 --}}
                {{-- ============================================ --}}
                
                {{-- Header with Download and Read Me buttons --}}
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Document Structure</h3>
                        <p class="text-sm text-gray-600 mt-1">Hierarchical folder organization with security levels</p>
                    </div>
                    
                    <div class="flex gap-3 items-center">
                        <div class="text-sm text-gray-600 mr-2">
                            Total Documents: <span class="font-semibold">{{ \App\Models\DataRoomDocument::count() }}</span>
                        </div>
                        
                        {{-- Document Index Download Button --}}
                        <a href="{{ route('data-room.export-index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Index
                        </a>
                        
                        {{-- Read Me button --}}
                        <button onclick="showReadMe()" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Read Me
                        </button>
                    </div>
                </div>
                
                {{-- Folder Structure --}}
                @foreach($folders as $folder)
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
                                        
                                        {{-- ============================================ --}}
                                        {{-- NOVI KOD 2: DOKUMENTI SA DOWNLOAD DUGMIĆIMA --}}
                                        {{-- ============================================ --}}
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
                                        {{-- ============================================ --}}
                                        {{-- KRAJ NOVOG KODA 2 --}}
                                        {{-- ============================================ --}}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="mt-8 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                    <p class="text-sm text-blue-800">
                        <strong>Security Levels:</strong>
                        <span class="ml-2">🟢 Public</span>
                        <span class="ml-2">🔵 Restricted</span>
                        <span class="ml-2">🟠 Confidential</span>
                        <span class="ml-2">🔴 Highly Confidential</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Read Me Modal --}}
    <div id="readMeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">How to Use This Data Room</h3>
                    <button onclick="closeReadMe()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="text-sm text-gray-600 space-y-3">
                    <p><strong>Purpose:</strong> This Data Room contains all essential documents related to fund operations, legal structure, compliance, and performance reporting.</p>
                    
                    <p><strong>Document Organization:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Documents are organized by section numbers (0, 1, 2, 3, etc.)</li>
                        <li>Each section contains related folders and subfolders</li>
                        <li>Security levels indicate access restrictions</li>
                    </ul>
                    
                    <p><strong>Security Levels:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><span class="text-green-600 font-semibold">Public</span> - Available to all users</li>
                        <li><span class="text-yellow-600 font-semibold">Restricted</span> - Limited access required</li>
                        <li><span class="text-orange-600 font-semibold">Confidential</span> - Approved users only</li>
                        <li><span class="text-red-600 font-semibold">Highly Confidential</span> - Senior management only</li>
                    </ul>
                    
                    <p><strong>Document Index:</strong> Click "Download Index" to get a complete Excel listing of all documents with versions, dates, and descriptions.</p>
                    
                    <p><strong>Contact Information:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Legal queries:</strong> legal@yourfund.com</li>
                        <li><strong>Admin support:</strong> admin@yourfund.com</li>
                        <li><strong>Technical issues:</strong> support@yourfund.com</li>
                    </ul>
                </div>
                
                <div class="mt-6 text-right">
                    <button onclick="closeReadMe()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
    function showReadMe() {
        document.getElementById('readMeModal').classList.remove('hidden');
    }

    function closeReadMe() {
        document.getElementById('readMeModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('readMeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReadMe();
        }
    });
    </script>
</x-app-layout>