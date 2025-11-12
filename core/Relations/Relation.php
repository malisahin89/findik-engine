<?php

namespace Core\Relations;

use Illuminate\Database\Capsule\Manager as DB;

abstract class Relation
{
    protected $related;
    protected $foreignKey;
    protected $localKey;
    protected $parent;
    
    public function __construct($related, $foreignKey, $localKey, $parent)
    {
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->parent = $parent;
    }
    
    abstract public function get();
    
    protected function getRelatedTable()
    {
        return (new $this->related)->getTable();
    }
}