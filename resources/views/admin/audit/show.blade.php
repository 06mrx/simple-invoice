@extends('layouts.admin')
@section('title', 'Detail Audit')
@section('page-title', 'Lihat Detail Aktivitas')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between">
                <x-ui.card-title>Detail Audit Log</x-ui.card-title>
                @php
                    $variant = match($audit->event) {
                        'created' => 'success',
                        'updated' => 'default',
                        'deleted' => 'destructive',
                        default => 'secondary',
                    };
                @endphp
                <x-ui.badge :variant="$variant">{{ ucfirst($audit->event) }}</x-ui.badge>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary">
                            <svg class="h-5 w-5 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">User</p>
                            <p class="text-sm font-medium">{{ optional($audit->user)->name ?? 'System' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary">
                            <svg class="h-5 w-5 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tabel & ID</p>
                            <p class="text-sm font-medium">{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary">
                            <svg class="h-5 w-5 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">IP Address</p>
                            <p class="text-sm font-medium">{{ $audit->ip_address }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary">
                            <svg class="h-5 w-5 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tanggal & Waktu</p>
                            <p class="text-sm font-medium">{{ $audit->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-muted-foreground">{{ $audit->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <div class="grid grid-cols-1 gap-6 {{ $audit->event !== 'created' ? 'lg:grid-cols-2' : '' }}">
            @if($audit->event !== 'created')
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>Data Sebelum</x-ui.card-title>
                    </x-ui.card-header>
                    <x-ui.card-content>
                        @if(empty($audit->old_values))
                            <p class="text-sm text-muted-foreground">Tidak ada data sebelumnya</p>
                        @else
                            <div class="space-y-3">
                                @foreach($audit->old_values as $key => $value)
                                    <div class="flex gap-2">
                                        <span class="w-1/3 text-sm font-medium text-muted-foreground">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="w-2/3 break-words text-sm">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-ui.card-content>
                </x-ui.card>
            @endif

            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ $audit->event === 'created' ? 'Data Dibuat' : 'Data Sesudah' }}</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    @if(empty($audit->new_values))
                        <p class="text-sm text-muted-foreground">Data telah dihapus</p>
                    @else
                        <div class="space-y-3">
                            @foreach($audit->new_values as $key => $value)
                                <div class="flex gap-2">
                                    <span class="w-1/3 text-sm font-medium text-muted-foreground">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="w-2/3 break-words text-sm">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card-content>
            </x-ui.card>
        </div>

        <x-ui.card>
            <x-ui.card-header>
                <x-ui.button type="button" variant="ghost" class="flex w-full items-center justify-between text-left h-auto px-0 py-0 hover:bg-transparent" onclick="toggleRawData()">
                    <x-ui.card-title>Raw Data (JSON)</x-ui.card-title>
                    <svg id="rawDataIcon" class="h-5 w-5 text-muted-foreground transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </x-ui.button>
            </x-ui.card-header>
            <div id="rawDataContent" class="hidden">
                <x-ui.card-content class="space-y-4">
                    @if($audit->event !== 'created' && !empty($audit->old_values))
                        <div>
                            <h4 class="mb-2 text-sm font-medium text-muted-foreground">Data Sebelum (JSON):</h4>
                            <pre class="overflow-x-auto rounded-lg border bg-muted p-4 text-xs"><code>{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    @endif
                    @if(!empty($audit->new_values))
                        <div>
                            <h4 class="mb-2 text-sm font-medium text-muted-foreground">Data Sesudah (JSON):</h4>
                            <pre class="overflow-x-auto rounded-lg border bg-muted p-4 text-xs"><code>{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    @endif
                </x-ui.card-content>
            </div>
        </x-ui.card>

        <div class="flex justify-end">
            <x-ui.button variant="outline" href="{{ route('admin.audit.index') }}">Kembali</x-ui.button>
        </div>
    </div>

    <script>
        function toggleRawData() {
            const content = document.getElementById('rawDataContent');
            const icon = document.getElementById('rawDataIcon');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
@endsection