{{-- Reverse Payment Modal --}}
<div id="reversePaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center mb-4">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-2 text-center">Reverse Payment</h3>
            <p class="text-sm text-gray-500 mb-4 text-center">This action will reverse the payment and update totals. This cannot be undone.</p>
            
            <form id="reversePaymentForm" method="POST">
                @csrf
                
                {{-- Reason for Reversal --}}
                <div class="mb-4">
                    <label for="reverse_notes" class="block text-sm font-medium text-gray-700 mb-2">Reason for Reversal *</label>
                    <textarea name="notes" id="reverse_notes" rows="4" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Explain why this payment is being reversed..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">This reason will be logged for audit purposes.</p>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReverseModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reverse Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentReversePaymentId = null;

    function openReverseModal(paymentId) {
        currentReversePaymentId = paymentId;
        const modal = document.getElementById('reversePaymentModal');
        const form = document.getElementById('reversePaymentForm');
        form.action = `/payments/${paymentId}/reverse`;
        modal.classList.remove('hidden');
    }

    function closeReverseModal() {
        const modal = document.getElementById('reversePaymentModal');
        modal.classList.add('hidden');
        document.getElementById('reversePaymentForm').reset();
        currentReversePaymentId = null;
    }

    // Close modal when clicking outside
    document.getElementById('reversePaymentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReverseModal();
        }
    });
</script>
