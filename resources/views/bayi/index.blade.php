@extends('layouts.admin')
@section('title', 'Data Bayi')
@section('page-title', 'Kelola Data Bayi')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Data Bayi</h2>
                <p class="text-muted-foreground">Contoh modul CRUD.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form action="{{ route('bayi.index') }}" method="GET" class="relative w-full sm:w-64">
                    <x-ui.input name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..." class="pr-8" />
                    <x-ui.button type="submit" variant="ghost" size="icon" class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 text-muted-foreground hover:text-foreground">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </x-ui.button>
                </form>
                <form action="{{ route('bayi.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="per_page" class="text-xs text-muted-foreground">Show</label>
                    <x-ui.select name="per_page" id="per_page" onchange="this.form.submit()" class="w-20">
                        @foreach ([10, 20, 50, 100] as $val)
                            <option value="{{ $val }}" {{ request('per_page', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </x-ui.select>
                </form>
                @can('create-bayi')
                    <x-ui.button href="{{ route('bayi.create') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add
                    </x-ui.button>
                @endcan
            </div>
        </div>

        <x-ui.card>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>Nama</x-ui.table-head>
                        <x-ui.table-head>NIK</x-ui.table-head>
                        <x-ui.table-head>Tgl Lahir</x-ui.table-head>
                        <x-ui.table-head>JK</x-ui.table-head>
                        <x-ui.table-head>Ortu</x-ui.table-head>
                        <x-ui.table-head>BB</x-ui.table-head>
                        <x-ui.table-head>TB</x-ui.table-head>
                        <x-ui.table-head>Status</x-ui.table-head>
                        <x-ui.table-head class="text-right">Aksi</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @forelse ($bayis as $bayi)
                        <x-ui.table-row>
                            <x-ui.table-cell class="font-medium">{{ $bayi->nama }}</x-ui.table-cell>
                            <x-ui.table-cell>{{ $bayi->nik }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ \Carbon\Carbon::parse($bayi->tgl_lahir)->format('d M Y') }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $bayi->jk == 'L' ? 'L' : 'P' }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $bayi->nama_ortu }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $bayi->bb ?? '-' }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $bayi->tb ?? '-' }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                @if ($bayi->deleted_at)
                                    <x-ui.badge variant="destructive">Dihapus</x-ui.badge>
                                @else
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @endif
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-right">
                                <div class="flex justify-end gap-2">
                                    @can('edit-bayi')
                                        <x-ui.button variant="outline" size="sm" href="{{ route('bayi.edit', $bayi) }}">Edit</x-ui.button>
                                    @endcan
                                    @can('delete-bayi')
                                        @if (!$bayi->deleted_at)
                                            <x-ui.dialog>
                                                <x-slot:trigger>
                                                    <x-ui.button variant="destructive" size="sm" type="button">Hapus</x-ui.button>
                                                </x-slot:trigger>
                                                <div class="space-y-4">
                                                    <div>
                                                        <h2 class="text-lg font-semibold">Hapus bayi?</h2>
                                                        <p class="text-sm text-muted-foreground">Tindakan ini tidak bisa dibatalkan. Hapus {{ $bayi->nama }}?</p>
                                                    </div>
                                                    <div class="flex justify-end gap-2">
                                                        <x-ui.button variant="outline" type="button" @click="open = false">Batal</x-ui.button>
                                                        <form action="{{ route('bayi.destroy', $bayi) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-ui.button variant="destructive" type="submit">Hapus</x-ui.button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </x-ui.dialog>
                                        @else
                                            <form action="{{ route('bayi.restore', $bayi->id) }}" method="POST">
                                                @csrf
                                                <x-ui.button variant="secondary" size="sm" type="submit">Pulihkan</x-ui.button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="9" class="text-center text-muted-foreground">Tidak ada data bayi.</x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
            <div class="p-4">{{ $bayis->links() }}</div>
        </x-ui.card>
    </div>
@endsection