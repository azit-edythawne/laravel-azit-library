<?php

namespace Azit\Ddd\Arch\Domains\Request;

use Azit\Ddd\Arch\Constant\FileConstant;
use Azit\Ddd\Arch\Constant\MessageConstant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator as Validate;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest {
    protected Request $request;
    private const FILE_ALLOWS = ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

    /**
     * @return Request
     */
    public function getRequest(): Request {
        return $this->request;
    }

    /**
     * Permite agregar el request a la clase padre
     * @param Request $request
     */
    public function setRequest(Request $request): void {
        $this -> request = $request;
    }

    /**
     * Permite obtener un valor especifico de la clase Request de Laravel
     * @param string $key
     * @return mixed
     */
    public function getItemRequest(string $key): mixed {
        return $this -> request -> $key;
    }

    /**
     * Permite obtener el segmento final del endpoint que se esta consumiendo actualmente
     * Obtiene ultimo segmento de url
     * @return string
     */
    protected function getUrlLastSegment(): string {
        return Str::of($this -> request -> url()) -> basename();
    }

    /**
     * Permite parsear la request a array para regresar sus atributos
     * @return array
     * @deprecated Este metodo esta obsoleto, no debería de ocuparse
     */
    public function getParseRequest(): array {
        return $this -> getRequest() -> all();
    }

    /**
     * Parsea string a json
     * @param string $key
     * @return array
     */
    public function stringToJson(string $key): array {
        return json_decode($this -> getItemRequest($key), true);
    }

    /**
     * Permite agregar nuevos valores a la request
     * @param array $newAttributes
     * @return void
     */
    public function merge(array $newAttributes) : void {
        $this -> request -> merge($newAttributes);
    }

    /**
     * Aplica reglas de validacion
     * @param array $rules
     * @return void
     */
    protected function applyRules(array $rules) {
        $validator = Validator::make($this->getParseRequest(), $rules);
        $this->isValid($validator);
    }

    /**
     * Permite validar si el request es valido o tornara una excepción
     * @param Validate $validator
     * @return void
     */
    protected function isValid(Validate $validator): void {
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'message' => MessageConstant::EXCEPTION_DATA_REQUIRED,
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Permite configurar los archiuos permitidos
     * @return File
     */
    protected function filesAllows(array $fileAllows = BaseRequest::FILE_ALLOWS, int $fileSize = FileConstant::FILE_MAX_SIZE){
        return File::types($fileAllows) -> max($fileSize);
    }
}
