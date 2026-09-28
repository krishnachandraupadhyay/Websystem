<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionComponent extends Model
{
    protected $fillable = [
        'section_id',
        'component_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
