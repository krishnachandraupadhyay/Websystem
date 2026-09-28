<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSection extends Model
{
    protected $fillable = [
        'admin_id',
        'section_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
