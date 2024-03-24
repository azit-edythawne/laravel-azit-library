<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class BaseLocalService {

    protected BaseIterator $iterator;

    abstract function execute() : void;

    /**
     * Constructor
     * @param BaseIterator $iterator
     */
    public function __construct(BaseIterator $iterator){
        $this -> iterator = $iterator;
    }

}
