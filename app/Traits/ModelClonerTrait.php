<?php

namespace App\Traits;

use App;
use App\Constants\ModelClonerConstants;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

trait ModelClonerTrait
{
    public function duplicate(Model $model, Relation $relation = null): Model
    {
        $clone = $model->replicate($model->exceptColumns ?? null);

        if (!$relation && get_class($model) === ModelClonerConstants::AUTHOR_MODEL) {
            $clone->name = $clone->name . ' Cloned';
        }

        $clone->save();

        if ($relation && !is_a($relation, ModelClonerConstants::BELONGS_TO_MANY)) {
            $relation->save($clone);
        }

        if (!empty($model->cloneableRelations)) {
            $this->cloneRelations($model, $clone);
        }

        return $clone;
    }

    protected function cloneRelations(Model $model, Model $clone): void
    {
        foreach($model->cloneableRelations as $relationName) {
            $relation = call_user_func([$model, $relationName]);

            if (is_a($relation, ModelClonerConstants::BELONGS_TO_MANY)) {
                $this->duplicatePivotedRelation($relation, $relationName, $clone);
            } else
                $this->cloneDirectRelation($relation, $relationName, $clone);
        }
    }

    protected function duplicatePivotedRelation(Relation $relation, string $relationName, Model $clone): void
    {
        $postIds = [];

        $relation->as('pivot')->get()->each(function ($foreign) use ($clone, $relationName, &$postIds) {
            $pivotAttributes = Arr::except(
                $foreign->pivot->getAttributes(), [
                    $foreign->pivot->getRelatedKey(),
                    $foreign->pivot->getForeignKey(),
                    $foreign->pivot->getCreatedAtColumn(),
                    $foreign->pivot->getUpdatedAtColumn()
                ]);

            // additional columns of the pivot table, if any
            foreach (array_keys($pivotAttributes) as $attributeKey) {
                $pivotAttributes[$attributeKey] = $foreign->pivot->getAttribute($attributeKey);
            }

            // Duplicate many-to-many relations and their own relations
            $foreignDuplicate = $this->duplicate($foreign);
            $clone->$relationName()->attach($foreignDuplicate, $pivotAttributes);
            $clone->save();

            // posts are created after reviews, we need to update post_id in reviews
            if (get_class($foreign) === ModelClonerConstants::POST_MODEL) {
                $postIds[$foreign->id] = $foreignDuplicate->id;
                $this->updateReviews($postIds, $clone);
            }
        });
    }

    protected function cloneDirectRelation(Relation $relation, string $relationName, Model $clone): void
    {
        $relation->get()->each(function($relationObject) use ($clone, $relationName) {

            $clonedRelation = $this->duplicate($relationObject, $clone->$relationName());

            if (
                is_a($clone->$relationName(), ModelClonerConstants::BELONGS_TO) ||
                is_a($clone->$relationName(), ModelClonerConstants::MORPH_TO)
            ) {
                $clone->$relationName()->associate($clonedRelation);
                $clone->save();
            }
        });
    }

    private function updateReviews($postIds, $clone): void
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
