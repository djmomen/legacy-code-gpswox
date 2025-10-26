<?php namespace Tobuli\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    private array $searchFieldsAvailable;

    public function scopeSearch(Builder $query, $value )
    {
        return $this->_search($query, $value, 'like');
    }

    public function scopeSearchExclude( $query, $value )
    {
        return $this->_search($query, $value, 'not like');
    }

    private function _search($query, $value, $operator)
    {
        $value = trim($value);

        if (empty($value))
            return $query;

        if (empty($this->searchable))
            return $query;

        $values = explode(';', $value);

        if (count($values) > 1) {
            $query->where(function ($query) use ($values) {
                foreach ($values as $value) {
                    $query->orWhere(function($q) use ($value){
                        $q->search($value);
                    });
                }
            });

            return $query;
        }

        $query->where(function ($query) use ($value, $operator) {
            foreach ($this->getSearchableFields() as $searchField) {
                $parts = explode('.', $searchField);
                $relation = null;
                $field = $parts[0];

                if (isset($parts[1])) {
                    $relation = $parts[0];
                    $field = $parts[1];
                }

                if ($relation) {
                    $query->orWhereHas($relation, function($query) use ($field, $value, $operator){
                        $query->where($field, $operator, '%' . $value . '%');
                    });
                } else {
                    $query->orWhere($this->table.'.'.$field, $operator, '%' . $value . '%');
                }

            }
        });

        return $query;
    }

    private function getSearchableFields(): array
    {
        if (isset($this->searchFieldsAvailable)) {
            return $this->searchFieldsAvailable;
        }

        $configFields = settings('search_fields.' . static::class);

        return $this->searchFieldsAvailable = is_array($configFields)
            ? array_intersect($this->searchable, $configFields)
            : $this->searchable;
    }
}
