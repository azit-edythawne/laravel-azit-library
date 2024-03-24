<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Illuminate\Support\Collection;

interface BaseIterator {

    function transform() : string|array|Collection;

    function feedback(mixed $out);

}
