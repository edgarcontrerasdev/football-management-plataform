<?php

return [
    [
        'type'       => 'image',
        'textField'  => 'equipo',
        'subField'   => 'siglas',
        'title'      => 'EQUIPO',
        'folder'     => 'equipos',
        'imageField' => 'escudo'
    ],
    [
        'type'  => 'date',
        'field' => 'fecha_alta',
        'title' => 'FECHA DE REGISTRO'
    ],
    [
        'type'    => 'actions',
        'title'   => 'ACCIONES',
        'actions' => [
            [
                'icon'  => 'eye',
                'url'   => 'index.php?page=equipos&action=detalles&id={id}',
                'title' => 'Ver equipo',
                'class' => ''
            ]
        ]
    ]
];
