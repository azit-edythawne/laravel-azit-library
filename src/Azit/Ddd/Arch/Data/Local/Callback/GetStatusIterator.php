<?php

namespace Azit\Ddd\Arch\Data\Local\Callback;

use Azit\Ddd\Model\BaseBuilder;

interface GetStatusIterator {

    public function getNextStatus(int $id, array $idRoles, int $type = BaseBuilder::TYPE_WHERE, string $columnStatus = 'status_id');

}