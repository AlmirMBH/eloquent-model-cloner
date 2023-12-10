<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Post extends Model
{
    use HasFactory;

   protected $fillable = ['title', 'body'];
   public array $cloneableRelations = ['comments', 'image'];
   public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

   protected $hidden = [
       'created_at',
       'updated_at',
       'deleted_at',
   ];


   public function authors(): BelongsToMany
   {
       return $this->belongsToMany(Author::class)
           ->withTimestamps();
   }

   public function comments(): HasMany
   {
       return $this->hasMany(Comment::class);
   }

   public function image(): MorphTo
    {
        return $this->morphTo();
    }

   public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
