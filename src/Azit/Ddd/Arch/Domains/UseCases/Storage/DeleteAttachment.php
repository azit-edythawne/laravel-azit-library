<?php

namespace Azit\Ddd\Arch\Domains\UseCases\Storage;

use Azit\Ddd\Arch\Constant\MessageConstant;
use Azit\Ddd\Arch\Data\Network\StorageRepository;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Azit\Ddd\Arch\Domains\UseCases\BaseCases;

class DeleteAttachment extends BaseCases {

    protected StorageRepository $repository;

    public function __construct() {
        parent::__construct(null);
        $this -> repository = new StorageRepository();
    }

    /**
     * Elinminacion de archivo
     * @return BaseResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function of() : BaseResponse {
        $path = $this -> getStringValue('path');
        $isRemove = $this -> repository -> deleteFile($path);
        return $this -> setResponse(MessageConstant::SUCCESS_ACTION, $isRemove);
    }

}
