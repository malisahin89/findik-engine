<?php

namespace Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Core\Relations\HasOneRelation;
use Core\Relations\HasManyRelation;
use Core\Relations\BelongsToRelation;

abstract class Model extends EloquentModel
{
    public $timestamps = true;

    // Override relationship methods to return Eloquent relations
    protected function newHasOne($query, $parent, $foreignKey, $localKey)
    {
        return new HasOneRelation($query, $parent, $foreignKey, $localKey);
    }

    protected function newHasMany($query, $parent, $foreignKey, $localKey)
    {
        return new HasManyRelation($query, $parent, $foreignKey, $localKey);
    }

    protected function newBelongsTo($query, $child, $foreignKey, $ownerKey, $relation)
    {
        return new BelongsToRelation($query, $child, $foreignKey, $ownerKey, $relation);
    }
}