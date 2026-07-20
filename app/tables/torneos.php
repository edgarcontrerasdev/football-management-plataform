<?php
return [
    //torneo
    [
        'type'  =>  'text',
        'field' =>  'nombre',
        'title' =>  'torneo'
    ],
    [
        'type'  =>  'text',
        'field' =>  'liga',
        'title' =>  'liga'
    ],
    [
        'type'  =>  'date',
        'field' =>  'fecha_inicio',
        'title' =>  'INICIO'
    ],
    [
        'type'  =>  'date',
        'field' =>  'fecha_fin',
        'title' =>  'TERMINA'
    ],
    [
        'type'   =>  'status',
        'field'  =>  'estado',
        'title'  =>  'estado',
        'source' =>  'general'  
    ],
    [
        'type'   =>  'actions',
        'title'  =>  'acciones',
        'actions' =>[
            [
                'icon'  => 'eye',
                'url'   => 'index.php?page=torneos&action=detalle&id={id}',
                'title' => 'ver',
                'class' => ''
            ],
            [
                'icon'  => 'pencil',
                'url'   => 'index.php?page=torneos&action=edit&id={id}',
                'title' => 'editar',
                'class' => 'btn-edit'
            ],
            [
                'icon'  => 'trash',
                'url'   => '#',
                'title' => 'eliminar',
                'class' => 'btn-delete'
            ]
        ]
    ]  


]; ?>