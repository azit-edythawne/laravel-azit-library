<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class BaseLocalService implements CallbackService {

    protected BaseIterator $iterator;

    /**
     * Constructor
     * @param BaseIterator $iterator
     */
    public function __construct(BaseIterator $iterator){
        $this -> iterator = $iterator;
    }

}
