{{-- Mark as Paid Modal --}}
<div id="markPaidModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Mark Payment as Paid</h3>
            
            <form id="markPaidForm" method="POST">
                @csrf
                
                {{-- Payment Method --}}
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                        <option value="">Select method...</option>
                        <option value="wire_transfer">Wire Transfer</option>
                        <option value="ach">ACH</option>
                        <option value="check">Check</option>
                        <option value="cash">Cash</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                {{-- Reference Number --}}
                <div class="mb-4">
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                        placeholder="e.g., WIRE-123456">
                </div>

                {{-- Paid Date --}}
                <div class="mb-4">
                    <label for="paid_date" class="block text-sm font-medium text-gray-700 mb-2">Paid Date *</label>
                    <input type="date" name="paid_date" id="paid_date" required
                        value="{{ date('Y-m-d') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark">
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="payment_notes" rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-dark focus:ring-brand-dark"
                        placeholder="Additional payment details..."></textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMarkPaidModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPaymentId = null;

    function openMarkPaidModal(paymentId) {
        currentPaymentId = paymentId;
        const modal = document.getElementById('markPaidModal');
        const form = document.getElementById('markPaidForm');
        form.action = `/payments/${paymentId}/mark-paid`;
        modal.classList.remove('hidden');
    }

    function closeMarkPaidModal() {
        const modal = document.getElementById('markPaidModal');
        modal.classList.add('hidden');
        document.getElementById('markPaidForm').reset();
        currentPaymentId = null;
    }

    // Close modal when clicking outside
    document.getElementById('markPaidModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMarkPaidModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMarkPaidModal();
        }
    });
</script>
