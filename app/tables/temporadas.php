<?php
return [
    [
        'type' => 'text',
        'field' => 'temporada',
        'title' => 'TEMPORADA'
    ],
    [
        'type' => 'status',
        'field' => 'estado',
        'title' => 'ESTADO',
        'source' => 'temporadas'
    ],
    [
        'type' => 'date',
        'field' => 'fecha_inicio',
        'title' => 'FECHA DE ALTA'
    ],
    [
        'type' => 'date',
        'field' => 'fecha_fin',
        'title' => 'FECHA DE FIN'
    ],
    [
        'type'    => 'actions',
        'title'   => 'ACCIONES',
        'actions' => [
            [
                'icon'  => 'eye',
                'url'   => 'index.php?page=temporadas&action=detalles&id={id}',
                'title' => 'Ver',
                'class' => ''
            ],
            [
                'icon'  => 'pencil',
                'url'   => 'index.php?page=temporadas&action=edit&id={id}',
                'title' => 'Editar',
                'class' => 'btn-edit',
                'show_if' => [
                    'field'  => 'estado',
                    'values' => ['borrador']
                ]
            ],
            [
                'icon'  => 'trash',
                'url'   => 'index.php?page=temporadas&action=delete&id={id}',
                'title' => 'Eliminar',
                'class' => 'btn-delete',
                'show_if' => [
                    'field'  => 'estado',
                    'values' => ['borrador']
                ]
            ],
            [
                'icon'  => 'play-circle',
                'url'   => 'index.php?page=temporadas&action=activar&id={id}',
                'title' => 'Activar',
                'class' => 'btn-activar',
                'show_if' => [
                    'field'  => 'estado',
                    'values' => ['borrador']
                ]
            ],
            [
                'icon'  => 'flag',
                'url'   => 'index.php?page=temporadas&action=finalizar&id={id}',
                'title' => 'Finalizar',
                'class' => 'btn-finalizar',
                'show_if' => [
                    'field'  => 'estado',
                    'values' => ['activa']
                ]
            ],
            [
                'icon'  => 'lock',
                'url'   => 'index.php?page=temporadas&action=cerrar&id={id}',
                'title' => 'Cerrar',
                'class' => 'btn-cerrar',
                'show_if' => [
                    'field'  => 'estado',
                    'values' => ['finalizada']
                ]
            ]
        ]
    ]
];