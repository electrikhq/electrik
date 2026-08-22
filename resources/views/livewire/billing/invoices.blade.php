<div class="space-y-6">

    <x-electrik::page-header
        title="Invoices"
        :description="'Download invoices from Stripe for '.$team->name.'.'"
    />

    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <div class="overflow-x-auto rounded-xl border border-border/80 bg-card shadow-xs">
        <table class="min-w-full text-sm">
            <thead class="border-b border-border bg-muted/40 text-left text-muted-foreground">
                <tr>
                    <th class="px-4 py-2 font-medium">Date</th>
                    <th class="px-4 py-2 font-medium">Total</th>
                    <th class="px-4 py-2 font-medium">Status</th>
                    <th class="px-4 py-2 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-b border-border last:border-0" wire:key="inv-{{ $invoice->id }}">
                        <td class="px-4 py-3">{{ $invoice->date()->toFormattedDateString() }}</td>
                        <td class="px-4 py-3">{{ $invoice->total() }}</td>
                        <td class="px-4 py-3">{{ $invoice->status }}</td>
                        <td class="px-4 py-3 text-end">
                            <x-slate::button type="button" variant="ghost" size="sm" wire:click="download('{{ $invoice->id }}')">
                                Download
                            </x-slate::button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
