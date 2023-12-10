<?php

namespace App\Traits;

use App;
use App\Models\Author;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

trait ModelClonerTrait
{
    private function duplicate(Model $model, Relation $relation = null): Model
    {
        $clone = $model->replicate($model->exceptColumns ?? null);

        if ($model instanceof Author && !$relation) {
            $clone->name .= ' Cloned';
        }

        $clone->save();

        if ($relation && !$relation instanceof BelongsToMany) {
            $relation->save($clone);
        }

        if (!empty($model->cloneableRelations)) {
            $this->cloneRelations($model, $clone);
        }

        return $clone;
    }

    private function cloneRelations(Model $model, Model $clone): void
    {
        foreach ($model->cloneableRelations as $relationName) {
            $relation = call_user_func([$model, $relationName]);

            if ($relation instanceof BelongsToMany) {
                $this->clonePivotRelation($relation, $relationName, $clone);
            } else {
                $this->cloneDirectRelation($relation, $relationName, $clone);
            }
        }
    }

    private function clonePivotRelation(Relation $relation, string $relationName, Model $clone): void
    {
        $postIds = [];

        $relation->as('pivot')->get()->each(function ($foreign) use ($clone, $relationName, &$postIds) {
            $pivotAttributes = $this->getPivotAttributes($foreign);

            $foreignDuplicate = $this->duplicate($foreign);
            $clone->$relationName()->attach($foreignDuplicate, $pivotAttributes);
            $clone->save();

            // Special requirement (can be omitted) to update post_id in reviews table
            if ($foreign instanceof Post) {
                $postIds[$foreign->id] = $foreignDuplicate->id;
                $this->updateReviews($postIds, $clone);
            }
        });
    }

    private function cloneDirectRelation(Relation $relation, string $relationName, Model $clone): void
    {
        $relation->get()->each(function($relationObject) use ($clone, $relationName) {
            $relationType = $clone->$relationName();
            $clonedRelation = $this->duplicate($relationObject, $relationType);

            if ($relationType instanceof BelongsToMany || $relationType instanceof MorphTo) {
                $relationType->associate($clonedRelation);
                $clone->save();
            }
        });
    }

    private function getPivotAttributes(Model $foreign): array
    {
        // Retrieve all attributes from the pivot table; exclude keys you do not need
        $pivotAttributes = Arr::except(
            $foreign->pivot->getAttributes(), [
                $foreign->pivot->getRelatedKey(),
                $foreign->pivot->getForeignKey(),
                $foreign->pivot->getCreatedAtColumn(),
                $foreign->pivot->getUpdatedAtColumn()
            ]);

        // Replace the remaining attribute values with the values from the current instance
        foreach (array_keys($pivotAttributes) as $attributeKey) {
            $pivotAttributes[$attributeKey] = $foreign->pivot->getAttribute($attributeKey);
        }

        return $pivotAttributes;
    }

    private function updateReviews(array $postIds, Model $clone): void
    {
        foreach ($postIds as $originalForeignId => $duplicateForeignId) {
            Review::where([
                'post_id' => $originalForeignId,
                'author_id' => $clone->id
            ])->update([
                'post_id' => $duplicateForeignId,
            ]);
        }
    }
}
