<?php

namespace Azit\Ddd\Controller;

use Azit\Ddd\Arch\Domains\Request\StorageRequest;
use Illuminate\Http\Request;

class StorageController extends ResponseController {

    protected StorageRequest $request;

    public function __construct() {
        $this -> request = new StorageRequest();
    }

    public function store(Request $args) {
        $this -> request -> setRequest($args);
        $response = $this -> request -> store();
        return $this -> getResponse($response);
    }

    public function multiStore(Request $args) {
        $this -> request -> setRequest($args);
        $response = $this -> request -> multiStore();
        return $this -> getResponse($response);
    }

    public function replace(Request $args){
        $this -> request -> setRequest($args);
        $response = $this -> request -> replace();
        return $this -> getResponse($response);
    }

    public function postDownload(Request $args) {
        $this -> request -> setRequest($args);
        $response = $this -> request -> download();
        return $this -> getResponse($response);
    }

    public function getDownload(Request $args){
        return $this -> postDownload($args);
    }

    public function destroy(Request $args){
        $this -> request -> setRequest($args);
        $response = $this -> request -> destroy();
        return $this -> getResponse($response);
    }

}
