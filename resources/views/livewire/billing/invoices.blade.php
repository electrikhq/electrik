<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Invoices</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">View and download your invoices</p>
    </div>

    @if(session('error'))
        <x-slate::alert type="error" class="mb-6">
            {{ session('error') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        @if($invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $invoice->date()->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    {{ $invoice->total() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-slate::badge 
                                        :color="$invoice->paid ? 'success' : 'warning'"
                                    >
                                        {{ $invoice->paid ? 'Paid' : 'Pending' }}
                                    </x-slate::badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a 
                                        href="{{ route('billing.invoices') }}" 
                                        wire:click.prevent="downloadInvoice('{{ $invoice->id }}')"
                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400"
                                    >
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-neutral-600 dark:text-neutral-400">No invoices found.</p>
            </div>
        @endif
    </x-slate::card>
</div>

