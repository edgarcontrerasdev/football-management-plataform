```php
<?php

return [

    [
        'type'       => 'image',
        'textField'  => 'nombre',
        'title'      => 'LIGA',
        'folder'     => 'ligas',
        'imageField' => 'logo'
    ],

    [
        'type'  => 'text',
        'field' => 'siglas',
        'title' => 'SIGLAS'
    ],

    [
        'type'  => 'text',
        'field' => 'municipio',
        'title' => 'MUNICIPIO'
    ],

    [
        'type'   => 'status',
        'field'  => 'estado',
        'title'  => 'ESTADO',
        'source' => 'general'
    ],

    [
        'type'    => 'actions',
        'title'   => 'ACCIONES',
        'actions' => [
            [
                'icon'  => 'eye',
                'url'   => 'index.php?page=ligas&action=detalle&id={id}',
                'title' => 'Ver liga',
                'class' => ''
            ]
        ]
    ]

];
