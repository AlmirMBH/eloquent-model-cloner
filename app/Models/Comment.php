<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    public $fillable = ['body'];
    public array $cloneableRelations = ['tags'];
    public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
}
