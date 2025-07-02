<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan ini
use Illuminate\Foundation\Validation\ValidatesRequests; // Tambahkan ini
use Illuminate\Routing\Controller as BaseController; // Ubah ini

class JobTitleController extends BaseController // Ubah ini
{
    use AuthorizesRequests, ValidatesRequests; // Tambahkan ini

    public function index()
    {
        $this->authorize('viewAny', JobTitle::class);
        $jobTitles = JobTitle::latest()->paginate(10);
        return view('job_titles.index', compact('jobTitles'));
    }

    public function create()
    {
        $this->authorize('create', JobTitle::class);
        return view('job_titles.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', JobTitle::class);
        $request->validate(['name' => 'required|string|max:255|unique:job_titles']);
        JobTitle::create($request->only('name'));
        return redirect()->route('job_titles.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(JobTitle $jobTitle)
    {
        $this->authorize('update', $jobTitle);
        return view('job_titles.edit', compact('jobTitle'));
    }

    public function update(Request $request, JobTitle $jobTitle)
    {
        $this->authorize('update', $jobTitle);
        $request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('job_titles')->ignore($jobTitle->id)]]);
        $jobTitle->update($request->only('name'));
        return redirect()->route('job_titles.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(JobTitle $jobTitle)
    {
        $this->authorize('delete', $jobTitle);
        $jobTitle->delete();
        return redirect()->route('job_titles.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
