<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Tambahkan ini
use Illuminate\Foundation\Validation\ValidatesRequests; // Tambahkan ini
use Illuminate\Routing\Controller as BaseController; // Ubah ini

class OrganizationController extends BaseController // Ubah ini
{
    use AuthorizesRequests, ValidatesRequests; // Tambahkan ini

    public function index()
    {
        $this->authorize('viewAny', Organization::class);
        $organizations = Organization::with('parent')->latest()->paginate(10);
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        $this->authorize('create', Organization::class);
        $organizations = Organization::pluck('name', 'id');
        return view('organizations.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Organization::class);
        $request->validate([
            'name' => 'required|string|max:255|unique:organizations',
            'parent_id' => 'nullable|exists:organizations,id',
        ]);

        Organization::create($request->all());

        return redirect()->route('organizations.index')->with('success', 'Unit Organisasi berhasil ditambahkan.');
    }

    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);
        $organizations = Organization::where('id', '!=', $organization->id)->pluck('name', 'id');
        return view('organizations.edit', compact('organization', 'organizations'));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('organizations')->ignore($organization->id)],
            'parent_id' => 'nullable|exists:organizations,id',
        ]);

        $organization->update($request->all());

        return redirect()->route('organizations.index')->with('success', 'Unit Organisasi berhasil diperbarui.');
    }

    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);
        $organization->delete();
        return redirect()->route('organizations.index')->with('success', 'Unit Organisasi berhasil dihapus.');
    }
}
