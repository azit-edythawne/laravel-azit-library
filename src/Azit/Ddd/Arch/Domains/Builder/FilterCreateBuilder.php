<?php

namespace Azit\Ddd\Arch\Domains\Builder;

use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Model\BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FilterCreateBuilder {
    protected Collection $queries;
    protected Collection $columns;
    protected Collection $selects;
    protected array $attributes;

    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';

    public const OPERATOR_ILIKE = 'ilike';

    /**
     * Constructor
     * @param array $attributes
     */
    public function __construct(array $attributes) {
        $this -> attributes = $attributes;
        $this -> columns = collect();
        $this -> selects = collect();
        $this -> queries = collect([
            BaseBuilder::TYPE_WHERE => [],
            BaseBuilder::TYPE_WHERE_FN => [],
            BaseBuilder::TYPE_RELATION_NESTED => [],
        ]);
    }

    /**
     * Regresa los atributos
     * @return array
     */
    public function getAttributes() : array {
        return $this -> attributes;
    }

    /**
     * Regresa un attributo
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key) : mixed {
        if (Arr::has($this -> attributes, $key)) {
            $value = Arr::get($this -> attributes, $key);

            return $value == 'null' ? null : $value;
        }

        return null;
    }

    /**
     * Metodo que mezcla los tipos de condicionales
     * BaseBuilder::TYPE_WHERE para condicional where
     *  BaseBuilder::TYPE_WHERE_FN para condicional where anidado
     * @param int $typeQuery
     * @param string $table
     * @param bool $isRelation
     * @param string $logic
     * @return void
     */
    private function query(int $typeQuery, string $table, bool $isRelation, string $logic){
        if ($this -> columns -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
            $cached = $this -> queries -> pull($typeQuery);

            $custom = [
                [
                    'table' => $table,
                    'relation' => $isRelation,
                    'boolean' => $logic,
                    'columns' => $this -> columns -> toArray()
                ]
            ];

            if ($this -> selects -> count() > ValueConstant::ARRAY_SIZE_EMPTY) {
                $custom[ValueConstant::ARRAY_SIZE_EMPTY]['selects'] = $this -> selects -> toArray();
            }

            $merged = collect($cached) -> merge($custom);
            $this -> queries -> put($typeQuery, $merged);
        }

        $this -> columns = collect();
        $this -> selects = collect();
    }

    /**
     * Condicional where
     * @param int $typeQuery
     * @param string $logic
     * @return void
     */
    public function addQuery(int $typeQuery, string $logic = BaseBuilder::AND){
        $this -> query($typeQuery, BaseBuilder::RELATION_HOST, false, $logic);
    }

    /**
     * Condicionales anidas
     * @param int $typeQuery
     * @param string $table
     * @param bool $isRelation
     * @param string $logic
     * @return void
     */
    public function addQueryNested(int $typeQuery, string $table, bool $isRelation, string $logic = BaseBuilder::AND){
        $this -> query($typeQuery, $table, $isRelation, $logic);
    }

    /**
     * Agregar columnas que permiten condicionales
     * @param string $column
     * @param string|null $keyValue
     * @param string $operator
     * @param string $logic
     * @param mixed|null $defaultValue Agregar un valor si la key no existe
     * @return void
     */
    public function addColumn(string $column, string $keyValue = null, string $operator = '=', string $logic = BaseBuilder::AND, mixed $defaultValue = null) {
        $isKeyExists =  Arr::exists($this -> attributes, $keyValue);

        if (!$isKeyExists && !isset($defaultValue)){
            return;
        }

        $value = $isKeyExists ? Arr::get($this -> attributes, $keyValue) : $defaultValue;

        if (Str::contains($operator, 'like')){
            $this -> columns -> add([$column, $operator, Str::replace('?', $value,'%?%'), $logic]);
        }

        if (!Str::contains($operator, 'like')){
            $this -> columns -> add([$column, $operator, $value, $logic]);
        }
    }

    /**
     * Agregar columnas que permiten condicionales
     * @param string $columnRaw
     * @param array|null $bindings
     * @param string $logic
     * @return void
     */
    public function addColumnRaw(string $columnRaw, array $bindings = null, string $logic = BaseBuilder::AND){
        $this -> columns -> add([$columnRaw, BaseBuilder::OP_RAW, $bindings, $logic]);
    }

    /**
     * Agregar columnas a la relacion
     * @param array $selects
     * @return void
     */
    public function addSelects(array $selects){
        $this -> selects = collect($selects);
    }

    /**
     * Regresa el array de las condicionales anidadas o lineales
     * @return array
     */
    public function toArray() : array {
        if ($this -> queries -> get(BaseBuilder::TYPE_WHERE) == null){
            $this -> queries -> forget(BaseBuilder::TYPE_WHERE);
        }

        if ($this -> queries -> get(BaseBuilder::TYPE_WHERE_FN) == null){
            $this -> queries -> forget(BaseBuilder::TYPE_WHERE_FN);
        }

        return $this -> queries -> toArray();
    }

    /**
     * Obtiene el listado de columnas
     * @return array
     */
    public function toColumns() : array {
        return $this -> columns -> toArray();
    }

}
