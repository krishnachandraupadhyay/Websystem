<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $table = 'sub_sections';

    protected $fillable = [
        'section_id',
        'subsection_name',
        'subsection_title',
        'subsection_slug',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order'  => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function components()
    {
        return $this->belongsToMany(Component::class, 'sub_section_components', 'sub_section_id', 'component_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
