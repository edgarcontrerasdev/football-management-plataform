<?php

return [
    [
        'type'       => 'image',
        'textField'  => 'nombre',
        'title'      => 'CLUB',
        'folder'     => 'clubes',
        'imageField' => 'logo'
    ],
    [
        'type' => 'text',
        'field' => 'siglas',
        'title' => 'SIGLAS'
    ],
    [
        'type' => 'status',
        'field' => 'estado',
        'title' => 'ESTADO',
        'source' => 'general'
    ],
    [
        'type' => 'date',
        'field' => 'created_at',
        'title' => 'REGISTRADO'
    ]
];
