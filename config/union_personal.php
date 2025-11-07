<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ambiente Activo
    |--------------------------------------------------------------------------
    |
    | Define qué ambiente usar: 'test' o 'produccion'
    |
    */
    'ambiente' => env('UP_AMBIENTE', 'test'),

    /*
    |--------------------------------------------------------------------------
    | Configuración de Ambientes
    |--------------------------------------------------------------------------
    */
    'test' => [
        'endpoint' => env('SOAP_UP_ENDPOINT', 'http://181.13.241.19:7002/cawsTest/Servicios'),
        'wsdl' => env('SOAP_UP_WSDL', 'http://181.13.241.19:7002/cawsTest/Servicios?wsdl'),
        'prestador_id' => env('SOAP_UP_PRESTADOR_ID_PRUEBA', '8888'),
        'user_id' => env('SOAP_UP_USER_ID_PRUEBA', '8888'),
        'user_pass' => env('SOAP_UP_USER_PASS_PRUEBA', '7777'),
    ],

    'produccion' => [
        'endpoint' => env('SOAP_UP_ENDPOINT_PROD', 'https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios'),
        'wsdl' => env('SOAP_UP_WSDL_PROD', 'https://wsup.unionpersonal.com.ar:8443/cawsprod/Servicios?wsdl'),
        'prestador_id' => env('SOAP_UP_PRESTADOR_ID_PROD', '149093'),
        'user_id' => env('SOAP_UP_USER_ID_PROD', '149093'),
        'user_pass' => env('SOAP_UP_USER_PASS_PROD', 'DIAB'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Protocolo y Emisor
    |--------------------------------------------------------------------------
    */
    'protocolo' => 'CA_V20',
    'emisor_id' => env('SOAP_UP_EMISOR_ID', 'INTEGRACION-UP'),
    'terminal' => env('SOAP_UP_TERMINAL', '01'),
    'app_name' => env('SOAP_UP_APP_NAME', 'Global'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'log_enabled' => env('SOAP_UP_LOG_ENABLED', true),
    'db_log_enabled' => env('SOAP_UP_DB_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Timeouts y Configuración SOAP
    |--------------------------------------------------------------------------
    */
    'timeout' => env('SOAP_UP_TIMEOUT', 30),
    'connection_timeout' => 10,
    'cache_wsdl' => WSDL_CACHE_NONE, // Para desarrollo, en producción usar WSDL_CACHE_BOTH

    /*
    |--------------------------------------------------------------------------
    | Autocreación de Afiliados
    |--------------------------------------------------------------------------
    |
    | Si está en true, al ejecutar ELG se creará/actualizará automáticamente
    | el afiliado en la tabla afiliados_convenio_up
    |
    */
    'autocrear_afiliados' => env('UP_AUTOCREAR_AFILIADOS', true),

    /*
    |--------------------------------------------------------------------------
    | Prestaciones de Prueba
    |--------------------------------------------------------------------------
    |
    | Prestaciones válidas para testing según el manual de UP.
    | Estas prestaciones están habilitadas en ambos ambientes (test y prod).
    |
    */
    'prestaciones_test' => [
        '1420107' => 'CONSULTA ESPECIALIZADA',    // Más común para AP
        '1420101' => 'CONSULTA MEDICA',           // Básica para testing
    ],

    /*
    |--------------------------------------------------------------------------
    | Afiliados de Prueba - AMBIENTE TEST
    |--------------------------------------------------------------------------
    | 
    | Estos afiliados están configurados específicamente para el ambiente
    | de testing de Unión Personal. Todos devuelven STATUS=OK.
    |
    */
    'afiliados_test' => [
        'test' => [
            'codigo' => '54715500',
            'plan' => '150',        // Plan Accord
            'vercred' => '45',
        ],
        'test2' => [
            'codigo' => '54715300', 
            'plan' => '2',          // Plan Básico
            'vercred' => '31',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Afiliados de Prueba - AMBIENTE PRODUCCIÓN
    |--------------------------------------------------------------------------
    |
    | Estos afiliados están configurados para el ambiente de producción.
    | Usar con precaución ya que generan transacciones reales.
    |
    */

    'afiliados_prod' => [
        'prod1' => [
            'codigo' => '54715500',
            'plan' => '3C',
            'vercred' => '71',
        ],
        'prod2' => [
            'codigo' => '54715300',
            'plan' => '2',
            'vercred' => '65',
        ],
    ],
];
