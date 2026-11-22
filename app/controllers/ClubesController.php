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
            'create' => 'index.php?page=clubes&action=crear',
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

    private function verificarAcceso(): void
    {
        if (!in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)) {
            http_response_code(403);
            view('403.php');
            exit;
        }
    }

    public function crear(): void
    {
        $this->verificarAcceso();

        $error = $_SESSION['club_error'] ?? null;
        unset($_SESSION['club_error']);

        view('pages.clubes.create.php', ['error' => $error]);
    }

    public function store(): void
    {
        $this->verificarAcceso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=clubes&action=crear');
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $siglas = trim((string) ($_POST['siglas'] ?? ''));
        $observaciones = trim((string) ($_POST['observaciones'] ?? ''));

        $_SESSION['club_old'] = [
            'nombre' => $nombre,
            'siglas' => $siglas,
            'observaciones' => $observaciones
        ];

        if ($nombre === '') {
            $_SESSION['club_error'] = 'El nombre del club es obligatorio.';
            header('Location: index.php?page=clubes&action=crear');
            exit;
        }

        $archivo = $_FILES['logo'] ?? null;
        $nombreLogo = null;
        $rutaLogo = null;

        if ($archivo && $archivo['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['club_error'] = 'No se pudo recibir el archivo del logo.';
                header('Location: index.php?page=clubes&action=crear');
                exit;
            }

            if ($archivo['size'] > 2 * 1024 * 1024) {
                $_SESSION['club_error'] = 'El logo no debe superar los 2 MB.';
                header('Location: index.php?page=clubes&action=crear');
                exit;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($archivo['tmp_name']);
            $formatos = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($formatos[$mime]) || @getimagesize($archivo['tmp_name']) === false) {
                $_SESSION['club_error'] = 'El archivo debe ser una imagen JPG, PNG o WEBP válida.';
                header('Location: index.php?page=clubes&action=crear');
                exit;
            }

            $directorio = ROOT_PATH . '/public/assets/img/clubes/';
            if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                $_SESSION['club_error'] = 'No está disponible la carpeta para guardar logos.';
                header('Location: index.php?page=clubes&action=crear');
                exit;
            }

            $nombreLogo = bin2hex(random_bytes(16)) . '.' . $formatos[$mime];
            $rutaLogo = $directorio . $nombreLogo;

            if (!move_uploaded_file($archivo['tmp_name'], $rutaLogo)) {
                $_SESSION['club_error'] = 'No fue posible guardar el archivo del logo.';
                header('Location: index.php?page=clubes&action=crear');
                exit;
            }
        }

        try {
            $this->clubModel->crear([
                'nombre' => $nombre,
                'siglas' => $siglas,
                'logo' => $nombreLogo,
                'observaciones' => $observaciones
            ]);

            unset($_SESSION['club_old']);
            $_SESSION['success'] = 'El club se registró correctamente.';
            header('Location: index.php?page=clubes');
            exit;
        } catch (Throwable $e) {
            if ($rutaLogo && is_file($rutaLogo)) {
                @unlink($rutaLogo);
            }

            error_log('Error al registrar club: ' . $e->getMessage());
            $_SESSION['club_error'] = 'No fue posible registrar el club. Intenta nuevamente.';
            header('Location: index.php?page=clubes&action=crear');
            exit;
        }
    }

}
