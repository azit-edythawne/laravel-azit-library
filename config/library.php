<?php

return [
    'storage' => [
        'root' => env('APP_STORAGE_ROOT'),
        'uri' => env('APP_STORAGE_UR'),
        'base_path' => env('APP_OWNER_BASE_GET_STORAGE')
    ],
    'url_sgu' => env('SGU_LOGIN', 'http://201.163.59.107:7099/persona/v2/persona?llaveApp=%s'),
    'url_sgu_logout' => env('SGU_LOGOUT', 'http://201.163.59.107:7099/logout'),
    'url_sgu_cct' => env('SGU_URL_SEARCH_BY_CCT', 'https://apis.sej.jalisco.gob.mx/ccts/v1/ccts/clavecct?llaveApp=%s&clave=%s'),
    'url_sgu_by_person' => env('SGU_URL_SEARCH_BY_PERSON', 'http://201.163.59.107:7099/persona/v2/persona/trabajador?llaveApp=%s&filtro=%s&criterio=%s')
];