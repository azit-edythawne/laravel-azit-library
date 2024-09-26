<?php

namespace Azit\Ddd\Arch\Data\Network;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class StorageRepository extends NetworkRepository {


    public function getUrl(string $segment): string {
        return config('library.storage.uri') . $segment;
    }

    /**
     * Permite guardar un archivo
     * @param string $folder
     * @param string $idRequest
     * @param UploadedFile $file
     * @return null
     */
    public function save(string $folder, string $idRequest, UploadedFile $file) {
        try {
            $filename = $file -> getClientOriginalName();

            return $this -> setPostAttachmentHttp($this -> getUrl('/upload'), [
                'filename' => $filename,
                'folder' => "$folder/$idRequest/",
            ], $file, $filename);

        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Permite guardar un archivo
     * @param string $folder
     * @param string $idRequest
     * @param UploadedFile[] $files
     * @return null
     */
    public function multiSave(string $folder, string $idRequest, array $files) {
        try {
            return $this -> setPostAttachmentHttp($this -> getUrl('/uploads'), [
                'folder' => "$folder/$idRequest/",
            ], $files);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Permite reemplazar un archivo
     * @param string $path
     * @param UploadedFile $file
     * @return null
     */
    public function rewrite(string $path, UploadedFile $file) {
        try {
            $filename = $file -> getClientOriginalName();

            return $this -> setPostAttachmentHttp($this -> getUrl('/upload-replace-old'), [
                'path_old' => $path,
            ], $file, $filename);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Permite obtener la url de un archivo mediante su path
     * @param string $path
     * @param string|null $folder
     * @return null
     */
    public function getPath(string $path, string $folder = null) {
        return $this -> getPostHttp($this -> getUrl('/get/url'), ['path' => $path, 'folder' => $folder]);
    }

    /**
     * Permite obtener las urls de varios archivos mediante su path
     * @param array|null $segments
     * @param string|null $folder
     * @return void|null
     */
    public function getPaths(array $segments = null, string $folder = null) {
        if (!isset($segments)) {
            return;
        }

        return $this -> getPostHttp($this -> getUrl('/get/urls'), ['paths' => $segments, 'folder' => $folder]);
    }

    /**
     * Permite obtener la url de un archivo mediante su path
     * @param string $path
     * @param string|null $folder
     * @return null
     */
    public function getDownload(string $path, string $folder = null) {
        return $this -> getGetAttachmentHttp($this -> getUrl('/get/download'), ['path' => $path]);
    }

    /**
     * Permite eliminar un archivo mediante su path
     * @param string $segment
     * @param string|null $folder
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteFile(string $segment, string $folder = null) : bool {
        $response = $this -> getPostGuzzle($this->getUrl('/clear/file'), ['path' => $segment, 'folder' => $folder]);

        if (isset($response)) {
            $response = json_decode($response, true);

            if (Arr::has($response, 'data')) {
                return Arr::get($response, 'data', false);
            }
        }

        return false;
    }

}
