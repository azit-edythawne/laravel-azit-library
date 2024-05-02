<?php

namespace Azit\Ddd\Arch\Data\Service;

use Azit\Ddd\Arch\Constant\PageConstant;
use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Arch\Data\Local\Callback\GetPaginatedIterator;
use Azit\Ddd\Arch\Domains\UseCases\BaseIterator;

abstract class PaginatedBaseLocalService extends BaseLocalService {

    private int $limit;
    private ?array $filters;
    private string $orderColumn;
    private string $orderBy;
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
     * @return void
     */
    protected function requiredPagination(GetPaginatedIterator $class) : void {
        $this -> paginated = $class;
    }

    /**
     * @param array|null $filters
     * @param int $limit
     * @param string $orderBy
     * @param string $orderColumn
     * @return void
     */
    public function set(?array $filters = null, int $limit = ValueConstant::DEFAULT_LIMIT, string $orderBy = 'desc', string $orderColumn = 'id') : void {
        $this -> limit = $limit;
        $this -> filters = $filters;
        $this -> orderBy = $orderBy;
        $this -> orderColumn = $orderColumn;
    }

    /**
     * Executar
     * @return void
     */
    public function execute() : void {
        $pages = collect($this -> paginated -> setPaginated($this -> filters, $this -> limit, $this -> orderBy, $this -> orderColumn) -> toArray());

        $this -> iterator -> feedback([
            PageConstant::PAGINATION_KEY_DATA => $pages -> pull(PageConstant::PAGINATION_KEY_DATA, []),
            PageConstant::PAGINATION_KEY_PAGES  => $pages
        ]);
    }

}
