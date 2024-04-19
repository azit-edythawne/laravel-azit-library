<?php

namespace Azit\Ddd\Controller;

use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Azit\Ddd\Arch\Domains\Response\StorageResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ResponseController extends Controller {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Construye json respuesta al front
     * @param BaseResponse $resource
     * @return JsonResponse|BinaryFileResponse
     */
    protected function getResponse(BaseResponse $resource) : JsonResponse|BinaryFileResponse {
        // Se utiliza para descargar archivos desde un hilo
        if ($resource instanceof StorageResponse) {
            return $this -> getResponseDocument($resource);
        }

        $data = $resource -> getData();
        $message = $resource -> getMessage();
        return $this -> getResponseData($message, $data);
    }

    /**
     * Respuesta de documentos
     * @param BaseResponse $response
     * @return JsonResponse|BinaryFileResponse
     */
    protected function getResponseDocument(BaseResponse $response): JsonResponse|BinaryFileResponse {
        $data = $response -> getData();
        $message = $response -> getMessage();

        if (!isset($data)){
            return $this -> getResponseData($message, $data);
        }

        return response() -> download($data) -> deleteFileAfterSend();
    }

    /**
     *  Respuesta de datos
     * @param string $message
     * @param mixed $data
     * @param int $emptyCode
     * @return JsonResponse
     */
    protected function getResponseData(string $message, mixed $data, int $emptyCode = Response::HTTP_ACCEPTED) : JsonResponse {
        $response = ['data' => $data, 'message' => $message];

        if (!isset($data)) {
            return response() -> json($response, $emptyCode);
        }

        return response() -> json($response);
    }

}
