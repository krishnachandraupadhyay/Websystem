<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        'component_name',
        'component_title',
        'component_slug',
        'description',
        'status',
        'is_subcomponent',
        'is_multiple',
    ];

    protected $casts = [
        'status'          => 'boolean',
        'is_subcomponent' => 'boolean',
        'is_multiple'     => 'boolean',
    ];

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_components')
                    ->withPivot('status', 'is_multiple')
                    ->withTimestamps();
    }

    public function subcomponents()
    {
        return $this->belongsToMany(Component::class, 'component_subcomponents', 'parent_component_id', 'sub_component_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function parentComponents()
    {
        return $this->belongsToMany(Component::class, 'component_subcomponents', 'sub_component_id', 'parent_component_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function fields()
    {
        return $this->hasMany(ComponentField::class)->orderBy('sort_order', 'asc');
    }

    public function activeFields()
    {
        return $this->hasMany(ComponentField::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc');
    }
}
