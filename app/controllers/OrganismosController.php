<?php

declare(strict_types=1);

require_once ROOT_PATH . '/app/models/Organismo.php';

class OrganismosController
{
    private Organismo $organismoModel;

    public function __construct(PDO $db)
    {
        $this->organismoModel = new Organismo($db);
    }

    private function verificarAcceso(): void
    {
        if (!in_array(Auth::role(), ['ADMIN', 'SUPERVISOR'], true)) {
            http_response_code(403);
            view('403.php');
            exit;
        }
    }

    public function index(): void
    {
        $this->verificarAcceso();

        $filtros = [
            'estado' => trim((string) ($_GET['estado'] ?? '')),
            'tipo' => trim((string) ($_GET['tipo'] ?? '')),
            'buscar' => trim((string) ($_GET['buscar'] ?? ''))
        ];

        $pagina = max(1, (int) ($_GET['p'] ?? 1));
        $limite = 10;
        $total = $this->organismoModel->contar($filtros);
        $pagination = Pagination::paginate($total, $limite);
        $offset = $pagination['offset'] ?? (($pagina - 1) * $limite);

        $organismos = $this->organismoModel->listar($filtros, $limite, $offset);
        $metricas = $this->organismoModel->metricas();

        $kpis = [
            ['title' => 'Organismos registrados', 'value' => $metricas['total'], 'icon' => 'fas fa-sitemap', 'class' => 'info'],
            ['title' => 'Organismos activos', 'value' => $metricas['activos'], 'icon' => 'fas fa-check-circle', 'class' => 'success'],
            ['title' => 'Organismos inactivos', 'value' => $metricas['inactivos'], 'icon' => 'fas fa-ban', 'class' => 'danger']
        ];

        $toolbox = [
            'create' => 'index.php?page=organismos&action=crear',
            'filters' => [
                ['type' => 'select', 'name' => 'estado', 'label' => 'Todos los estados', 'default' => '', 'options' => ['activo' => 'Activos', 'inactivo' => 'Inactivos']],
                ['type' => 'select', 'name' => 'tipo', 'label' => 'Todos los tipos', 'default' => '', 'options' => ['asociacion' => 'Asociación', 'federacion' => 'Federación', 'comite' => 'Comité', 'otro' => 'Otro']],
                ['type' => 'search', 'name' => 'buscar', 'placeholder' => 'Buscar organismo...']
            ]
        ];

        $tableData = TableService::make($organismos, 'organismos');
        $columns = $tableData['columns'];
        $rows = $tableData['rows'];
        $tableTitle = 'Catálogo de organismos';

        if (isset($_GET['ajax'])) {
            include ROOT_PATH . '/admin/views/components/table-content.php';
            return;
        }

        view('pages.organismos.index.php', compact(
            'organismos', 'filtros', 'columns', 'rows', 'toolbox',
            'pagination', 'kpis', 'metricas', 'tableTitle'
        ));
    }

    public function crear(): void
    {
        $this->verificarAcceso();
        $error = $_SESSION['organismo_error'] ?? null;
        unset($_SESSION['organismo_error']);
        $old = $_SESSION['organismo_old'] ?? [];
        unset($_SESSION['organismo_old']);
        view('pages.organismos.create.php', compact('error', 'old'));
    }

    public function store(): void
    {
        $this->verificarAcceso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=organismos');
            exit;
        }

        $data = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'siglas' => trim((string) ($_POST['siglas'] ?? '')),
            'tipo' => trim((string) ($_POST['tipo'] ?? '')),
            'alcance_territorial' => trim((string) ($_POST['alcance_territorial'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'correo' => trim((string) ($_POST['correo'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
            'sitio_web' => trim((string) ($_POST['sitio_web'] ?? '')),
            'fecha_creacion' => trim((string) ($_POST['fecha_creacion'] ?? '')),
            // El formulario de edición no cambia el logotipo; conservar el existente.
            'logo' => $organismo['logo'] ?? null
        ];

        $_SESSION['organismo_old'] = $data;

        if ($data['nombre'] === '' || $data['tipo'] === '') {
            $_SESSION['organismo_error'] = 'El nombre y el tipo de organismo son obligatorios.';
            header('Location: index.php?page=organismos&action=crear');
            exit;
        }

        try {
            $this->organismoModel->crear($data);
            unset($_SESSION['organismo_old']);
            $_SESSION['success'] = 'El organismo se registró correctamente.';
            header('Location: index.php?page=organismos');
            exit;
        } catch (Throwable $e) {
            error_log('Error al registrar organismo: ' . $e->getMessage());
            $_SESSION['organismo_error'] = 'No fue posible registrar el organismo.';
            header('Location: index.php?page=organismos&action=crear');
            exit;
        }
    }

    public function detalles($id): void
    {
        $this->verificarAcceso();
        $id = (int) $id;
        $organismo = $this->organismoModel->obtenerPorId($id);
        if (!$organismo) {
            http_response_code(404);
            view('pages.organismos.detalle.php', ['organismo' => null, 'ligas' => []]);
            return;
        }
        $errorRelacion = $_SESSION['organismo_relacion_error'] ?? null;
        unset($_SESSION['organismo_relacion_error']);
        view('pages.organismos.detalle.php', [
            'organismo' => $organismo,
            'ligas' => $this->organismoModel->obtenerLigas($id),
            'ligasDisponibles' => $this->organismoModel->listarLigasDisponibles($id),
            'errorRelacion' => $errorRelacion
        ]);
    }

    public function editar($id): void
    {
        $this->verificarAcceso();
        $organismo = $this->organismoModel->obtenerPorId((int) $id);
        if (!$organismo) {
            http_response_code(404);
            header('Location: index.php?page=organismos');
            exit;
        }
        $old = $_SESSION['organismo_edit_old'] ?? [];
        unset($_SESSION['organismo_edit_old']);
        $error = $_SESSION['organismo_edit_error'] ?? null;
        unset($_SESSION['organismo_edit_error']);
        view('pages.organismos.edit.php', compact('organismo', 'old', 'error'));
    }

    public function actualizar($id): void
    {
        $this->verificarAcceso();
        $id = (int) $id;
        $organismo = $this->organismoModel->obtenerPorId($id);
        if (!$organismo) {
            header('Location: index.php?page=organismos');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=organismos&action=editar&id=' . $id);
            exit;
        }

        $data = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'siglas' => trim((string) ($_POST['siglas'] ?? '')),
            'tipo' => trim((string) ($_POST['tipo'] ?? '')),
            'alcance_territorial' => trim((string) ($_POST['alcance_territorial'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'correo' => trim((string) ($_POST['correo'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
            'sitio_web' => trim((string) ($_POST['sitio_web'] ?? '')),
            'fecha_creacion' => trim((string) ($_POST['fecha_creacion'] ?? ''))
        ];
        $_SESSION['organismo_edit_old'] = $data;
        $tipos = ['asociacion', 'federacion', 'comite', 'otro'];
        $estados = ['activo', 'inactivo'];
        if ($data['nombre'] === '' || !in_array($data['tipo'], $tipos, true) || !in_array($data['estado'], $estados, true)) {
            $_SESSION['organismo_edit_error'] = 'Verifica el nombre, tipo y estado del organismo.';
            header('Location: index.php?page=organismos&action=editar&id=' . $id);
            exit;
        }
        try {
            $this->organismoModel->actualizar($id, $data);
            unset($_SESSION['organismo_edit_old']);
            $_SESSION['success'] = 'La información del organismo se actualizó correctamente.';
            header('Location: index.php?page=organismos&action=detalles&id=' . $id);
            exit;
        } catch (Throwable $e) {
            error_log('Error al actualizar organismo: ' . $e->getMessage());
            $_SESSION['organismo_edit_error'] = 'No fue posible actualizar el organismo.';
            header('Location: index.php?page=organismos&action=editar&id=' . $id);
            exit;
        }
    }


    public function vincularLiga($id): void
    {
        $this->verificarAcceso();
        $organismoId = (int) $id;
        $url = 'index.php?page=organismos&action=detalles&id=' . $organismoId;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $url);
            exit;
        }

        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $data = [
            'tipo_relacion' => trim((string) ($_POST['tipo_relacion'] ?? '')),
            'es_gestor' => isset($_POST['es_gestor']) ? 1 : 0,
            'fecha_inicio' => trim((string) ($_POST['fecha_inicio'] ?? '')),
            'referencia' => trim((string) ($_POST['referencia'] ?? '')),
            'observaciones' => trim((string) ($_POST['observaciones'] ?? ''))
        ];
        $tipos = ['gestion', 'afiliacion', 'colaboracion', 'otro'];
        if ($ligaId <= 0 || !in_array($data['tipo_relacion'], $tipos, true)
            || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $data['fecha_inicio'])
            || !checkdate((int) substr($data['fecha_inicio'], 5, 2), (int) substr($data['fecha_inicio'], 8, 2), (int) substr($data['fecha_inicio'], 0, 4))) {
            $_SESSION['organismo_relacion_error'] = 'Verifica la liga, el tipo de relación y la fecha de inicio.';
            header('Location: ' . $url);
            exit;
        }
        try {
            $this->organismoModel->vincularLiga($organismoId, $ligaId, $data);
            $_SESSION['success'] = 'La liga se vinculó correctamente al organismo.';
        } catch (Throwable $e) {
            error_log('Error al vincular liga con organismo: ' . $e->getMessage());
            $_SESSION['organismo_relacion_error'] = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'No fue posible vincular la liga. Intenta nuevamente.';
        }
        header('Location: ' . $url);
        exit;
    }

    public function finalizarRelacion($id): void
    {
        $this->verificarAcceso();
        $organismoId = (int) $id;
        $relacionId = (int) ($_POST['relacion_id'] ?? 0);
        $fechaFin = trim((string) ($_POST['fecha_fin'] ?? ''));
        $url = 'index.php?page=organismos&action=detalles&id=' . $organismoId;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $relacionId <= 0
            || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $fechaFin)
            || !checkdate((int) substr($fechaFin, 5, 2), (int) substr($fechaFin, 8, 2), (int) substr($fechaFin, 0, 4))) {
            $_SESSION['organismo_relacion_error'] = 'Indica una fecha de finalización válida.';
            header('Location: ' . $url);
            exit;
        }
        try {
            $this->organismoModel->finalizarRelacion($organismoId, $relacionId, $fechaFin);
            $_SESSION['success'] = 'La relación con la liga se finalizó. El historial se conservó.';
        } catch (Throwable $e) {
            error_log('Error al finalizar relación de organismo: ' . $e->getMessage());
            $_SESSION['organismo_relacion_error'] = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'No fue posible finalizar la relación.';
        }
        header('Location: ' . $url);
        exit;
    }

}
