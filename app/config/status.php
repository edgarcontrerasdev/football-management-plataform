
<?php

// app/config/status.php
return [

    'general' => [
        'activo' => [
            'label' => 'Activo',
            'color' => 'success',
            'icon'  => 'check'
        ],
        'inactivo' => [
            'label' => 'Inactivo',
            'color' => 'secondary',
            'icon'  => 'pause'
        ],
        'suspendido' => [
            'label' => 'Suspendido',
            'color' => 'danger',
            'icon'  => 'ban'
        ]
    ],
    'temporadas'    =>[
        'activa'        =>[
            'label'     => 'Activa',
            'color'     => 'success',
            'icon'      => 'check'
        ],
        'cerrada'       =>[
            'label'     => 'Cerrada',
            'color'     => 'danger',
            'icon'      => 'ban'
        ],
        'borrador'      =>[
            'label'     => 'Planeada',
            'color'     => 'secondary',
            'icon'      => 'pause'
        ],
        'finalizada'    =>[
            'label'     => 'Finalizada',
            'color'     => 'warning',
            'icon'      => 'close'
        ]   
    ],

    'pagos' => [
        'pendiente' => [
            'label' => 'Pendiente',
            'color' => 'warning',
            'icon'  => 'clock'
        ],
        'pagado' => [
            'label' => 'Pagado',
            'color' => 'success',
            'icon'  => 'dollar-sign'
        ],
        'cancelado' => [
            'label' => 'Cancelado',
            'color' => 'danger',
            'icon'  => 'x'
        ]
    ]

];