<?php

namespace Azit\Ddd\Arch\Domains\UseCases\Storage;

use Azit\Ddd\Arch\Constant\MessageConstant;
use Azit\Ddd\Arch\Data\Network\StorageRepository;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Azit\Ddd\Arch\Domains\Response\StorageResponse;
use Azit\Ddd\Arch\Domains\UseCases\BaseCases;
use Illuminate\Support\Facades\Storage;

class GetAttachment extends BaseCases {

    protected StorageRepository $repository;

    public function __construct() {
        parent::__construct(new StorageResponse());
        $this -> repository = new StorageRepository();
    }

    public function getDownload() : BaseResponse {
        $path = $this -> getStringValue('path');
        $file = $this -> repository -> getDownload($path);

        if (isset($file)) {
            $filename = basename($path);

            Storage::put($filename, $file);
            $newPath = Storage::path($filename);

            return $this -> setResponse(MessageConstant::SUCCESS_ACTION, $newPath);
        }

        return $this -> setResponse(MessageConstant::EXCEPTION_ATTACHMENTS, null);
    }

}
