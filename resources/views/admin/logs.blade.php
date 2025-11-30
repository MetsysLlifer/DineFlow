@extends('layouts.base')
@section('title','Activity Logs')
@section('content')
<x-header />
<div class="min-h-screen p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
    </div>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.logs',['tab'=>'users']) }}" class="px-3 py-2 rounded text-sm {{ $tab==='users' ? 'bg-indigo-600 text-white' : 'bg-white shadow' }}">Users' Activities</a>
        <a href="{{ route('admin.logs',['tab'=>'food']) }}" class="px-3 py-2 rounded text-sm {{ $tab==='food' ? 'bg-indigo-600 text-white' : 'bg-white shadow' }}">Food</a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-3 py-2">Time</th>
                    <th class="text-left px-3 py-2">User</th>
                    <th class="text-left px-3 py-2">Action</th>
                    <th class="text-left px-3 py-2">Meta</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-b">
                    <td class="px-3 py-2 text-gray-600">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-2">{{ $log->user?->name ?? 'Guest' }}</td>
                    <td class="px-3 py-2 font-medium">{{ $log->action }}</td>
                    <td class="px-3 py-2 text-gray-500">
                        @if($log->metadata)
                            <code class="text-xs">{{ json_encode($log->metadata) }}</code>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-gray-500">No logs found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
