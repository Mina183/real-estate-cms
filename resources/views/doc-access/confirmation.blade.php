<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Submitted — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="mx-auto h-16 w-auto object-contain">
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg px-8 py-8 text-center">

                @php $status = $accessRequest?->status ?? 'pending'; @endphp

                @if($status === 'rejected')
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Access Not Granted</h2>
                    <p class="mt-3 text-sm text-gray-600">
                        Your request for <strong>{{ $link->package->name }}</strong> was not approved.
                        Please contact us if you believe this is an error.
                    </p>
                @else
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Request Submitted</h2>
                    <p class="mt-3 text-sm text-gray-600">
                        Your request to access the <strong>{{ $link->package->name }}</strong> documents
                        has been received and is currently under review.
                    </p>
                    <p class="mt-3 text-sm text-gray-500">
                        Once approved, return to this page using the same link you were given.
                        Access is granted for <strong>48 hours</strong> from the time of approval.
                    </p>

                    @if($accessRequest)
                        <div class="mt-4 bg-gray-50 rounded-md px-4 py-3 text-left">
                            <p class="text-xs text-gray-500">
                                <span class="font-medium">Reference:</span>
                                Request #{{ $accessRequest->id }} &bull; {{ $accessRequest->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endif

                    <a href="{{ route('doc-access.show', $link->token) }}"
                       class="mt-6 inline-block text-sm text-blue-600 hover:underline">
                        ← Back to access page
                    </a>
                @endif

            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                {{ config('app.name') }} &mdash; Secure Document Access
            </p>

        </div>
    </div>

</body>
</html>
