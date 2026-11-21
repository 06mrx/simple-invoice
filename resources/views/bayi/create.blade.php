@extends('layouts.admin')
@section('title', 'Tambah Bayi')
@section('page-title', 'Tambah Data Bayi Baru')

@section('content')
    <div class="mx-auto max-w-3xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Tambah Bayi</x-ui.card-title>
                <x-ui.card-description>Input data bayi baru.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('bayi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <x-ui.label for="nama">Nama</x-ui.label>
                            <x-ui.input id="nama" name="nama" value="{{ old('nama') }}" placeholder="Nama bayi" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="nik">NIK</x-ui.label>
                            <x-ui.input id="nik" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="tgl_lahir">Tanggal Lahir</x-ui.label>
                            <x-ui.input id="tgl_lahir" name="tgl_lahir" type="date" value="{{ old('tgl_lahir') }}" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label>Jenis Kelamin</x-ui.label>
                            <div class="flex gap-6 pt-2">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="jk" value="L" {{ old('jk') == 'L' ? 'checked' : '' }} class="h-4 w-4 border-input text-primary focus:ring-ring" required>
                                    Laki-laki
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="jk" value="P" {{ old('jk') == 'P' ? 'checked' : '' }} class="h-4 w-4 border-input text-primary focus:ring-ring" required>
                                    Perempuan
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="nama_ortu">Nama Orang Tua / Wali</x-ui.label>
                            <x-ui.input id="nama_ortu" name="nama_ortu" value="{{ old('nama_ortu') }}" placeholder="Nama Ayah / Nama Ibu" required />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="bb">Berat Badan (kg)</x-ui.label>
                            <x-ui.input id="bb" name="bb" type="number" step="0.1" value="{{ old('bb') }}" placeholder="3.5" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="tb">Tinggi Badan (cm)</x-ui.label>
                            <x-ui.input id="tb" name="tb" type="number" step="0.1" value="{{ old('tb') }}" placeholder="50.5" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="ll">Lingkar Lengan (cm)</x-ui.label>
                            <x-ui.input id="ll" name="ll" type="number" step="0.1" value="{{ old('ll') }}" placeholder="12.5" />
                        </div>
                        <div class="space-y-2">
                            <x-ui.label for="lk">Lingkar Kepala (cm)</x-ui.label>
                            <x-ui.input id="lk" name="lk" type="number" step="0.1" value="{{ old('lk') }}" placeholder="35.5" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <x-ui.label for="ket">Keterangan</x-ui.label>
                            <x-ui.textarea id="ket" name="ket" rows="3">{{ old('ket') }}</x-ui.textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('bayi.index') }}" type="button">Batal</x-ui.button>
                        <x-ui.button type="submit">Simpan</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nikInput = document.getElementById('nik');
            const errorSpan = document.createElement('span');
            errorSpan.id = 'nik-error';
            errorSpan.classList.add('text-red-500', 'text-sm', 'mt-1');
            nikInput.parentNode.appendChild(errorSpan);

            nikInput.addEventListener('input', function () {
                let value = nikInput.value;
                value = value.replace(/[^0-9]/g, '');
                nikInput.value = value;
                if (value.length !== 16 && value.length > 0) {
                    errorSpan.textContent = 'NIK harus terdiri dari 16 digit angka.';
                    nikInput.setCustomValidity('NIK tidak valid');
                } else {
                    errorSpan.textContent = '';
                    nikInput.setCustomValidity('');
                }
            });
        });
    </script>
@endpush