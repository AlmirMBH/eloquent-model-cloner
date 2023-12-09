<?php

namespace App\Constants;

class ModelClonerConstants
{
    public const BELONGS_TO_MANY = 'Illuminate\Database\Eloquent\Relations\BelongsToMany';
    public const BELONGS_TO = 'Illuminate\Database\Eloquent\Relations\BelongsTo';
    public const MORPH_TO = 'Illuminate\Database\Eloquent\Relations\MorphTo';

    public const AUTHOR_MODEL = 'App\Models\Author';
    public const POST_MODEL = 'App\Models\Post';
    public const REVIEW_MODEL = 'App\Models\Review';
}
