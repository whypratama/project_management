@extends('layouts.main')

@section('header')
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <title>Manajemen Pengguna</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
@endsection

@section('content')

<div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-8">Manajemen Pengguna</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Pengguna</li>
                    </ol>
                </nav>
            </div>
            <div class="col-3">
                <div class="text-center mb-n5">
                    <img src="{{ asset('assets/images/breadcrumb/ChatBc.png') }}" alt="" class="img-fluid mb-n4" />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Daftar Pengguna</h2>
    {{-- Ini akan memanggil UserPolicy@create --}}
    @can('create', App\Models\User::class)
        <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah Pengguna Baru</a>
    @endcan
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive mb-4 border rounded-1">
        <table class="table text-nowrap mb-0 align-middle">
          <thead class="text-dark fs-4">
            <tr>
              <th><h6 class="fs-4 fw-semibold mb-0">Nama</h6></th>
              <th><h6 class="fs-4 fw-semibold mb-0">Email</h6></th>
              <th><h6 class="fs-4 fw-semibold mb-0">Peran</h6></th>
              <th><h6 class="fs-4 fw-semibold mb-0">Unit Organisasi</h6></th>
              <th class="text-end"><h6 class="fs-4 fw-semibold mb-0">Aksi</h6></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach($user->roles as $role)
                    {{-- Kode ini sudah benar untuk Spatie --}}
                    <span class="badge bg-primary">{{ $role->name }}</span>
                  @endforeach
                </td>
                {{-- PENYESUAIAN DI SINI: organizationalUnit -> organization --}}
                <td>{{ $user->organization->name ?? 'N/A' }}</td>
                <td class="text-end">
                    {{-- Baris ini akan memanggil UserPolicy@view --}}
                    @can('view', $user)
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info text-white me-1">Lihat</a>
                    @endcan
                    {{-- Baris ini akan memanggil UserPolicy@update --}}
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                    @endcan
                    {{-- Baris ini akan memanggil UserPolicy@delete --}}
                    @can('delete', $user)
                        <form id="user-delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteUser({{ $user->id }})">Hapus</button>
                        </form>
                    @endcan
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Tidak ada pengguna yang ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{-- Link paginasi --}}
      <div class="mt-4">{{ $users->links() }}</div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    function confirmDeleteUser(userId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`user-delete-form-${userId}`).submit();
            }
        })
    }
</script>
@endpush