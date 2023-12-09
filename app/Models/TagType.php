<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagType extends Model
{
    use HasFactory;

    protected $fillable = ['type_name', 'slug',];
    public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
