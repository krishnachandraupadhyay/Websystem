<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionComponentSubcomponent extends Model
{
    protected $table = 'section_component_subcomponents';

    protected $fillable = [
        'section_id',
        'component_id',
        'sub_component_id',
        'status',
        'order',
        'is_multiple',
    ];

    protected $casts = [
        'status'      => 'boolean',
        'order'       => 'integer',
        'is_multiple' => 'boolean',
    ];

    /** The parent section */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /** The component (e.g. Card) this sub-component belongs to */
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    /** The sub-component itself (e.g. Heading, Image, Paragraph) */
    public function subComponent()
    {
        return $this->belongsTo(Component::class, 'sub_component_id');
    }

    /**
     * Scope: get all sub-components for a specific section + component pair.
     */
    public function scopeForSectionComponent($query, int $sectionId, int $componentId)
    {
        return $query->where('section_id', $sectionId)
                     ->where('component_id', $componentId)
                     ->where('status', true)
                     ->orderBy('order');
    }
}
