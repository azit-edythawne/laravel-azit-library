<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class PaginatedBaseLocalService extends BaseLocalService {

    private int $limit;
    private ?array $filters;
    private GetPaginatedIterator $paginated;

    /**
     * Constructor
     * @param BaseIterator $iterator
     */
    public function __construct(BaseIterator $iterator){
        parent::__construct($iterator);
    }

    /**
     * Requiere paginador
     * @param GetPaginatedIterator $class
     * @param array|null $filters
     * @param int $limit
     * @return void
     */
    protected function requiredPagination(GetPaginatedIterator $class, ?array $filters = null, int $limit = ValueConstant::DEFAULT_LIMIT) : void {
        $this -> limit = $limit;
        $this -> filters = $filters;
        $this -> paginated = $class;
    }

    /**
     * Executar
     * @return void
     */
    public function execute() : void {
        $pages = collect($this -> paginated -> setPaginated($this -> filters, $this -> limit) -> toArray());

        $this -> iterator -> feedback([
            PageConstant::PAGINATION_KEY_DATA => $pages -> pull(PageConstant::PAGINATION_KEY_DATA, []),
            PageConstant::PAGINATION_KEY_PAGES  => $pages
        ]);
    }

}
