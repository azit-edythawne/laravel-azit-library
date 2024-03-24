<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Data\Network\SguRepository as SguRepositoryExtended;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class SguService extends SguRepositoryExtended implements CallbackService {

    protected BaseIterator $iterator;

    /**
     * Constructor
     * @param BaseIterator $iterator
     */
    public function __construct(BaseIterator $iterator){
        $this -> iterator = $iterator;
    }

}
