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
</head>
@endsection

@section('content')
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Daftar Proyek</h2>
    @can('create', App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Buat Proyek Baru</a>
    @endcan
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Nama Proyek</th>
              <th>Unit Organisasi</th>
              <th>Status</th>
              <th>Tgl Mulai</th>
              <th>Tgl Selesai</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($projects as $project)
              <tr>
                <td>
                  <a href="{{ route('projects.show', $project->id) }}" class="text-primary fw-semibold">{{ $project->name }}</a>
                </td>
                <td>{{ $project->organization->name ?? 'N/A' }}</td>
                <td>
                  <span class="badge bg-primary">{{ $project->status }}</span>
                </td>
                <td>{{ $project->start_date?->format('d M Y') }}</td>
                <td>{{ $project->end_date?->format('d M Y') }}</td>
                <td>
                  @can('view', $project)
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">Lihat</a>
                  @endcan
                  @can('update', $project)
                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-primary">Edit</a>
                  @endcan
                  @can('delete', $project)
                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                  @endcan
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">Tidak ada proyek.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $projects->links() }}</div>
    </div>
  </div>
</div>
@endsection
