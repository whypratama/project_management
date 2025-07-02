@php
// Helper untuk menentukan ikon dan warna badge timeline
function getTimelineDetails($type) {
    switch (strtolower($type)) {
        case 'proyek': return ['icon' => 'ti ti-flag-checkered', 'badge_class' => 'border-primary'];
        case 'tugas': return ['icon' => 'ti ti-list-check', 'badge_class' => 'border-info'];
        case 'file': return ['icon' => 'ti ti-file-text', 'badge_class' => 'border-purple'];
        case 'diskusi': return ['icon' => 'ti ti-message-dots', 'badge_class' => 'border-warning'];
        default: return ['icon' => 'ti ti-clock', 'badge_class' => 'border-secondary'];
    }
}
@endphp

@extends('layouts.main')

@section('header')
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <title>Detail Proyek: {{ $project->name }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
@endsection


@section('content')

<div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-8">Detail Proyek</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('projects.index') }}">Proyek</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $project->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-3">
                <div class="text-center mb-n5"><img src="{{ asset('assets/images/breadcrumb/ChatBc.png') }}" alt="" class="img-fluid mb-n4" /></div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

<div class="card">
    <div class="card-header text-bg-primary"><h5 class="mb-0 text-white">Informasi Proyek</h5></div>
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">{{ $project->name }}</h5>
        <p>{{ $project->description }}</p>
        <div class="row mt-4">
            <div class="col-md-6 mb-2"><strong>Unit Organisasi:</strong> {{ $project->organization->name ?? 'N/A' }}</div>
            <div class="col-md-6 mb-2"><strong>Dibuat oleh:</strong> {{ $project->creator->name ?? 'N/A' }}</div>
            <div class="col-md-6 mb-2"><strong>Tanggal Mulai:</strong> {{ $project->start_date?->format('d M Y') ?? '-' }}</div>
            <div class="col-md-6 mb-2"><strong>Tanggal Selesai:</strong> {{ $project->end_date?->format('d M Y') ?? '-' }}</div>
            <div class="col-md-6 mb-2"><strong>Status:</strong> <span class="badge bg-primary">{{ $project->status }}</span></div>
        </div>
    </div>
    <div class="form-actions border-top card-body">
        @can('update', $project)
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary"><i class="ti ti-edit fs-5 me-1"></i> Edit Proyek</a>
        @endcan
        @can('delete', $project)
            <form id="delete-project-form" action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn bg-danger-subtle text-danger ms-2" onclick="confirmDelete()">Hapus Proyek</button>
            </form>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tasks-tab" role="tab">Tugas ({{ $project->tasks->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#files-tab" role="tab">File ({{ $project->attachments->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#chat-tab" role="tab">Diskusi ({{ $project->messages->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#timeline-tab" role="tab">Timeline</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active p-3" id="tasks-tab" role="tabpanel">
                @can('addTask', $project)
                <div class="card card-body mb-4">
                    <h4 class="card-title">Tambah Tugas Baru</h4>
                    <p class="card-subtitle mb-3">Isi detail tugas yang akan ditambahkan ke proyek ini.</p>
                    <form action="{{ route('projects.tasks.store', $project->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3"><input type="text" name="name" class="form-control" required placeholder="Nama Tugas"></div>
                            <div class="col-md-6 mb-3">
                                <select name="assigned_to" class="form-select">
                                    <option value="">-- Tugaskan kepada --</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->jobTitle->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3"><textarea name="description" rows="2" class="form-control" placeholder="Deskripsi Tugas"></textarea></div>
                        <div class="mb-3"><input type="date" name="due_date" class="form-control"></div>
                        <div class="text-end"><button type="submit" class="btn btn-primary">Tambah Tugas</button></div>
                    </form>
                </div>
                @endcan

                <h4 class="card-title fw-semibold">Daftar Tugas</h4>
                @forelse ($project->tasks as $task)
                    <div class="py-3 border-bottom">
                        <div id="task-view-{{ $task->id }}" class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $task->name }}</h6>
                                <span class="fs-2 text-muted">{{ Str::limit($task->description, 60) }}</span>
                            </div>
                            <div class="ms-auto text-end">
                                <h6 class="mb-0 fw-semibold">{{ $task->assignee->name ?? 'Belum Ditugaskan' }}</h6>
                                <span class="fs-2">{{ $task->due_date?->format('d M Y') ?? 'Tanpa Batas Waktu' }}</span>
                            </div>
                            <div class="ms-3"><span class="badge bg-primary">{{ $task->status }}</span></div>
                            @can('update', $task)
                                <a href="javascript:void(0)" onclick="toggleEdit('{{ $task->id }}')" class="btn btn-sm btn-light ms-2">Edit</a>
                            @endcan
                        </div>
                        <div id="task-edit-{{ $task->id }}" style="display:none;" class="mt-3 bg-light-subtle p-3 rounded">
                            <h6 class="fw-semibold mb-3">Edit Tugas</h6>
                            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-2"><input type="text" name="name" value="{{ $task->name }}" class="form-control form-control-sm" required></div>
                                <div class="mb-2"><textarea name="description" rows="2" class="form-control form-control-sm">{{ $task->description }}</textarea></div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                         <select name="assigned_to" class="form-select form-select-sm">
                                            @foreach ($assignableUsers as $user)
                                                <option value="{{ $user->id }}" @selected($task->assigned_to == $user->id)>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2"><input type="date" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 mb-2">
                                        <select name="status" class="form-select form-select-sm" required @if(auth()->id() !== $task->creator_id) disabled @endif>
                                            <option value="pending" @selected($task->status == 'pending')>Tertunda</option>
                                            <option value="in_progress" @selected($task->status == 'in_progress')>Dalam Proses</option>
                                            <option value="completed" @selected($task->status == 'completed')>Selesai</option>
                                        </select>
                                        @if(auth()->id() !== $task->creator_id)
                                            <input type="hidden" name="status" value="{{ $task->status }}">
                                            <small class="text-muted">Hanya pembuat tugas yang bisa mengubah status.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                                    <button type="button" onclick="toggleEdit('{{ $task->id }}')" class="btn btn-sm btn-secondary">Batal</button>
                                    @can('delete', $task)
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ms-2">Hapus</button>
                                    </form>
                                    @endcan
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada tugas untuk proyek ini.</div>
                @endforelse
            </div>
            <div class="tab-pane p-3" id="files-tab" role="tabpanel"><p class="text-muted">Fitur File akan kita aktifkan di langkah selanjutnya.</p></div>
            <div class="tab-pane p-3" id="chat-tab" role="tabpanel"><p class="text-muted">Fitur Diskusi akan kita aktifkan di langkah selanjutnya.</p></div>
            <div class="tab-pane p-3" id="timeline-tab" role="tabpanel">
                <div class="card-body">
                    <h4 class="card-title fw-semibold">Timeline Proyek</h4>
                    <ul class="timeline-widget mb-0 position-relative mt-4">
                         @php
                            $timelineEvents = collect();
                            if ($project->start_date) {
                                $timelineEvents->push(['date' => $project->start_date, 'type' => 'Proyek', 'title' => 'Proyek Dimulai', 'description' => 'Proyek "' . $project->name . '" telah dimulai.']);
                            }
                            if ($project->end_date) {
                                $timelineEvents->push(['date' => $project->end_date, 'type' => 'Proyek', 'title' => 'Target Selesai Proyek', 'description' => 'Target penyelesaian proyek.']);
                            }
                            foreach ($project->tasks as $task) {
                                if($task->created_at) {
                                    $timelineEvents->push(['date' => $task->created_at, 'type' => 'Tugas', 'title' => 'Tugas Ditambahkan: ' . $task->name, 'description' => 'Ditugaskan kepada ' . ($task->assignee->name ?? 'N/A')]);
                                }
                                if ($task->status == 'completed' && $task->updated_at) {
                                    $timelineEvents->push(['date' => $task->updated_at, 'type' => 'Tugas', 'title' => 'Tugas Selesai: ' . $task->name, 'description' => 'Tugas telah ditandai selesai.']);
                                }
                            }
                            // PERBAIKAN: Gunakan ->values() setelah sortByDesc untuk mereset key array
                            $timelineEvents = $timelineEvents->whereNotNull('date')->sortByDesc('date')->values();
                        @endphp

                        @forelse ($timelineEvents as $event)
                             @php $details = getTimelineDetails($event['type']); @endphp
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-time text-dark flex-shrink-0 text-end">{{ \Carbon\Carbon::parse($event['date'])->format('d M Y') }}</div>
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 {{ $details['badge_class'] }} flex-shrink-0 my-8"></span>
                                    @if(!$loop->last)<span class="timeline-badge-border d-block flex-shrink-0"></span>@endif
                                </div>
                                <div class="timeline-desc fs-3 text-dark mt-n1">
                                   <strong>{{ $event['title'] }}</strong> - <span class="text-muted">{{ $event['description'] }}</span>
                                </div>
                            </li>
                        @empty
                             <li class="timeline-item d-flex position-relative overflow-hidden">
                                 <div class="timeline-badge-wrap d-flex flex-column align-items-center"><span class="timeline-badge border-2 border-secondary flex-shrink-0 my-8"></span></div>
                                <div class="timeline-desc fs-3 text-muted mt-n1">Timeline proyek akan muncul di sini.</div>
                             </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    function toggleEdit(taskId) {
        // Sembunyikan semua form edit lain dan tampilkan semua view lain
        document.querySelectorAll('[id^="task-edit-"]').forEach(el => {
            if (el.id !== 'task-edit-' + taskId) {
                el.style.display = 'none';
            }
        });
        document.querySelectorAll('[id^="task-view-"]').forEach(el => {
             if (el.id !== 'task-view-' + taskId) {
                el.style.display = 'flex';
            }
        });

        // Toggle elemen yang dipilih
        const viewDiv = document.getElementById('task-view-' + taskId);
        const editDiv = document.getElementById('task-edit-' + taskId);

        if (editDiv.style.display === 'none' || editDiv.style.display === '') {
            viewDiv.style.display = 'none';
            editDiv.style.display = 'block';
        } else {
            viewDiv.style.display = 'flex';
            editDiv.style.display = 'none';
        }
    }

    function confirmDelete() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan data proyek ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-project-form').submit();
            }
        })
    }
</script>
@endpush
