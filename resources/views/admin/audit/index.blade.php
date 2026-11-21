@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'Kelola Log Aktivitas')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Audit Logs</h2>
                <p class="text-muted-foreground">Riwayat aktivitas sistem.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form action="{{ route('admin.audit.index') }}" method="GET" class="relative w-full sm:w-64">
                    <x-ui.input name="search" value="{{ request('search') }}" placeholder="Cari event, pengguna, tabel..." class="pr-8" />
                    <x-ui.button type="submit" variant="ghost" size="icon" class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 text-muted-foreground hover:text-foreground">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </x-ui.button>
                </form>
                <form action="{{ route('admin.audit.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="per_page" class="text-xs text-muted-foreground">Show</label>
                    <x-ui.select name="per_page" id="per_page" onchange="this.form.submit()" class="w-20">
                        @foreach ([10, 20, 50, 100] as $val)
                            <option value="{{ $val }}" {{ request('per_page', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </x-ui.select>
                </form>
            </div>
        </div>

        <x-ui.card>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>User</x-ui.table-head>
                        <x-ui.table-head>Event</x-ui.table-head>
                        <x-ui.table-head>Tabel</x-ui.table-head>
                        <x-ui.table-head>IP</x-ui.table-head>
                        <x-ui.table-head>Tanggal</x-ui.table-head>
                        <x-ui.table-head class="text-right">Aksi</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @forelse ($audits as $audit)
                        <x-ui.table-row>
                            <x-ui.table-cell class="font-medium">{{ optional($audit->user)->name ?? 'System' }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                @php
                                    $variant = match($audit->event) {
                                        'created' => 'success',
                                        'updated' => 'default',
                                        'deleted' => 'destructive',
                                        default => 'secondary',
                                    };
                                @endphp
                                <x-ui.badge :variant="$variant">{{ $audit->event }}</x-ui.badge>
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ class_basename($audit->auditable_type) }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $audit->ip_address }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ \Carbon\Carbon::parse($audit->created_at)->format('d M Y H:i:s') }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-right">
                                <x-ui.button variant="outline" size="sm" href="{{ route('admin.audit.show', $audit->id) }}">Detail</x-ui.button>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="6" class="text-center text-muted-foreground">Tidak ada log ditemukan.</x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
            <div class="p-4">{{ $audits->links() }}</div>
        </x-ui.card>
    </div>
@endsection