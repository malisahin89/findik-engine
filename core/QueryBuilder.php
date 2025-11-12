<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as DB;

class QueryBuilder
{
    protected $table;
    protected $query;
    
    public function __construct($table)
    {
        $this->table = $table;
        $this->query = DB::table($table);
    }
    
    public function select($columns = ['*'])
    {
        $this->query = $this->query->select($columns);
        return $this;
    }
    
    public function where($column, $operator = null, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->query = $this->query->where($column, $operator, $value);
        return $this;
    }
    
    public function whereIn($column, $values)
    {
        $this->query = $this->query->whereIn($column, $values);
        return $this;
    }
    
    public function whereNull($column)
    {
        $this->query = $this->query->whereNull($column);
        return $this;
    }
    
    public function orderBy($column, $direction = 'asc')
    {
        $this->query = $this->query->orderBy($column, $direction);
        return $this;
    }
    
    public function limit($limit)
    {
        $this->query = $this->query->limit($limit);
        return $this;
    }
    
    public function offset($offset)
    {
        $this->query = $this->query->offset($offset);
        return $this;
    }
    
    public function join($table, $first, $operator = null, $second = null)
    {
        $this->query = $this->query->join($table, $first, $operator, $second);
        return $this;
    }
    
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        $this->query = $this->query->leftJoin($table, $first, $operator, $second);
        return $this;
    }
    
    public function groupBy($columns)
    {
        $this->query = $this->query->groupBy($columns);
        return $this;
    }
    
    public function having($column, $operator = null, $value = null)
    {
        $this->query = $this->query->having($column, $operator, $value);
        return $this;
    }
    
    public function get()
    {
        return $this->query->get();
    }
    
    public function first()
    {
        return $this->query->first();
    }
    
    public function find($id)
    {
        return $this->query->where('id', $id)->first();
    }
    
    public function count()
    {
        return $this->query->count();
    }
    
    public function exists()
    {
        return $this->query->exists();
    }
    
    public function insert(array $values)
    {
        return $this->query->insert($values);
    }
    
    public function insertGetId(array $values)
    {
        return $this->query->insertGetId($values);
    }
    
    public function update(array $values)
    {
        return $this->query->update($values);
    }
    
    public function delete()
    {
        return $this->query->delete();
    }
    
    public function paginate($perPage = 15, $page = null)
    {
        $page = $page ?: (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        
        $total = $this->count();
        $items = $this->limit($perPage)->offset($offset)->get();
        
        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
}