<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentField extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_id',
        'field_name',
        'field_label',
        'field_type',
        'placeholder',
        'default_value',
        'help_text',
        'is_required',
        'is_active',
        'sort_order',
        'options',
        'validation_rules',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
        'options'     => 'array',
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get normalized options array for select fields:
     * returns array of ['value' => '...', 'label' => '...']
     */
    public function getFormattedOptionsAttribute(): array
    {
        $opts = $this->options;
        if (empty($opts)) {
            return [];
        }

        if (is_string($opts)) {
            $decoded = json_decode($opts, true);
            $opts = is_array($decoded) ? $decoded : [];
        }

        $formatted = [];
        foreach ($opts as $key => $item) {
            if (is_array($item)) {
                $val = $item['value'] ?? $item['val'] ?? $key;
                $lbl = $item['label'] ?? $item['name'] ?? $val;
                $formatted[] = ['value' => (string)$val, 'label' => (string)$lbl];
            } else {
                $formatted[] = ['value' => (string)$key, 'label' => (string)$item];
            }
        }

        return $formatted;
    }
}
