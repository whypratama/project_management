@extends('layouts.main')

@section('header')
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <title>Manajemen Project</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>
@endsection

@section('content')

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Buat Proyek Baru</h4>
    <p class="card-subtitle mb-0">Untuk membuat proyek baru, silakan isi form di bawah ini.</p>
  </div>
  <form action="{{ route('projects.store') }}" method="POST" class="form-horizontal r-separator">
    @csrf
    <div class="card-body">

      {{-- Nama Proyek --}}
      <div class="form-group mb-0 row">
        <label for="name" class="col-3 text-end col-form-label">Nama Proyek</label>
        <div class="col-9 border-start pb-2 pt-2">
          <input type="text" name="name" id="name" class="form-control" placeholder="Nama Proyek" required>
        </div>
      </div>

      {{-- Deskripsi --}}
      <div class="form-group mb-0 row">
        <label for="description" class="col-3 text-end col-form-label">Deskripsi</label>
        <div class="col-9 border-start pb-2 pt-2">
          <textarea name="description" id="description" rows="3" class="form-control" placeholder="Deskripsi Proyek"></textarea>
        </div>
      </div>

      {{-- Unit Organisasi --}}
      <div class="form-group mb-0 row">
        <label for="organization_id" class="col-3 text-end col-form-label">Unit Organisasi</label>
        <div class="col-9 border-start pb-2 pt-2">
          {{-- PERBAIKAN DI SINI: Menggunakan variabel $organizations --}}
          <select name="organization_id" id="organization_id" class="form-control" required>
            <option value="">Pilih Unit Organisasi</option>
            @foreach ($organizations as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Rentang Tanggal --}}
      <div class="form-group mb-0 row">
        <label for="date_range" class="col-3 text-end col-form-label">Rentang Tanggal</label>
        <div class="col-9 border-start pb-2 pt-2">
          <input type="text" class="form-control" name="date_range" id="date_range" />
        </div>
      </div>

      {{-- Status --}}
      <div class="form-group mb-0 row">
        <label for="status" class="col-3 text-end col-form-label">Status</label>
        <div class="col-9 border-start pb-2 pt-2">
          <select name="status" id="status" class="form-control" required>
            <option value="pending">Tertunda</option>
            <option value="in_progress">Dalam Proses</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Dibatalkan</option>
          </select>
        </div>
      </div>

    </div>

    {{-- Tombol --}}
    <div class="p-3 border-top">
      <div class="form-group mb-0 text-end">
        <a href="{{ route('projects.index') }}" class="btn bg-danger-subtle text-danger me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Buat Proyek</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        $('input[name="date_range"]').daterangepicker({
            opens: 'left',
            locale: { format: 'DD-MM-YYYY' }
        });
    });
</script>
@endpush
