<?php

return [
    'url_sgu' => env('URL_SGU', 'http://201.163.59.107:7099/persona/v2/persona?llaveApp=%s'),
    'url_sgu_logout' => env('URL_SGU_LOGOUT', 'http://201.163.59.107:7099/logout'),
    'url_sgu_cct' => env('URL_SGU_LOGOUT', 'https://apis.sej.jalisco.gob.mx/ccts/v1/ccts/clavecct?llaveApp=%s&clave=%s'),
    'url_sgu_by_person' => env('URL_SGU_LOGOUT', 'http://201.163.59.107:7099/persona/v2/persona/trabajador?llaveApp=%s&filtro=%s&criterio=%s')
];