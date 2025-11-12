<?php

namespace Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Capsule\Manager as DB;
use Core\Relations\HasOneRelation;
use Core\Relations\HasManyRelation;
use Core\Relations\BelongsToRelation;

abstract class Model extends EloquentModel
{
    public $timestamps = true;
    
    // Query Builder methods
    public static function query()
    {
        return DB::table((new static)->getTable());
    }
    
    public static function where($column, $operator = null, $value = null)
    {
        return static::query()->where($column, $operator, $value);
    }
    
    public static function find($id)
    {
        $result = static::query()->where('id', $id)->first();
        if (!$result) return null;
        
        $instance = new static();
        foreach ($result as $key => $value) {
            $instance->$key = $value;
        }
        $instance->exists = true;
        return $instance;
    }
    
    public static function all($columns = ['*'])
    {
        return static::query()->select($columns)->get()->map(function($item) {
            $instance = new static();
            foreach ($item as $key => $value) {
                $instance->$key = $value;
            }
            $instance->exists = true;
            return $instance;
        });
    }
    
    public static function create(array $attributes)
    {
        $instance = new static();
        $instance->fill($attributes);
        $instance->save();
        return $instance;
    }
    
    public function save(array $options = [])
    {
        if ($this->exists) {
            return $this->doUpdate();
        }
        return $this->doInsert();
    }
    
    protected function doInsert()
    {
        $attributes = $this->getAttributes();
        if ($this->timestamps) {
            $attributes['created_at'] = $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $id = static::query()->insertGetId($attributes);
        $this->setAttribute('id', $id);
        $this->exists = true;
        return true;
    }
    
    protected function doUpdate()
    {
        $attributes = $this->getAttributes();
        if ($this->timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return static::query()->where('id', $this->id)->update($attributes);
    }
    
    public function delete()
    {
        return static::query()->where('id', $this->id)->delete();
    }
    
    // Relationship methods
    public function hasOne($related, $foreignKey = null, $localKey = 'id')
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        return new HasOneRelation($related, $foreignKey, $localKey, $this);
    }
    
    public function hasMany($related, $foreignKey = null, $localKey = 'id')
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        return new HasManyRelation($related, $foreignKey, $localKey, $this);
    }
    
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $ownerKey = $ownerKey ?: 'id';
        $foreignKey = $foreignKey ?: $this->getRelatedForeignKey($related);
        return new BelongsToRelation($related, $foreignKey, $ownerKey, $this);
    }
    
    public function getForeignKey()
    {
        return strtolower(class_basename($this)) . '_id';
    }
    
    protected function getRelatedForeignKey($related)
    {
        return strtolower(class_basename($related)) . '_id';
    }
}