<?php

namespace Core\Relations;

class BelongsToRelation extends Relation
{
    public function __construct($related, $foreignKey, $ownerKey, $parent)
    {
        parent::__construct($related, $foreignKey, $ownerKey, $parent);
    }

    public function get()
    {
        $foreignValue = $this->parent->{$this->foreignKey};
        if (!$foreignValue) {
            return null;
        }

        return $this->related::where($this->localKey, $foreignValue)->first();
    }
}