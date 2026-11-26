<?php

declare(strict_types=1);

require_once ROOT_PATH . '/app/models/Organismo.php';
require_once ROOT_PATH . '/app/services/AuthorizationService.php';

class OrganismosController
{
    private Organismo $organismoModel;
    private PDO $db;
    private AuthorizationService $authorization;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->organismoModel = new Organismo($db);
        $this->authorization = new AuthorizationService($db);
    }

    private function verificarAcceso(?int $organismoId = null, bool $soloAdmin = false): void
    {
        if (Auth::role() === 'ADMIN') return;
        if ($soloAdmin || $organismoId === null ||
            !in_array(Auth::role(), ['ORGANISMO', 'SUPERVISOR'], true) ||
            !$this->authorization->hasScope('organismo', $organismoId)) {
            http_response_code(403);
            view('403.php');
            exit;
        }
    }

    private function organismosAutorizados(): ?array
    {
        return Auth::role() === 'ADMIN' ? null :
            ($this->authorization->getScopedEntityIds('organismo') ?? []);
    }

    private function guardarLogo(?array $archivo, ?string $logoActual = null): array
    {
        if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [$logoActual, null];
        }
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($archivo['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('El logotipo debe cargarse correctamente y no superar 2 MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        $formatos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($formatos[$mime]) || @getimagesize($archivo['tmp_name']) === false) {
            throw new RuntimeException('El logotipo debe ser una imagen JPG, PNG o WEBP válida.');
        }
        $directorio = ROOT_PATH . '/public/assets/img/organismos/';
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            throw new RuntimeException('No está disponible la carpeta de logotipos de organismos.');
        }
        $nombre = bin2hex(random_bytes(16)) . '.' . $formatos[$mime];
        $ruta = $directorio . $nombre;
        if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
            throw new RuntimeException('No fue posible guardar el logotipo del organismo.');
        }
        return [$nombre, $ruta];
    }

    public function index(): void
    {
        if (Auth::role() === 'ORGANISMO') {
            $ids = $this->organismosAutorizados() ?? [];
            if (count($ids) !== 1) {
                http_response_code(403);
                view('403.php');
                exit;
            }
            header('Location: index.php?page=organismos&action=detalles&id=' . (int) $ids[0]);
            exit;
        }

        $filtros = [
            'estado' => trim((string) ($_GET['estado'] ?? '')),
            'tipo' => trim((string) ($_GET['tipo'] ?? '')),
            'buscar' => trim((string) ($_GET['buscar'] ?? '')),
            'ids' => $this->organismosAutorizados()
        ];

        $pagina = max(1, (int) ($_GET['p'] ?? 1));
        $limite = 10;
        $total = $this->organismoModel->contar($filtros);
        $pagination = Pagination::paginate($total, $limite);
        $offset = $pagination['offset'] ?? (($pagina - 1) * $limite);

        $organismos = $this->organismoModel->listar($filtros, $limite, $offset);
        $metricas = $this->organismoModel->metricas($filtros['ids']);

        $kpis = [
            ['title' => 'Organismos registrados', 'value' => $metricas['total'], 'icon' => 'fas fa-sitemap', 'class' => 'info'],
            ['title' => 'Organismos activos', 'value' => $metricas['activos'], 'icon' => 'fas fa-check-circle', 'class' => 'success'],
            ['title' => 'Organismos inactivos', 'value' => $metricas['inactivos'], 'icon' => 'fas fa-ban', 'class' => 'danger']
        ];

        $toolbox = [
            'create' => Auth::role() === 'ADMIN' ? 'index.php?page=organismos&action=crear' : null,
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
        $this->verificarAcceso(null, true);
        $error = $_SESSION['organismo_error'] ?? null;
        unset($_SESSION['organismo_error']);
        $old = $_SESSION['organismo_old'] ?? [];
        unset($_SESSION['organismo_old']);
        view('pages.organismos.create.php', compact('error', 'old'));
    }

    public function store(): void
    {
        $this->verificarAcceso(null, true);

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
            'logo' => null
        ];

        $_SESSION['organismo_old'] = $data;

        if ($data['nombre'] === '' || $data['tipo'] === '') {
            $_SESSION['organismo_error'] = 'El nombre y el tipo de organismo son obligatorios.';
            header('Location: index.php?page=organismos&action=crear');
            exit;
        }

        $rutaLogoNuevo = null;
        try {
            [$data['logo'], $rutaLogoNuevo] = $this->guardarLogo($_FILES['logo'] ?? null);
            $this->organismoModel->crear($data);
            unset($_SESSION['organismo_old']);
            $_SESSION['success'] = 'El organismo se registró correctamente.';
            header('Location: index.php?page=organismos');
            exit;
        } catch (Throwable $e) {
            if ($rutaLogoNuevo && is_file($rutaLogoNuevo)) @unlink($rutaLogoNuevo);
            error_log('Error al registrar organismo: ' . $e->getMessage());
            $_SESSION['organismo_error'] = $e instanceof PDOException ? 'No fue posible registrar el organismo.' : $e->getMessage();
            header('Location: index.php?page=organismos&action=crear');
            exit;
        }
    }

    public function detalles($id): void
    {
        $id = (int) $id;
        $this->verificarAcceso($id);
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
            'solicitudesVinculacion' => $this->organismoModel->listarSolicitudesVinculacion($id),
            'errorRelacion' => $errorRelacion
        ]);
    }

    public function editar($id): void
    {
        $this->verificarAcceso((int) $id);
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
        $id = (int) $id;
        $this->verificarAcceso($id);
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
            'fecha_creacion' => trim((string) ($_POST['fecha_creacion'] ?? '')),
            'logo' => $organismo['logo'] ?? null
        ];
        $_SESSION['organismo_edit_old'] = $data;
        $tipos = ['asociacion', 'federacion', 'comite', 'otro'];
        $estados = ['activo', 'inactivo'];
        if ($data['nombre'] === '' || !in_array($data['tipo'], $tipos, true) || !in_array($data['estado'], $estados, true)) {
            $_SESSION['organismo_edit_error'] = 'Verifica el nombre, tipo y estado del organismo.';
            header('Location: index.php?page=organismos&action=editar&id=' . $id);
            exit;
        }
        $logoAnterior = $organismo['logo'] ?? null;
        $rutaLogoNuevo = null;
        try {
            [$data['logo'], $rutaLogoNuevo] = $this->guardarLogo($_FILES['logo'] ?? null, $logoAnterior);
            $this->organismoModel->actualizar($id, $data);
            if ($rutaLogoNuevo && $logoAnterior && $logoAnterior !== 'default.png') {
                $rutaAnterior = ROOT_PATH . '/public/assets/img/organismos/' . $logoAnterior;
                if (is_file($rutaAnterior)) @unlink($rutaAnterior);
            }
            unset($_SESSION['organismo_edit_old']);
            $_SESSION['success'] = 'La información del organismo se actualizó correctamente.';
            header('Location: index.php?page=organismos&action=detalles&id=' . $id);
            exit;
        } catch (Throwable $e) {
            if ($rutaLogoNuevo && is_file($rutaLogoNuevo)) @unlink($rutaLogoNuevo);
            error_log('Error al actualizar organismo: ' . $e->getMessage());
            $_SESSION['organismo_edit_error'] = $e instanceof PDOException ? 'No fue posible actualizar el organismo.' : $e->getMessage();
            header('Location: index.php?page=organismos&action=editar&id=' . $id);
            exit;
        }
    }


    public function vincularLiga($id): void
    {
        $organismoId = (int) $id;
        if (Auth::role() !== 'ORGANISMO' || !$this->authorization->hasScope('organismo', $organismoId)) {
            http_response_code(403);
            view('403.php');
            return;
        }
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

    public function resolverSolicitudVinculacion($id): void
    {
        $organismoId = (int) $id;
        if (Auth::role() !== 'ORGANISMO' || !$this->authorization->hasScope('organismo', $organismoId)) {
            http_response_code(403);
            view('403.php');
            return;
        }
        $url = 'index.php?page=organismos&action=detalles&id=' . $organismoId;
        $solicitudId = (int) ($_POST['solicitud_id'] ?? 0);
        $decision = trim((string) ($_POST['decision'] ?? ''));
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $solicitudId <= 0 || !in_array($decision, ['aprobar', 'rechazar'], true)) {
            $_SESSION['organismo_relacion_error'] = 'La solicitud o decisión no es válida.';
            header('Location: ' . $url);
            exit;
        }
        try {
            $this->organismoModel->resolverSolicitudVinculacion(
                $organismoId,
                $solicitudId,
                (int) Auth::id(),
                $decision,
                [
                    'tipo_relacion' => $_POST['tipo_relacion'] ?? 'afiliacion',
                    'es_gestor' => isset($_POST['es_gestor']),
                    'observaciones' => trim((string) ($_POST['observaciones'] ?? '')),
                    'respuesta' => trim((string) ($_POST['respuesta'] ?? ''))
                ]
            );
            $_SESSION['success'] = $decision === 'aprobar'
                ? 'La solicitud fue aprobada y la relación institucional quedó registrada.'
                : 'La solicitud fue rechazada.';
        } catch (Throwable $e) {
            error_log('Error al resolver solicitud de vinculación: ' . $e->getMessage());
            $_SESSION['organismo_relacion_error'] = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'No fue posible resolver la solicitud.';
        }
        header('Location: ' . $url . '#solicitudes-vinculacion');
        exit;
    }

    public function finalizarRelacion($id): void
    {
        $organismoId = (int) $id;
        if (Auth::role() !== 'ORGANISMO' || !$this->authorization->hasScope('organismo', $organismoId)) {
            http_response_code(403);
            view('403.php');
            return;
        }
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
