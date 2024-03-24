<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

interface BaseIterator {

    function transform() : string|array;

    function feedback(mixed $out);

}
