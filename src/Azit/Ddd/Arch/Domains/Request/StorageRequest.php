<?php

namespace Azit\Ddd\Arch\Domains\Request;

use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Azit\Ddd\Arch\Domains\UseCases\Storage\CreateAttachment;
use Azit\Ddd\Arch\Domains\UseCases\Storage\DeleteAttachment;
use Azit\Ddd\Arch\Domains\UseCases\Storage\GetAttachment;

class StorageRequest extends BaseRequest {

    protected GetAttachment $getCase;
    protected CreateAttachment $createCase;
    protected DeleteAttachment $deleteCase;

    public function __construct() {
        $this -> getCase = new GetAttachment();
        $this -> createCase = new CreateAttachment();
        $this -> deleteCase = new DeleteAttachment();
    }

    /**
     * Validador para guardar
     * @return BaseResponse
     */
    public function store() : BaseResponse {
        $this -> applyRules([
            'uuid' => 'required|uuid',
            'files' => 'required|array|min:1',
            'files.*.filename' => 'required|string',
            'files.*.file' => [ 'required', $this -> filesAllows() ],
        ]);

        $this -> createCase -> setRequest($this->getRequest());
        return $this -> createCase -> of();
    }

    /**
     * Validador para reemplazar
     * @return BaseResponse
     */
    public function replace() : BaseResponse {
        $this -> applyRules([
            'path_old' => 'required|string',
            'file' => [ 'required', $this -> filesAllows() ]
        ]);

        $this -> createCase -> setRequest($this->getRequest());
        return $this -> createCase -> rewrite();
    }

    /**
     * Validador para descargar
     * @return BaseResponse
     */
    public function download() : BaseResponse {
        $this -> applyRules([
            'path' => 'required|string'
        ]);

        $this -> getCase -> setRequest($this->getRequest());
        return $this -> getCase -> getDownload();
    }

    /**
     * Validador para eliminar
     * @return BaseResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy() : BaseResponse {
        $this -> applyRules([
            'path' => 'required|string'
        ]);

        $this -> deleteCase -> setRequest($this->getRequest());
        return $this -> deleteCase -> of();
    }

}
