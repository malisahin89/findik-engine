<?php

namespace Core\Relations;

class HasManyRelation extends Relation
{
    public function __construct($related, $foreignKey, $localKey, $parent)
    {
        parent::__construct($related, $foreignKey, $localKey, $parent);
    }

    public function get()
    {
        $localValue = $this->parent->{$this->localKey};
        if (!$localValue) {
            return [];
        }

        return $this->related::where($this->foreignKey, $localValue)->get();
    }
}