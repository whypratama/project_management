<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;
use App\Models\CalendarEvent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

        public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil ID user bawahan jika ada relasi job_title bawahan
        $subordinateIds = optional(optional($user->jobTitle)->subordinates)->pluck('user_id')?->toArray() ?? [];

        $query = Project::with('tasks')
            ->where('organization_id', $user->organization_id)
            ->where(function ($q) use ($user, $subordinateIds) {
                if ($user->hasRole('Super Admin')) {
                    // Tidak ada filter tambahan untuk Super Admin
                } elseif ($user->jobTitle?->name === 'Staff') {
                    $q->whereHas('tasks', function ($tq) use ($user) {
                        $tq->where('user_id', $user->id);
                    });
                } elseif ($user->organization) {
                    $assignableOrgIds = array_merge([$user->organization->id], $user->organization->getAllChildIds());
                    $q->where(function ($inner) use ($user, $assignableOrgIds) {
                        $inner->where('creator_id', $user->id)
                              ->orWhereIn('organization_id', array_unique($assignableOrgIds));
                    });
                } else {
                    $q->where('creator_id', $user->id);
                }
            })
            ->when($request->filled('search_project'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_project . '%');
            })
            ->when($request->filled('project_status'), function ($q) use ($request) {
                $q->where('status', $request->project_status);
            });

        $projects = $query->get();

        $tasks = Task::with('project')
            ->where(function ($q) use ($user, $subordinateIds) {
                $q->where('user_id', $user->id);
                if (!empty($subordinateIds)) {
                    $q->orWhereIn('user_id', $subordinateIds);
                }
            })
            ->whereHas('project', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->when($request->filled('search_task'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search_task . '%');
            })
            ->when($request->filled('task_status'), function ($q) use ($request) {
                $q->where('status', $request->task_status);
            })
            ->orderBy('due_date')
            ->get();

        return view('dashboard', compact('projects', 'tasks'));
    }


    public function calendarEvents()
    {
        $user = Auth::user();

        $taskEvents = Task::whereHas('project', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->get(['id', 'title', 'due_date as start']);

        $customEvents = CalendarEvent::where('user_id', $user->id)
            ->get(['id', 'title', 'start', 'end', 'level']);

        return response()->json($taskEvents->merge($customEvents));
    }

    public function storeCalendarEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'level' => 'nullable|string'
        ]);

        $event = CalendarEvent::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'level' => $request->level ?? 'Primary'
        ]);

        return response()->json($event);
    }
}
