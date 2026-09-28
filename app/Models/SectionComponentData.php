<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionComponentData extends Model
{
    use HasFactory;

    protected $table = 'section_component_data';

    protected $fillable = [
        'section_id',
        'component_id',
        'sub_component_id',
        'component_field_id',
        'instance_index',
        'field_name',
        'content_value',
        'extra_value',
        'file_path',
    ];

    protected $casts = [
        'instance_index' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    public function subComponent()
    {
        return $this->belongsTo(Component::class, 'sub_component_id');
    }

    public function field()
    {
        return $this->belongsTo(ComponentField::class, 'component_field_id');
    }
}
