@extends('layouts.main')

@section('header')
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <title>Edit Proyek: {{ $project->name }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Formulir Edit Proyek: {{ $project->name }}</h5>
            <form action="{{ route('projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Proyek</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $project->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="organization_id" class="form-label">Unit Organisasi</label>
                    <select name="organization_id" id="organization_id" class="form-control" required>
                        @foreach ($organizations as $id => $name)
                            <option value="{{ $id }}" @selected(old('organization_id', $project->organization_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="pending" @selected(old('status', $project->status) == 'pending')>Tertunda</option>
                        <option value="in_progress" @selected(old('status', $project->status) == 'in_progress')>Dalam Proses</option>
                        <option value="completed" @selected(old('status', 'in_progress') == 'completed')>Selesai</option>
                        <option value="cancelled" @selected(old('status', $project->status) == 'cancelled')>Dibatalkan</option>
                    </select>
                </div>
                <div class="mt-4">
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Proyek</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
