<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Azit\Ddd\Arch\Domains\UseCases\Entity\AuthEntity;
use Illuminate\Http\Request;

interface UserRequireCallback {

    public function extractUserByRequest(Request $args) : ?array;

    public function getUser() : array | AuthEntity | null;

}