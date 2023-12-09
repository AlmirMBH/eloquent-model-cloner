<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'slug',];
    public array $cloneableRelations = ['tagType'];
    public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function comments(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function tagType(): HasOne
    {
        return $this->hasOne(TagType::class);
    }

}
