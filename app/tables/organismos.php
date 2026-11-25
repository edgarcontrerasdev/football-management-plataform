<?php

return [
    [
        'type' => 'image',
        'folder' => 'organismos',
        'imageField' => 'logo',
        'textField' => 'nombre',
        'subField' => 'siglas',
        'defaultImage' => 'ligas/default.png',
        'title' => 'ORGANISMO'
    ],
    ['type' => 'text', 'field' => 'tipo', 'title' => 'TIPO'],
    ['type' => 'text', 'field' => 'alcance_territorial', 'title' => 'ALCANCE'],
    ['type' => 'status', 'field' => 'estado', 'title' => 'ESTADO', 'source' => 'general'],
    ['type' => 'date', 'field' => 'created_at', 'title' => 'REGISTRADO'],
    ['type' => 'actions', 'title' => 'ACCIONES', 'actions' => [['icon' => 'eye', 'url' => 'index.php?page=organismos&action=detalles&id={id}', 'title' => 'Ver organismo', 'class' => '']]]
];
