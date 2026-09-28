<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SectionComponentSubcomponent;

class Section extends Model
{
    protected $fillable = [
        'section_name',
        'section_title',
        'section_slug',
        'description',
        'status',
        'is_subsection',
    ];

    protected $casts = [
        'status'        => 'boolean',
        'is_subsection' => 'boolean',
    ];

    public function components()
    {
        return $this->belongsToMany(Component::class, 'section_components')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function subsections()
    {
        return $this->hasMany(SubSection::class)->orderBy('order');
    }

    /**
     * Per-section sub-component overrides.
     * Use ->where('component_id', $id) to get sub-components for a specific component.
     */
    public function sectionComponentSubcomponents()
    {
        return $this->hasMany(SectionComponentSubcomponent::class);
    }
}
