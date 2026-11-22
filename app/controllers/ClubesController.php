<?php

declare(strict_types=1);

require_once ROOT_PATH . '/app/models/Club.php';

class ClubesController
{
    private Club $clubModel;

    public function __construct(PDO $db)
    {
        $this->clubModel = new Club($db);
    }

    public function index(): void
    {
        if (!in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        $filtros = [
            'estado' => trim((string) ($_GET['estado'] ?? '')),
            'buscar' => trim((string) ($_GET['buscar'] ?? ''))
        ];

        $pagina = max(1, (int) ($_GET['p'] ?? 1));
        $limite = 10;
        $total = $this->clubModel->contar($filtros);
        $pagination = Pagination::paginate($total, $limite);
        $offset = $pagination['offset'] ?? (($pagina - 1) * $limite);

        $clubes = $this->clubModel->listar($filtros, $limite, $offset);
        $metricas = $this->clubModel->metricas();

        $kpis = [
            [
                'title' => 'Clubes registrados',
                'value' => $metricas['total'],
                'icon' => 'fas fa-shield-alt',
                'class' => 'info'
            ],
            [
                'title' => 'Clubes activos',
                'value' => $metricas['activos'],
                'icon' => 'fas fa-check-circle',
                'class' => 'success'
            ],
            [
                'title' => 'Clubes inactivos',
                'value' => $metricas['inactivos'],
                'icon' => 'fas fa-ban',
                'class' => 'danger'
            ]
        ];

        $toolbox = [
            'filters' => [
                [
                    'type' => 'select',
                    'name' => 'estado',
                    'label' => 'Todos los estados',
                    'default' => '',
                    'options' => [
                        'activo' => 'Activos',
                        'inactivo' => 'Inactivos'
                    ]
                ],
                [
                    'type' => 'search',
                    'name' => 'buscar',
                    'placeholder' => 'Buscar club por nombre o siglas...'
                ]
            ]
        ];

        $tableData = TableService::make($clubes, 'clubes');
        $columns = $tableData['columns'];
        $rows = $tableData['rows'];
        $tableTitle = 'Catálogo de clubes';

        if (isset($_GET['ajax'])) {
            include ROOT_PATH . '/admin/views/components/table-content.php';
            return;
        }

        view('pages.clubes.index.php', [
            'clubes' => $clubes,
            'filtros' => $filtros,
            'columns' => $columns,
            'rows' => $rows,
            'toolbox' => $toolbox,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'metricas' => $metricas,
            'tableTitle' => $tableTitle
        ]);
    }
}
