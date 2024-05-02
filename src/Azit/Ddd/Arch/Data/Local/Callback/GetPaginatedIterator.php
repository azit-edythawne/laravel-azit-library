<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

use Azit\Ddd\Arch\Constant\ValueConstant;
use Illuminate\Pagination\AbstractPaginator;

interface GetPaginatedIterator {

    public function setPaginated(?array $filters = null, int $limit = ValueConstant::DEFAULT_LIMIT, string $orderBy = 'desc', string $orderColumn = 'id')  : AbstractPaginator;

}
