<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Download Documents — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

        <div class="w-full max-w-lg">

            <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="mx-auto h-16 w-auto object-contain">
                </a>
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">Your Documents</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Access granted for <strong>{{ $link->package->name }}</strong>.
                </p>
            </div>

            <div class="bg-white shadow-md rounded-lg px-8 py-6">

                {{-- Access expiry notice --}}
                <div class="bg-blue-50 border border-blue-200 rounded-md px-4 py-3 mb-6 text-sm text-blue-800">
                    <p>
                        Access expires on
                        <strong>{{ $accessRequest->expires_at->format('d M Y \a\t H:i') }}</strong>
                        ({{ $accessRequest->expires_at->diffForHumans() }}).
                    </p>
                </div>

                {{-- Document list --}}
                @php $items = $link->package->items->load('document.folder'); @endphp

                @if($items->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">No documents in this package.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($items as $item)
                            @if($item->document)
                                <li class="py-4 flex items-center gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->document->document_name }}</p>
                                        @if($item->document->folder)
                                            <p class="text-xs text-gray-400 truncate">{{ $item->document->folder->folder_name }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('doc-access.download', [$link->token, $item->document->id]) }}"
                                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2 border border-blue-600 text-sm font-medium rounded-md text-blue-600 hover:bg-blue-50 focus:outline-none">
                                        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download</span>
                                        @if($item->document->file_type)
                                            <span class="text-xs text-gray-400 uppercase">{{ $item->document->file_type }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif

            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                These documents are confidential. Please do not share this link.
                <br>{{ config('app.name') }} &mdash; Secure Document Access
            </p>

        </div>
    </div>

</body>
</html>
