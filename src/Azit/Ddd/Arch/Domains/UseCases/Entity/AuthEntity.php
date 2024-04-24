<?php

namespace Azit\Ddd\Arch\Domains\UseCases\Entity;

use Illuminate\Support\Arr;

abstract class AuthEntity {

    private array $attributes;

    /**
     * Requiere el array del modelo de User que viene desde la request
     * $user = $request -> user() ?-> toArray();
     * Cabe mencionar a que el usuario de user debe de incluir la relaciones "roles"
     * @param array $attributes
     */
    public function __construct(array $attributes){
        $this -> attributes = $attributes;
    }

    /**
     * Obtiene el id del usuario logeado
     * @return int
     */
    public function getId(): int {
        return Arr::get($this -> attributes, 'id');
    }

    /**
     * Obtiene los roles del usuario logeado
     * @return array
     */
    public function getRoles(): array {
        return Arr::has($this -> attributes, 'roles') ? Arr::get($this->attributes, 'roles') : [];
    }

    /**
     * Obtiene todos los id roles del usuario
     * @return array
     */
    public function getUserIdRoles() : array {
        return Arr::pluck($this -> getRoles(), 'id');
    }

    /**
     * Valida si el usuario actual tiene roles especificos
     * @param bool $requireAll
     * @param ...$roles
     * @return bool
     */
    public function hasUserRoles(bool $requireAll, ...$roles) : bool {
        $userRoles = $this -> getUserIdRoles();

        if ($requireAll) {
            return count(array_diff($roles, $userRoles)) == 0;
        } else {
            return count(array_intersect($roles, $userRoles)) > 0;
        }
    }


}