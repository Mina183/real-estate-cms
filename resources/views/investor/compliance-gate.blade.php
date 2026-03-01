@extends('layouts.investor-base')

@php
    $header = '<h2 class="font-semibold text-2xl text-brand-darker leading-tight">Data Room Access</h2>
        <p class="mt-1 text-sm text-brand-dark">Please read and confirm the following before accessing documents.</p>';
@endphp

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Notice --}}
        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-6">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-semibold mb-1">One-time confirmation required</p>
                <p>Before accessing the Triton Real Estate Fund data room, you must read and confirm the statements below. This is required for regulatory compliance and will be recorded with a timestamp.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('investor.compliance-gate.submit') }}">
            @csrf

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                {{-- Section 1: About the Data Room --}}
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-darker text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                        About This Data Room
                    </h3>
                    <div class="text-sm text-gray-600 space-y-2 pl-8">
                        <p>This data room contains confidential documents related to Triton Real Estate Fund operations, legal structure, compliance, and performance reporting.</p>
                        <p><strong>Document Organization:</strong> Documents are organized by section numbers (0, 1, 2, 3, etc.). Each section contains related folders and subfolders. Security levels indicate access restrictions.</p>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-semibold uppercase tracking-wide">Public</span>
                                <span class="text-gray-500">Available to all investors</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-semibold uppercase tracking-wide">Restricted</span>
                                <span class="text-gray-500">Authorized investors</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 font-semibold uppercase tracking-wide">Confidential</span>
                                <span class="text-gray-500">Approved investors only</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-semibold uppercase tracking-wide">Highly Confidential</span>
                                <span class="text-gray-500">Senior management only</span>
                            </div>
                        </div>
                        <p class="mt-2"><strong>Document Index:</strong> A complete Excel listing of all documents with versions and dates is available in section 0.</p>
                    </div>
                </div>

                {{-- Section 2: Contact Information --}}
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-darker text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                        Contact Information
                    </h3>
                    <div class="text-sm text-gray-600 space-y-1 pl-8">
                        <p>📧 Legal queries: <a href="mailto:legal@tritonrealestatefund.com" class="text-brand-darker hover:underline">legal@tritonrealestatefund.com</a></p>
                        <p>📧 Investor relations: <a href="mailto:ir@tritonrealestatefund.com" class="text-brand-darker hover:underline">ir@tritonrealestatefund.com</a></p>
                        <p>📧 Technical support: <a href="mailto:support@tritonrealestatefund.com" class="text-brand-darker hover:underline">support@tritonrealestatefund.com</a></p>
                    </div>
                </div>

                {{-- Section 3: Required Confirmations --}}
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-darker text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                        Required Confirmations
                    </h3>

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-4 pl-8">

                        {{-- Confirmation 1 --}}
                        <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ $errors->has('confirmed_professional_client') ? 'border-red-300 bg-red-50' : '' }}">
                            <input type="checkbox" name="confirmed_professional_client" value="1"
                                   class="mt-0.5 w-4 h-4 text-brand-darker border-gray-300 rounded flex-shrink-0"
                                   {{ old('confirmed_professional_client') ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">I confirm I am a Professional Client</p>
                                <p class="text-xs text-gray-500 mt-0.5">I confirm that I qualify as a Professional Client under the DFSA Client Classification Rules and understand the nature of the investments described in this data room.</p>
                            </div>
                        </label>

                        {{-- Confirmation 2 --}}
                        <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ $errors->has('agreed_confidentiality') ? 'border-red-300 bg-red-50' : '' }}">
                            <input type="checkbox" name="agreed_confidentiality" value="1"
                                   class="mt-0.5 w-4 h-4 text-brand-darker border-gray-300 rounded flex-shrink-0"
                                   {{ old('agreed_confidentiality') ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">I agree to confidentiality and non-distribution</p>
                                <p class="text-xs text-gray-500 mt-0.5">I agree that all documents and information in this data room are strictly confidential. I will not copy, distribute, or disclose any content to third parties without prior written consent from Triton Real Estate Fund.</p>
                            </div>
                        </label>

                        {{-- Confirmation 3 --}}
                        <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ $errors->has('acknowledged_ppm_confidential') ? 'border-red-300 bg-red-50' : '' }}">
                            <input type="checkbox" name="acknowledged_ppm_confidential" value="1"
                                   class="mt-0.5 w-4 h-4 text-brand-darker border-gray-300 rounded flex-shrink-0"
                                   {{ old('acknowledged_ppm_confidential') ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">I acknowledge the PPM is confidential</p>
                                <p class="text-xs text-gray-500 mt-0.5">I acknowledge that the Private Placement Memorandum (PPM) and all related materials are strictly confidential and are not for onward distribution. These documents have been provided solely for my evaluation of the fund.</p>
                            </div>
                        </label>

                        {{-- Confirmation 4 - Optional --}}
                        <label class="flex items-start gap-3 p-4 border border-dashed border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="checkbox" name="acknowledged_risk_warnings" value="1"
                                   class="mt-0.5 w-4 h-4 text-brand-darker border-gray-300 rounded flex-shrink-0"
                                   {{ old('acknowledged_risk_warnings') ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    I acknowledge I have received the risk warnings
                                    <span class="ml-1 text-xs font-normal text-gray-400">(optional)</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">I confirm I have been made aware of the risks associated with investing in real estate funds, including illiquidity, capital loss, and market risks.</p>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Your confirmations will be recorded with a timestamp for regulatory compliance purposes.
                    </p>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-darker text-white text-sm font-semibold rounded-lg hover:opacity-90 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirm & Access Documents
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection