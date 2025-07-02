<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    /**
     * Mengambil semua ID anak dari unit organisasi ini secara rekursif.
     *
     * @return array
     */
    public function getAllChildIds(): array
    {
        $childIds = [];
        foreach ($this->children as $child) {
            $childIds[] = $child->id;
            $childIds = array_merge($childIds, $child->getAllChildIds());
        }
        return $childIds;
    }
}
