@extends('layouts.main')



@section('header')
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <title>Detail Tugas: {{ $task->name }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
@endsection

@section('content')

<div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-8">Detail Tugas</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('projects.index') }}">Proyek</a></li>
                        <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('projects.show', $task->project->id) }}">{{ Str::limit($task->project->name, 20) }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $task->name }}</li>
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

<div class="card">
    <div class="card-header text-bg-primary"><h5 class="mb-0 text-white">Informasi Tugas</h5></div>
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">{{ $task->name }}</h5>
        <p>{{ $task->description }}</p>
        <div class="row mt-4">
            <div class="col-md-6 mb-2"><strong>Ditugaskan kepada:</strong> {{ $task->assignee->name ?? 'N/A' }}</div>
            <div class="col-md-6 mb-2"><strong>Dibuat oleh:</strong> {{ $task->creator->name ?? 'N/A' }}</div>
            <div class="col-md-6 mb-2"><strong>Batas Waktu:</strong> {{ $task->due_date?->format('d M Y') ?? '-' }}</div>
            <div class="col-md-6 mb-2"><strong>Status:</strong> <span class="badge bg-primary">{{ $task->status }}</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#files-tab" role="tab">File ({{ $task->attachments->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#chat-tab" role="tab">Diskusi ({{ $task->messages->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#timeline-tab" role="tab">Timeline</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active p-3" id="files-tab" role="tabpanel">
                @can('upload', $task)
                <div class="card card-body mb-4">
                    <h4 class="card-title">Unggah File Tugas</h4>
                    <form action="{{ route('tasks.attachments.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                         @csrf
                        <div class="input-group">
                            <input class="form-control" type="file" name="file" required>
                            <button type="submit" class="btn btn-primary">Unggah</button>
                        </div>
                    </form>
                </div>
                @endcan

                <h4 class="card-title fw-semibold">Daftar File Tugas</h4>
                @forelse ($task->attachments as $file)
                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                    <div><i class="ti ti-file-text fs-6"></i></div>
                    <div>
                        <h6 class="mb-0"><a href="{{ route('attachments.download', $file->id) }}">{{ $file->file_name }}</a></h6>
                        <span class="fs-2">oleh: {{ $file->uploader->name ?? 'N/A' }}</span>
                    </div>
                    <div class="ms-auto"><span class="fs-2">{{ $file->created_at->format('d M Y') }}</span></div>
                    @can('delete', $file)
                    <div class="ms-2">
                         <form action="{{ route('attachments.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Hapus file ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="border-0 bg-transparent text-dark p-0"><i class="ti ti-trash"></i></button>
                        </form>
                    </div>
                    @endcan
                </div>
                @empty
                    <p class="text-center text-muted py-3">Belum ada file.</p>
                @endforelse
            </div>
            <div class="tab-pane p-3" id="chat-tab" role="tabpanel">
                <div class="card overflow-hidden chat-application">
                    <div class="d-flex">
                        <div class="w-100">
                            <div class="chat-container h-100 w-100">
                                <div class="chat-box-inner-part h-100">
                                    <div class="chat-box-inner" style="height: 500px; overflow-y: auto;">
                                        <div class="chat-list chat active-chat p-3">
                                            @forelse ($task->messages->sortBy('created_at') as $message)
                                                @if ($message->sender_id === auth()->id())
                                                    <div class="hstack gap-3 align-items-start mb-7 justify-content-end">
                                                        <div class="text-end">
                                                            <div class="p-2 bg-info-subtle text-dark rounded-1 d-inline-block fs-3">{{ $message->content }}</div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="hstack gap-3 align-items-start mb-7 justify-content-start">
                                                        <div>
                                                            <h6 class="fs-2 text-muted">{{ $message->sender->name ?? 'Pengguna' }}</h6>
                                                            <div class="p-2 text-bg-light rounded-1 d-inline-block text-dark fs-3">{{ $message->content }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @empty
                                                <p class="text-center text-muted">Mulai diskusi tugas.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="px-3 py-2 border-top chat-send-message-footer">
                                        <form action="{{ route('projects.messages.store', $project->id) }}" method="POST" class="d-flex align-items-center justify-content-between">
                                            @csrf
                                            <input type="text" name="content" class="form-control message-type-box text-muted border-0 p-0 ms-2" placeholder="Ketik pesan Anda..." required>
                                            <button type="submit" class="btn btn-primary"><i class="ti ti-send"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane p-3" id="timeline-tab" role="tabpanel">
                <h4 class="card-title fw-semibold">Timeline Tugas</h4>
                <ul class="timeline-widget mb-0 position-relative mt-4">
                     @php
                        $timelineEvents = collect();
                        $timelineEvents->push(['date' => $task->created_at, 'type' => 'Tugas', 'title' => 'Tugas Dibuat', 'description' => 'Dibuat oleh ' . ($task->creator->name ?? 'N/A')]);
                        if ($task->due_date) {
                            $timelineEvents->push(['date' => $task->due_date, 'type' => 'Tugas', 'title' => 'Batas Waktu Tugas', 'description' => 'Tugas diharapkan selesai.']);
                        }
                        foreach ($task->attachments as $file) {
                            $timelineEvents->push(['date' => $file->created_at, 'type' => 'File', 'title' => 'File Diunggah: ' . Str::limit($file->file_name, 25), 'description' => 'Diunggah oleh ' . ($file->uploader->name ?? 'N/A')]);
                        }
                        foreach ($task->messages as $message) {
                            $timelineEvents->push(['date' => $message->created_at, 'type' => 'Diskusi', 'title' => 'Pesan Baru', 'description' => 'Dari ' . ($message->sender->name ?? 'N/A')]);
                        }
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
                            <div class="timeline-desc fs-3 text-muted mt-n1">Timeline tugas akan muncul di sini.</div>
                         </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
