<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Notice — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            <div class="text-center mb-10">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="mx-auto h-16 w-auto object-contain">
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg px-8 py-10 prose prose-sm max-w-none">

                <h1 class="text-2xl font-semibold text-gray-900 mb-2">Privacy Notice</h1>
                <p class="text-sm text-gray-500 mb-8">Triton Real Estate Fund (CEIC) Limited</p>

                <p>This Privacy Notice explains how Triton Real Estate Fund (CEIC) Limited ("Triton", "we", "us", or "our") collects, uses, and processes personal data in connection with investor communications and access to fund-related materials.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">1. Who we are</h2>
                <p>Triton Real Estate Fund (CEIC) Limited is a DIFC-domiciled Qualified Investor Fund. The Fund is managed by Axys Capital Ltd, a DFSA-regulated entity.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">2. What personal data we collect</h2>
                <p>We may collect and process the following types of personal data:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>Identification data (such as name and email address)</li>
                    <li>Communication data (such as correspondence, submitted forms, and responses)</li>
                    <li>Access and usage data (such as IP address, browser type, device information, access timestamps, and document download activity)</li>
                    <li>Investor-related information voluntarily provided (such as company details or investment interest)</li>
                </ul>
                <p class="mt-3">We collect this data when you:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>request access to documents;</li>
                    <li>communicate with us;</li>
                    <li>interact with our platform or materials.</li>
                </ul>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">3. How we use your data</h2>
                <p>We process your personal data for the following purposes:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>to provide access to fund materials and manage document requests;</li>
                    <li>to evaluate your interest in the Fund;</li>
                    <li>to communicate with you regarding the Fund and related opportunities;</li>
                    <li>to maintain security, monitor access, and prevent unauthorized use;</li>
                    <li>to comply with applicable legal, regulatory, and audit requirements.</li>
                </ul>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">4. Legal basis for processing</h2>
                <p>We process your personal data based on:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>your consent (e.g. when you request access to documents);</li>
                    <li>our legitimate interests in managing investor communications and protecting our platform;</li>
                    <li>compliance with legal and regulatory obligations.</li>
                </ul>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">5. Data sharing</h2>
                <p>We may share your personal data with:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>the Fund manager (Axys Capital Ltd);</li>
                    <li>service providers supporting our platform and operations;</li>
                    <li>regulatory authorities or advisors where required.</li>
                </ul>
                <p class="mt-3">We do not sell your personal data.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">6. Data retention</h2>
                <p>We retain personal data only for as long as necessary for the purposes described above, including regulatory and audit requirements.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">7. Data security</h2>
                <p>We implement appropriate technical and organizational measures to protect your personal data, including access controls and activity logging.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">8. Your rights</h2>
                <p>Subject to applicable law, you may have the right to:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>request access to your personal data;</li>
                    <li>request correction of inaccurate data;</li>
                    <li>request deletion of your data;</li>
                    <li>withdraw consent where processing is based on consent.</li>
                </ul>
                <p class="mt-3">Requests may be submitted using the contact details below.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">9. International transfers</h2>
                <p>Your personal data may be processed in jurisdictions outside the DIFC where necessary for operational purposes. We take reasonable steps to ensure appropriate protection of your data.</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">10. Contact</h2>
                <p>If you have any questions about this Privacy Notice or how your data is processed, please contact us at:</p>
                <p class="mt-2 text-gray-500">[Insert contact email]</p>

                <hr class="my-6 border-gray-200">

                <h2 class="text-base font-semibold text-gray-800 mt-6 mb-2">11. Updates</h2>
                <p>We may update this Privacy Notice from time to time. The latest version will always be available at this link.</p>

            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                {{ config('app.name') }} &mdash; Secure Document Access
            </p>

        </div>
    </div>

</body>
</html>
