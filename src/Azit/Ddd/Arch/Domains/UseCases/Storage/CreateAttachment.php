<?php

namespace Azit\Ddd\Arch\Domains\UseCases\Storage;

use Azit\Ddd\Arch\Constant\MessageConstant;
use Azit\Ddd\Arch\Data\Network\StorageRepository;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Azit\Ddd\Arch\Domains\UseCases\BaseCases;

class CreateAttachment extends BaseCases {

    protected StorageRepository $repository;

    public function __construct() {
        parent::__construct(null);
        $this -> repository = new StorageRepository();
    }

    public function of() : BaseResponse {
        $file = $this -> getAttachment('files');
        $uuid = $this -> getStringValue('uuid');
        $path = $this -> repository -> save(config('storage-external.root'), $uuid, $file);
        return  $this -> setResponse(MessageConstant::ACTION_MAKE, $path);
    }

    public function rewrite() : BaseResponse {
        $file = $this -> getAttachment('file');
        $pathOld = $this -> getStringValue('path_old');
        $path = $this -> repository -> rewrite($pathOld, $file);
        return  $this -> setResponse(MessageConstant::ACTION_MAKE, $path);
    }

}
