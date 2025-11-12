<?php

namespace Core\Relations;

class HasOneRelation extends Relation
{
    public function __construct($related, $foreignKey, $localKey, $parent)
    {
        parent::__construct($related, $foreignKey, $localKey, $parent);
    }

    public function get()
    {
        $localValue = $this->parent->{$this->localKey};
        if (!$localValue) {
            return null;
        }

        return $this->related::where($this->foreignKey, $localValue)->first();
    }
}