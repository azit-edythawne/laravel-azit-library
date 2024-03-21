<?php

namespace Azit\Ddd\Arch\Domains\UseCases;

use Azit\Ddd\Arch\Constant\ValueConstant;
use Azit\Ddd\Arch\Domains\Builder\FilterCreateBuilder;
use Azit\Ddd\Arch\Domains\Response\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class BaseCases {

    private ?string $url;
    private ?array $user;
    protected ?array $attributes;
    private BaseResponse $resource;
    private ?UserRequireCallback $userCallback;

    /**
     * Constructor
     * Permite un objecto response opcional
     * Permite un user callback para obtener el usuario logeado
     * @param BaseResponse|null $object
     * @param UserRequireCallback|null $callback
     */
    public function __construct(?BaseResponse $object, ?UserRequireCallback $callback) {
        $this -> initResponse($object);
        $this -> userCallback = $callback;
    }

    /**
     * Se require inicializar objecto de respuesta
     * @param BaseResponse|null $object
     * @return void
     */
    private function initResponse(?BaseResponse $object) {
        if (isset($object)) {
            $this -> resource = $object;
        }

        if (!isset($object)) {
            $this -> resource = new BaseResponse();
        }
    }

    /**
     * Requiere la request de laravel para obtener el usuario autenticado
     * @param Request $args
     * @return void
     */
    public function setRequest(Request $args) : void {
        $this -> setAttributes($args -> all());
        $this -> url = $args -> url();

        // Se requiere informacion del usuario autenticado
        if (isset($this -> userCallback)) {
            $this -> user = $this -> userCallback -> extractUserByRequest($args);
        }
    }

    /**
     * @param array|null $attributes
     * @deprecated Este metodo esta obsoleto, aunque puede ser utilizado aun
     */
    protected function setAttributes(?array $attributes): void {
        $this -> attributes = $attributes;
    }

    /**
     * Agregar nuevo valor al attributo del request
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function newAttribute(string $key, mixed $value) : void {
        $this -> attributes = Arr::add($this -> attributes, $key, $value);
    }

    /**
     * Retorna la respuesta
     * @param string $message
     * @param mixed $data
     * @return BaseResponse
     */
    protected function setResponse(string $message, mixed $data) : BaseResponse {
        $this -> resource -> setData($data);
        $this -> resource -> setMessage($message);
        return $this -> resource;
    }

    /**
     * Obtiene un valor del array de atributo dado un key
     * @param string $key
     * @return mixed
     */
    protected function getValue(string $key) : mixed {
        if (Arr::has($this -> attributes, $key)) {
            $value = Arr::get($this -> attributes, $key);

            return $value == 'null' ? null : $value;
        }

        return null;
    }

    /**
     * Validar que la request tenga una key
     * @param string $key
     * @return bool
     */
    protected function hasKey(string $key) : bool {
        return Arr::has($this -> attributes, $key);
    }

    /**
     * Obtiene un valor numerico
     * @param string $key
     * @return int
     */
    protected function getNumericValue(string $key) : int {
        return Arr::get($this->attributes, $key, ValueConstant::DEFAULT_NUMERIC);
    }

    /**
     * Obtiene un valor string
     * @param string $key
     * @return string
     */
    protected function getStringValue(string $key) : string {
        return Arr::get($this->attributes, $key, ValueConstant::DEFAULT_STRING);
    }

    /**
     * Obtiene un valor array
     * @param string $key
     * @return Collection
     */
    protected function getArrayValue(string $key) : Collection {
        return collect(Arr::get($this->attributes, $key));
    }

    /**
     * Permite crear un objecto de la clase FilterCreateBuilder para consultas de paginadores
     * @return FilterCreateBuilder
     */
    protected function getFilterCreateBuilder() : FilterCreateBuilder {
        return new FilterCreateBuilder($this -> attributes);
    }

    /**
     * Permite obtener un UploadedFile de los atributos
     * @param string $key
     * @param int $position
     * @return UploadedFile|null
     */
    protected function getAttachment(string $key, int $position = 0) : ?UploadedFile {
        if (!$this -> hasKey($key)) {
            return null;
        }

        $value = Arr::get($this->attributes, $key);

        if (is_array($value)) {
            $value = $value[$position];
        }

        return $value instanceof UploadedFile ? $value : null;
    }

    /**
     * Obtiene el ultimo segmento de la url actual del endpoint que se consulta
     * @return string
     */
    protected function getUrlLastSegment(): string {
        return Str::of($this->url)->basename();
    }

}
