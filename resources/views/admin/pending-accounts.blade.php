@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
	<div class="mb-6 flex items-center justify-between">
		<h1 class="text-2xl font-bold text-gray-900">Pending Account Approvals</h1>
		<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">Back to Dashboard</a>
	</div>

	@if (session('success'))
		<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
	@endif

	@if ($errors->any())
		<div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded">
			<ul class="list-disc pl-5 space-y-1">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="space-y-10">
		<!-- Plumbers -->
		<div class="bg-white rounded-lg shadow">
			<div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
				<h2 class="text-lg font-medium text-gray-900">Plumbers (Pending)</h2>
				<span class="text-sm text-gray-500">{{ $pendingPlumbers->count() }} pending</span>
			</div>
			<div class="p-6">
				@if($pendingPlumbers->count() === 0)
					<p class="text-gray-500">No pending plumbers.</p>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
								</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200">
								@foreach($pendingPlumbers as $user)
									<tr>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->full_name }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->phone_number }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm">
											<form method="POST" action="{{ route('admin.approve-account', $user->id) }}" class="inline">
												@csrf
												<button type="submit" class="px-3 py-1 rounded bg-green-600 text-white text-xs hover:bg-green-700">Approve</button>
											</form>
											<form method="POST" action="{{ route('admin.reject-account', $user->id) }}" class="inline ml-2">
												@csrf
												<button type="submit" class="px-3 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700">Reject</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div>

		<!-- Accountants -->
		<div class="bg-white rounded-lg shadow">
			<div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
				<h2 class="text-lg font-medium text-gray-900">Accountants (Pending)</h2>
				<span class="text-sm text-gray-500">{{ $pendingAccountants->count() }} pending</span>
			</div>
			<div class="p-6">
				@if($pendingAccountants->count() === 0)
					<p class="text-gray-500">No pending accountants.</p>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
									<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
								</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200">
								@foreach($pendingAccountants as $user)
									<tr>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->full_name }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->phone_number }}</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm">
											<form method="POST" action="{{ route('admin.approve-account', $user->id) }}" class="inline">
												@csrf
												<button type="submit" class="px-3 py-1 rounded bg-green-600 text-white text-xs hover:bg-green-700">Approve</button>
											</form>
											<form method="POST" action="{{ route('admin.reject-account', $user->id) }}" class="inline ml-2">
												@csrf
												<button type="submit" class="px-3 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700">Reject</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div>

		<!-- Customers -->
		
	</div>
</div>
@endsection






