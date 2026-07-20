<?php

return [
    
        //escudo+equipo
        [
            'type'          => 'image',
            'textField'     => 'equipo',
            'subField'      => 'liga',
            'title'         => 'EQUIPO',
            'folder'        => 'equipos',
            'imageField'    => 'escudo'
        ],
        //estado
        [
            'type' => 'status',
            'field' => 'estado',
            'title' => 'ESTADO',
            'source' => 'general'
        ],
        //fecha de alta
        [
            'type' => 'date',
            'field' => 'fecha_creacion',
            'title' => 'FECHA DE ALTA'
        ],
        //liga
        [
            'type' => 'text',
            'field' => 'liga',
            'title' => 'LIGA'
        ],
        //
        //acciones
        [
            'type'          => 'actions',
            'title'         => 'ACCIONES',
            'actions'       => [
                [
                    'icon'      => 'eye',
                    'url'       => 'index.php?page=equipos&action=detalles&id={id}',
                    'title'     => 'Ver',
                    'class'     =>  ''
                ],
                [
                    'icon'      => 'pencil',
                    'url'       => 'index.php?page=equipos&action=edit&id={id}',
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