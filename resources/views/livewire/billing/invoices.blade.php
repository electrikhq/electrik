<div>

	<x-slate::header color="white" full-width>
		<x-slot name="title">
			<div class="flex items-center space-x-2">

				<x-slate::icon icon="carbon-receipt" color="black" />

				<x-slate::heading font-bold>{{ auth()->user()->currentTeam->name }} <span class="text-gray-">invoices</span></x-slate::heading>
				
			</div>
		</x-slot>
		<x-slot name="actions">
		</x-slot>
	</x-slate::header>


	<x-slate::section transparent class="max-w-7xl mx-auto">
		<x-slot name="left" transparent>
			<x-slate::heading tag="h2" font-medium>Invoices</x-slate::heading>
			<x-slate::content>
				<p>You can find all your invoices here</p>
				<p>If you would like to manage your subscription on our payment gateway partner's website, <a href="{{ auth()->user()->currentTeam->billingPortalUrl() }}">click here</a>.</p>
			</x-slate::content>
		</x-slot>	
		<x-slot name="right">
			<x-slate::table>
				<x-slot name="head">
					<x-slate::table-head-cell>
						Number
					</x-slate::table-head-cell>
					<x-slate::table-head-cell>
						Date
					</x-slate::table-head-cell>
					<x-slate::table-head-cell>
						Total
					</x-slate::table-head-cell>
					<x-slate::table-head-cell>
						Status
					</x-slate::table-head-cell>
					<x-slate::table-head-cell>
						Action
					</x-slate::table-head-cell>
				</x-slot>

				@foreach ($invoices as $invoice)
					
					<x-slate::table-row>
						<x-slate::table-cell>
							{{ $invoice->number }}
						</x-slate::table-cell>
						<x-slate::table-cell>
							{{ $invoice->date()->toFormattedDateString() }}
						</x-slate::table-cell>
						<x-slate::table-cell>
							{{ $invoice->total() }}
						</x-slate::table-cell>
						<x-slate::table-cell>
							<x-slate::badge color="primary" size="md" rounded>
								{{ $invoice->status }}
							</x-slate::badge>
						</x-slate::table-cell>
						<x-slate::table-cell>
							<x-slate::icon icon="carbon-view" size="sm" target="_blank" type="link" link="{{ $invoice->hosted_invoice_url }}" />
							<x-slate::icon icon="carbon-download" size="sm" target="_blank" type="link" link="{{ $invoice->invoice_pdf }}" />
						</x-slate::table-cell>
					</x-slate::table-row>
				@endforeach
			</x-slate::table>
		</x-slot>
	</x-slate::section>
</div>