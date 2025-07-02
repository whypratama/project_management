<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ProjectController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // ... method index() ...
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        $user = auth()->user();
        $query = Project::with(['creator', 'organization'])->latest();
        if ($user->hasRole('Super Admin')) {
        } elseif ($user->jobTitle?->name === 'Staff') {
            $query->whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } elseif ($user->organization) {
            $assignableOrgIds = array_merge([$user->organization->id], $user->organization->getAllChildIds());
            $query->where(function ($q) use ($user, $assignableOrgIds) {
                $q->where('creator_id', $user->id)
                  ->orWhereIn('organization_id', array_unique($assignableOrgIds));
            });
        } else {
            $query->where('creator_id', $user->id);
        }
        $projects = $query->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        
        $user = auth()->user();
        
        if ($user->hasRole('Super Admin')) {
            // Super Admin bisa memilih semua organisasi
            $organizations = Organization::pluck('name', 'id');
        } elseif ($user->organization) {
            // Pengguna lain hanya bisa memilih unitnya dan unit di bawahnya
            $assignableOrgIds = array_unique(array_merge(
                [$user->organization->id], 
                $user->organization->getAllChildIds()
            ));
            
            $organizations = Organization::whereIn('id', $assignableOrgIds)->pluck('name', 'id');
        } else {
            $organizations = collect(); // Kosongkan jika user tidak punya organisasi
        }

        return view('projects.create', compact('organizations'));
    }

    // ... method store(), show(), edit(), update(), destroy() ...
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'date_range' => 'required|string',
            'status' => 'required|string',
        ]);
        $dates = explode(' - ', $request->date_range);
        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'organization_id' => $request->organization_id,
            'status' => $request->status,
            'start_date' => Carbon::createFromFormat('d-m-Y', $dates[0]),
            'end_date' => Carbon::createFromFormat('d-m-Y', $dates[1]),
            'creator_id' => auth()->id(),
        ]);
        return redirect()->route('projects.index')->with('success', 'Proyek baru berhasil dibuat.');
    }
    
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['organization', 'creator', 'tasks.assignee', 'messages.sender', 'attachments.uploader']);
        $currentUser = auth()->user();
        $assignableUsers = collect();
        if ($currentUser->hasRole('Super Admin')) {
            $assignableUsers = User::orderBy('name')->get();
        } elseif ($currentUser->organization) {
            $scopeOrgIds = array_unique(array_merge(
                [$currentUser->organization->id],
                $currentUser->organization->getAllChildIds()
            ));
            $assignableUsers = User::whereIn('organization_id', $scopeOrgIds)
                                ->orderBy('name')
                                ->get();
        }
        return view('projects.show', compact('project', 'assignableUsers'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) {
            $organizations = Organization::pluck('name', 'id');
        } elseif ($user->organization) {
            $assignableOrgIds = array_merge([$user->organization->id], $user->organization->getAllChildIds());
            $organizations = Organization::whereIn('id', array_unique($assignableOrgIds))->pluck('name', 'id');
        } else {
            $organizations = collect();
        }
        return view('projects.edit', compact('project', 'organizations'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);
        $project->update($request->all());
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus.');
    }
}
