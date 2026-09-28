<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionComponent extends Model
{
    protected $fillable = [
        'section_id',
        'component_id',
        'status',
        'is_multiple',
        'item_count',
    ];

    protected $casts = [
        'status'      => 'boolean',
        'is_multiple' => 'boolean',
        'item_count'  => 'integer',
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
