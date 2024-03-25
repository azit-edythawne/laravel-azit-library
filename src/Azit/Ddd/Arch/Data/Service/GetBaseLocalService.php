<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class GetBaseLocalService extends BaseLocalService {

    protected GetPaginatedIterator $paginated;

    public function __construct(BaseIterator $iterator, GetPaginatedIterator $class){
        parent::__construct($iterator);
    }

    protected function requiredPagination(GetPaginatedIterator $class) : void {
        $this -> paginated = $class;
    }


    public function getPaginated(?array $filters = null, int $limit = ValueConstant::DEFAULT_LIMIT) : array {
        $pages = collect($this->paginated->setPaginated($filters, $limit) -> toArray());

        return [
            PageConstant::PAGINATION_KEY_DATA => $pages -> pull(PageConstant::PAGINATION_KEY_DATA, []),
            PageConstant::PAGINATION_KEY_PAGES  => $pages
        ];
    }

    function execute(): void {

    }

}
