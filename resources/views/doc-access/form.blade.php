<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document Access Request — {{ config('app.name') }}</title>
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
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">Document Access Request</h1>
                <p class="mt-2 text-sm text-gray-600">
                    You have been invited to access the
                    <strong>{{ $link->package->name }}</strong> document package.
                </p>
                @if($link->label)
                    <p class="mt-1 text-xs text-gray-400">{{ $link->label }}</p>
                @endif
            </div>

            <div class="bg-white shadow-md rounded-lg px-8 py-6">

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-6">
                    Please enter your name and email address to request access.
                    Your request will be reviewed and you will be notified once approved.
                </p>

                <form action="{{ route('doc-access.submit', $link->token) }}" method="POST">
                    @csrf

                    <div class="space-y-4">

                        <div>
                            <label for="requester_name" class="block text-sm font-medium text-gray-700">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="requester_name" id="requester_name" required
                                   value="{{ old('requester_name') }}"
                                   autocomplete="name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="requester_email" class="block text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="requester_email" id="requester_email" required
                                   value="{{ old('requester_email') }}"
                                   autocomplete="email"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Request Access
                        </button>

                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                            By requesting access to these materials, you acknowledge the Privacy Notice and consent
                            to the processing of your personal data for access management, security monitoring,
                            regulatory compliance, and audit recordkeeping.
                            <a href="{{ route('privacy-notice') }}" target="_blank" class="underline hover:text-gray-700">Privacy Notice</a>
                        </p>

                    </div>
                </form>

            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                {{ config('app.name') }} &mdash; Secure Document Access
            </p>

        </div>
    </div>

</body>
</html>
