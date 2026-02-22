@extends('layouts.investor-base')

@php
    $header = '<h2 class="font-semibold text-2xl text-brand-darker leading-tight">
            My Documents
        </h2>
        <p class="mt-1 text-sm text-brand-dark">
            Access Level: <span class="font-semibold">' . ucfirst($accessLevel) . '</span>
        </p>';
@endphp

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if($accessLevel === 'none')
            <!-- No Access -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-yellow-800 dark:text-yellow-400">Document Access Not Yet Granted</h3>
                <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    Your document access is currently being processed. You will be notified once access has been granted.
                </p>
                <p class="mt-4 text-sm text-yellow-600 dark:text-yellow-400">
                    If you have any questions, please <a href="mailto:support@triton.com" class="underline hover:text-yellow-800">contact support</a>.
                </p>
            </div>

        @elseif($folders->isEmpty())
            <!-- Access granted but no documents yet -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-blue-800 dark:text-blue-400">No Documents Available Yet</h3>
                <p class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    Documents will appear here once they are uploaded by the fund manager.
                </p>
            </div>

        @else
            <!-- Document Folders -->
            <div class="space-y-6">
                @foreach($folders as $folder)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <!-- Folder Header -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $folder->folder_number }} - {{ $folder->folder_name }}
                                        </h3>
                                        @if($folder->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $folder->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $folder->documents->count() }} {{ Str::plural('document', $folder->documents->count()) }}
                                </span>
                            </div>
                        </div>

                        <!-- Documents List -->
                        <div class="p-6">
                            @if($folder->documents->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No documents in this folder yet</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($folder->documents as $document)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                            <div class="flex items-center space-x-4 flex-1">
                                                <!-- File Icon -->
                                                <div class="flex-shrink-0">
                                                    @if($document->file_type === 'pdf')
                                                        <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                        </svg>
                                                    @elseif(in_array($document->file_type, ['doc', 'docx']))
                                                        <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                        </svg>
                                                    @elseif(in_array($document->file_type, ['xls', 'xlsx']))
                                                        <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                        </svg>
                                                    @endif
                                                </div>

                                                <!-- Document Info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                                                        {{ $document->document_name }}
                                                    </p>
                                                    <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
                                                        <span>{{ strtoupper($document->file_type) }}</span>
                                                        <span>•</span>
                                                        <span>{{ number_format($document->file_size / 1024, 0) }} KB</span>
                                                        @if($document->version)
                                                            <span>•</span>
                                                            <span>v{{ $document->version }}</span>
                                                        @endif
                                                        <span>•</span>
                                                        <span>{{ $document->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                    @if($document->description)
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $document->description }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Download Button -->
                                            <div class="flex-shrink-0 ml-4">
                                                <a href="{{ route('investor.documents.download', $document->id) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Help Text -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <p class="font-semibold mb-2">Document Access Information:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>These documents are confidential and for your review only</li>
                            <li>Please do not share these documents without authorization</li>
                            <li>New documents will appear automatically when uploaded</li>
                            <li>If you have trouble accessing any document, please contact support</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection