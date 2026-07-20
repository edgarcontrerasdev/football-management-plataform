<?php

return [

    [
        'type' => 'image',
        'textField' => 'liga',
        'title' => 'LIGA',
        'folder' => 'ligas',
        'imageField' => 'escudo'
    ],

    [
        'type'      => 'status',
        'field'     => 'estado',
        'title'     => 'ESTADO',
        'source'    => 'general'
    ],

    [
        'type' => 'date',
        'field' => 'fecha_creacion',
        'title' => 'FECHA DE ALTA'
    ],

    [
        'type' => 'actions',
        'title' => 'ACCIONES',
        'actions' => [
            [
                'icon'      => 'eye',
                'url'       => 'index.php?page=ligas&action=detalles&id={id}',
                'title'     => 'Ver',
                'class'     =>  ''
            ],
            [
                'icon'      => 'pencil',
                'url'       => 'index.php?page=ligas&action=edit&id={id}',
                'title'     => 'Editar',
                'class'     => 'btn-edit'
            ],
            [
                'icon'      => 'trash',
                'url'       => '#',
                'title'     => 'Eliminar',
                'class'     => 'btn-delete'
            ]
        ]
    ]

];