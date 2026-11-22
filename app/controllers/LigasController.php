<?php

declare(strict_types=1);

require_once ROOT_PATH . '/app/models/Liga.php';
require_once ROOT_PATH . '/app/models/Equipo.php';
require_once ROOT_PATH . '/app/models/Temporada.php';
require_once ROOT_PATH . '/app/services/AuthorizationService.php';

class LigasController
{
    private PDO $db;

    private Liga $ligaModel;
    private Equipo $equipoModel;
    private Temporada $temporadaModel;

    private AuthorizationService $authorization;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->ligaModel = new Liga($db);
        $this->equipoModel = new Equipo($db);
        $this->temporadaModel = new Temporada($db);

        $this->authorization = new AuthorizationService($db);
    }

    /**
     * Listado de ligas respetando alcance.
     */

    public function index(): void
    {
        $scopeIds = $this->authorization ->getScopedEntityIds('liga');

        $filtros = [
            'estado' => trim( (string) ($_GET['estado'] ?? '')),
            'buscar' => trim( (string) ($_GET['buscar'] ?? '')
            )
        ];

        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite = 10;

        $total =
            $this->ligaModel
                ->contar(
                    $filtros,
                    $scopeIds
                );

        $pagination =
            Pagination::paginate(
                $total,
                $limite
            );

        /*
         * Permitimos que el helper determine
         * la página actual cuando existe.
         */
        $offset =$pagination['offset'] ?? (($pagina - 1) * $limite);

        $ligas =
            $this->ligaModel
                ->listar(
                    $filtros,
                    $scopeIds,
                    $limite,
                    $offset
                );

        $metricas = $this->ligaModel ->metricas($scopeIds);

        //creamos la kpis correspondientes
        $kpis = [
            [
            'title' => 'Ligas registradas',
            'value' => $metricas['total'] ?? 0,
            'icon'  => 'fas fa-flag',
            'class' => 'info'
            ],
            [
            'title' => 'Ligas activas',
            'value' => $metricas['activas'] ?? 0,
            'icon'  => 'fas fa-check-circle',
            'class' => 'success'
            ],
            [
            'title' => 'Ligas inactivas',
            'value' => $metricas['inactivas'] ?? 0,
            'icon'  => 'fas fa-ban',
            'class' => 'danger'
            ]
        ];

        //creamos el toolbox
        $toolbox = [
            'create' => 'index.php?page=ligas&action=crear',
            'filters' => [
                [
                    'type' => 'select',
                    'name' => 'estado',
                    'label' => 'Estado',
                    'default' => '',
                    'options' => [
                        'activo' => 'Activas',
                        'inactivo' => 'Inactivas'
                    ]
                ],
                [
                    'type' => 'search',
                    'name' => 'buscar',
                    'placeholder' => 'Buscar liga...'
                ]
            ]
        ];

        //creamos la tabla final con el listado de ligas
        //y los filtros en caso de ser necesarios

        $tableData  = TableService::make($ligas, 'ligas');
        $tableTitle = 'Listado de ligas';
        $columns    = $tableData['columns'];
        $rows       = $tableData['rows']; 
        
        //comprobamos si se mando algo por ajax y lo mostramos
        if(isset($_GET['ajax'])){
            include ROOT_PATH.'/admin/views/components/table-content.php';
            return; 
        }

        //VISTA
        view(
            'pages.ligas.index.php',
            [
                'ligas'         => $ligas,
                'filtros'       => $filtros,
                'columns'       => $columns,
                'rows'          => $rows,
                'toolbox'       => $toolbox,
                'pagination'    => $pagination,
                'kpis'          => $kpis,
                'metricas'      => $metricas,
                'scopeIds'      => $scopeIds,
                'tableTitle' => $tableTitle,
                'alcanceGlobal' => $scopeIds === null
            ]
        );
    }

    public function miLiga(): void
    {
        $scopeIds = $this->authorization->getScopedEntityIds('liga');

        // El usuario no tiene ninguna liga asignada.
        if (empty($scopeIds)) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        // Por ahora utilizamos la primera liga autorizada.
        $ligaId = (int) $scopeIds[0];

        $this->detalle($ligaId);
    }

    /**
     * Detalle de una liga.
     */

   public function detalle($id): void
    {
        $id = (int) $id;

        $scopeIds = $this->authorization->getScopedEntityIds('liga');

        $liga = $this->ligaModel->getById(
            $id,
            $scopeIds
        );

        if (!$liga) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        $temporadaActual = $this->temporadaModel->getActiva();

        $totalEquipos = 0;
        $equiposLiga = [];

        if ($temporadaActual) {
            $totalEquipos = $this->equipoModel->contarPorLiga(
                $id,
                (int) $temporadaActual['id']
            );

            $equiposLiga = $this->ligaModel->listarEquiposPorTemporada(
                $id,
                (int) $temporadaActual['id']
            );
        }

        $kpis = [
            [
                'title' => 'Equipos registrados',
                'value' => $totalEquipos,
                'icon'  => 'fas fa-users',
                'class' => 'info'
            ],
            [
                'title' => 'Jugadores registrados',
                'value' => 0,
                'icon'  => 'fas fa-user',
                'class' => 'success'
            ],
            [
                'title' => 'Pendientes',
                'value' => 0,
                'icon'  => 'fas fa-clock',
                'class' => 'warning'
            ],
            [
                'title' => 'Rechazados',
                'value' => 0,
                'icon'  => 'fas fa-times-circle',
                'class' => 'danger'
            ]
        ];

        view(
            'pages.ligas.detalle.php',
            [
                'liga' => $liga,
                'kpis' => $kpis,
                'temporadaActual' => $temporadaActual,
                'equiposLiga' => $equiposLiga
            ]
        );
    }

}

?>