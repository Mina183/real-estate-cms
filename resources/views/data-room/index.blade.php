<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔒 Secure Data Room
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Header with Upload, Download and Read Me buttons --}}
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Document Structure</h3>
                        <p class="text-sm text-gray-600 mt-1">Organized by investment stage and category</p>
                    </div>
                    
                    <div class="flex gap-3 items-center">
                        <div class="text-sm text-gray-600 mr-2">
                            Total Documents: <span class="font-semibold">{{ \App\Models\DataRoomDocument::count() }}</span>
                        </div>

                        {{-- Upload Document Button (Admin Only) --}}
                        @can('upload-documents')
                        <button onclick="showUploadModal()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Upload Document
                        </button>
                        @endcan
                        
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

                {{-- TABBED NAVIGATION --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                        <button onclick="switchTab('marketing')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="marketing">
                            Marketing
                        </button>
                        <button onclick="switchTab('compliance')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="compliance">
                            Diligence & Compliance
                        </button>
                        <button onclick="switchTab('legal')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="legal">
                            Legal & Funding
                        </button>
                        <button onclick="switchTab('activation')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="activation">
                            Activation
                        </button>
                        <button onclick="switchTab('monitoring')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="monitoring">
                            Monitoring
                        </button>
                        <button onclick="switchTab('investor-specific')" 
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" 
                                data-tab="investor-specific">
                            Investor-Specific
                        </button>
                    </nav>
                </div>

                {{-- TAB CONTENT --}}
                
                {{-- MARKETING TAB (Sections 1-2) --}}
                <div id="marketing-tab" class="tab-content">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">📢 Marketing Materials</h4>
                    @foreach($folders->whereIn('folder_number', ['1', '2']) as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

                {{-- DILIGENCE & COMPLIANCE TAB (Sections 3-5) --}}
                <div id="compliance-tab" class="tab-content hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">🔍 Diligence & Compliance</h4>
                    @foreach($folders->whereIn('folder_number', ['3', '4', '5']) as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

                {{-- LEGAL & FUNDING TAB (Sections 6-7) --}}
                <div id="legal-tab" class="tab-content hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">⚖️ Legal & Funding</h4>
                    @foreach($folders->whereIn('folder_number', ['6', '7']) as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

                {{-- ACTIVATION TAB (Sections 8-10) --}}
                <div id="activation-tab" class="tab-content hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">🚀 Operations & Activation</h4>
                    @foreach($folders->whereIn('folder_number', ['8', '9', '10']) as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

                {{-- MONITORING TAB (Section 11) --}}
                <div id="monitoring-tab" class="tab-content hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">📊 Reporting & Q&A</h4>
                    @foreach($folders->where('folder_number', '11') as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

                {{-- INVESTOR-SPECIFIC TAB (Section 12) --}}
                <div id="investor-specific-tab" class="tab-content hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">🔐 Investor-Specific Documents</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <strong>Confidential:</strong> Documents in this section are assigned to specific investors and are not visible to others.
                        </p>
                    </div>
                    @foreach($folders->where('folder_number', '12') as $folder)
                        @include('data-room.partials.folder-card', ['folder' => $folder])
                    @endforeach
                </div>

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

    {{-- Upload Document Modal --}}
    <div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">📤 Upload Document</h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if(session('upload_success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
                        ✅ {{ session('upload_success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                        ❌ {{ $errors->first() }}
                    </div>
                @endif
                
                <form action="{{ route('data-room.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Folder <span class="text-red-500">*</span>
                        </label>
                        <select name="folder_id" id="folder_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Folder --</option>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}" data-folder-number="{{ $folder->folder_number }}">
                                    {{ $folder->folder_number }} - {{ $folder->folder_name }}
                                </option>
                                @foreach($folder->children as $child)
                                    <option value="{{ $child->id }}" data-folder-number="{{ $child->folder_number }}">
                                        &nbsp;&nbsp;└─ {{ $child->folder_number }} - {{ $child->folder_name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    {{-- INVESTOR SELECTION (shows only for Section 12) --}}
                    <div id="investor-selection" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assign to Investor (Section 12 only)
                            <span class="text-xs text-gray-500">(Leave blank for general documents)</span>
                        </label>
                        <select name="investor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Available to All Investors --</option>
                            @php
                                $investors = \App\Models\Investor::orderBy('organization_name')->get();
                            @endphp
                            @foreach($investors as $investor)
                                <option value="{{ $investor->id }}">{{ $investor->organization_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Document Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="document_name" required
                               placeholder="e.g., Fund Performance Report Q4 2025.pdf"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="document" required
                               accept=".pdf,.doc,.docx,.xlsx,.pptx"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Accepted: PDF, Word, Excel, PowerPoint</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                            <input type="text" name="version" value="1.0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (optional)</label>
                        <textarea name="description" rows="3"
                                  placeholder="Brief description of the document..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeUploadModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            Upload Document
                        </button>
                    </div>
                </form>
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
                        <li>Documents are organized by investment stage tabs</li>
                        <li>Each tab contains relevant sections for that stage</li>
                        <li>Security levels indicate access restrictions</li>
                    </ul>
                    
                    <p><strong>Security Levels:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><span class="text-green-600 font-semibold">Public</span> - Available to all users</li>
                        <li><span class="text-yellow-600 font-semibold">Restricted</span> - Limited access required</li>
                        <li><span class="text-orange-600 font-semibold">Confidential</span> - Approved users only</li>
                        <li><span class="text-red-600 font-semibold">Highly Confidential</span> - Senior management only</li>
                    </ul>
                    
                    <p><strong>Document Index:</strong> Click "Download Index" to get a complete Excel listing of all documents.</p>
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

    {{-- JavaScript for Tabs and Modals --}}
    <script>
    // Upload Modal
    function showUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
    }

    // Show investor dropdown only for Section 12
    document.getElementById('folder_id')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const folderNumber = selectedOption.getAttribute('data-folder-number');
        const investorSelection = document.getElementById('investor-selection');
        
        if (folderNumber && folderNumber.startsWith('12')) {
            investorSelection.style.display = 'block';
        } else {
            investorSelection.style.display = 'none';
        }
    });
    
    // Tab switching
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active state from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active state to clicked button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        activeButton.classList.add('border-blue-500', 'text-blue-600');
    }
    
    // Initialize first tab as active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('marketing');
    });
    
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