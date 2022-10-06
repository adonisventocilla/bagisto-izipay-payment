<?php

return [
    [
        'key'    => 'sales.paymentmethods.izipay',
        'name'   => 'Izipay',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.admin.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],  [
                'name'          => 'description',
                'title'         => 'admin::app.admin.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ],  [
                'name'          => 'active',
                'title'         => 'admin::app.admin.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],  [
                'name'    => 'vads_ctx_mode',
                'title'   => 'Modo',
                'type'    => 'select',
                'options' => [
                    [
                        'title' => 'Prueba',
                        'value' => 'TEST',
                    ], [
                        'title' => 'Producción',
                        'value' => 'PRODUCTION',
                    ]
                ],
            ],  [
                'name'       => 'vads_currency',
                'title'      => 'Moneda',
                'type'          => 'select',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'options' => [
                    [
                        'title' => 'Nuevo Sol Peruano',
                        'value' => '604',
                    ], [
                        'title' => 'Dólar Americano',
                        'value' => '840',
                    ]
                ],
            ],  [
                'name'       => 'vads_site_id',
                'title'      => 'ID de Tienda',
                'type'          => 'depends',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
            ],  [
                'name'       => 'clave',
                'title'      => 'Clave secreta',
                'type'          => 'depends',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
            ],  [
                'name'       => 'hmac_sha_256',
                'title'      => 'Clave cifrado HMAC-SHA-256',
                'type'          => 'depends',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
            ]
        ]
    ]
];