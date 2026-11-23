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


    /**
     * Muestra el formulario para registrar una liga.
     */
    public function crear(): void
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        view('pages.ligas.create.php', [
            'errors' => $errors,
            'old' => $old
        ]);
    }

    /**
     * Valida y registra una liga independiente.
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=ligas');
            exit;
        }

        $data = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'siglas' => trim((string) ($_POST['siglas'] ?? '')),
            'estado' => trim((string) ($_POST['estado'] ?? 'activo')),
            'correo' => trim((string) ($_POST['contacto_email'] ?? '')),
            'telefono' => trim((string) ($_POST['contacto_telefono'] ?? '')),
            'notas' => trim((string) ($_POST['notas'] ?? ''))
        ];

        $errors = [];

        if ($data['nombre'] === '') {
            $errors[] = 'El nombre de la liga es obligatorio.';
        } elseif (mb_strlen($data['nombre']) > 150) {
            $errors[] = 'El nombre de la liga no puede superar los 150 caracteres.';
        }

        if ($data['siglas'] !== '' && mb_strlen($data['siglas']) > 30) {
            $errors[] = 'Las siglas no pueden superar los 30 caracteres.';
        }

        if (!in_array($data['estado'], ['activo', 'inactivo', 'suspendido'], true)) {
            $errors[] = 'El estado seleccionado no es válido.';
        }

        if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo de contacto no tiene un formato válido.';
        }

        if (mb_strlen($data['telefono']) > 30) {
            $errors[] = 'El teléfono no puede superar los 30 caracteres.';
        }

        if ($data['nombre'] !== '' && $this->ligaModel->existeNombre($data['nombre'])) {
            $errors[] = 'Ya existe una liga registrada con ese nombre.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=ligas&action=crear');
            exit;
        }

        try {
            $this->ligaModel->crear($data);
            flashSuccess('La liga se creó correctamente.');
            header('Location: index.php?page=ligas');
            exit;
        } catch (Throwable $e) {
            error_log('Error al crear liga: ' . $e->getMessage());
            $_SESSION['errors'] = ['No fue posible guardar la liga. Verifica los datos e inténtalo nuevamente.'];
            $_SESSION['old'] = $_POST;
            header('Location: index.php?page=ligas&action=crear');
            exit;
        }
    }

    /**
     * Muestra el formulario de edición de una liga dentro del alcance autorizado.
     */
    public function editar($id): void
    {
        $id = (int) $id;
        $scopeIds = $this->authorization->getScopedEntityIds('liga');
        $liga = $this->ligaModel->getById($id, $scopeIds);

        if (!$liga) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        $old = $_SESSION['old'] ?? [];
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['old'], $_SESSION['errors']);

        view('pages.ligas.edit.php', [
            'liga' => $liga,
            'old' => $old,
            'errors' => $errors
        ]);
    }

    /**
     * Valida y guarda los datos generales de una liga.
     */
    public function actualizar($id): void
    {
        $id = (int) $id;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=ligas');
            exit;
        }

        $scopeIds = $this->authorization->getScopedEntityIds('liga');
        $liga = $this->ligaModel->getById($id, $scopeIds);

        if (!$liga) {
            http_response_code(403);
            view('403.php');
            exit;
        }

        $data = [];
        foreach ([
            'nombre', 'siglas', 'estado', 'correo', 'telefono', 'direccion',
            'colonia', 'municipio', 'estado_republica', 'codigo_postal',
            'redes_sociales', 'notas'
        ] as $campo) {
            $data[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }

        $errors = [];
        if ($data['nombre'] === '') {
            $errors[] = 'El nombre de la liga es obligatorio.';
        } elseif (mb_strlen($data['nombre']) > 150) {
            $errors[] = 'El nombre no puede superar los 150 caracteres.';
        }

        if ($data['siglas'] !== '' && mb_strlen($data['siglas']) > 30) {
            $errors[] = 'Las siglas no pueden superar los 30 caracteres.';
        }
        if (!in_array($data['estado'], ['activo', 'inactivo', 'suspendido'], true)) {
            $errors[] = 'El estado seleccionado no es válido.';
        }
        if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no tiene un formato válido.';
        }
        if (mb_strlen($data['telefono']) > 30) {
            $errors[] = 'El teléfono no puede superar los 30 caracteres.';
        }
        if ($this->ligaModel->existeOtroNombre($data['nombre'], $id)) {
            $errors[] = 'Ya existe otra liga registrada con ese nombre.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: index.php?page=ligas&action=editar&id=' . $id);
            exit;
        }

        try {
            $this->ligaModel->actualizar($id, $data);
            flashSuccess('La información de la liga se actualizó correctamente.');
            header('Location: index.php?page=ligas&action=detalle&id=' . $id);
            exit;
        } catch (Throwable $e) {
            error_log('Error al actualizar liga ' . $id . ': ' . $e->getMessage());
            $_SESSION['errors'] = ['No fue posible actualizar la liga. Verifica los datos e inténtalo nuevamente.'];
            $_SESSION['old'] = $data;
            header('Location: index.php?page=ligas&action=editar&id=' . $id);
            exit;
        }
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

        // La liga utiliza la temporada de su organismo gestor; si es independiente,
        // utiliza exclusivamente su propia temporada.
        $organismoGestor = null;
        foreach (($liga['organismos'] ?? []) as $organismo) {
            if ((int) ($organismo['es_gestor'] ?? 0) === 1) {
                $organismoGestor = $organismo;
                break;
            }
        }

        $temporadaActual = $organismoGestor
            ? $this->temporadaModel->getActiva('organismo', (int) $organismoGestor['id'])
            : $this->temporadaModel->getActiva('liga', $id);

        $puedeResolverTemporadas = $organismoGestor
            ? $this->authorization->hasScope('organismo', (int) $organismoGestor['id'])
            : $this->authorization->hasScope('liga', $id);

        $temporadasVinculadas = $this->ligaModel->listarTemporadasVinculadas($id);
        $temporadasDisponibles = $this->temporadaModel->listarDisponiblesParaLiga(
            $id,
            $organismoGestor ? (int) $organismoGestor['id'] : null
        );
        $ligaTemporada = $temporadaActual
            ? $this->ligaModel->obtenerVinculacionTemporada($id, (int) $temporadaActual['id'])
            : null;
        $equiposDisponibles = ($ligaTemporada && $temporadaActual)
            ? $this->ligaModel->listarEquiposDisponibles($id, (int) $temporadaActual['id'])
            : [];
        $temporadasVinculadasIds = array_map(
            static fn(array $item): int => (int) $item['temporada_id'],
            $temporadasVinculadas
        );
        $temporadasDisponibles = array_values(array_filter(
            $temporadasDisponibles,
            static fn(array $item): bool => !in_array((int) $item['id'], $temporadasVinculadasIds, true)
        ));

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
                'temporadasDisponibles' => $temporadasDisponibles,
                'temporadasVinculadas' => $temporadasVinculadas,
                'puedeResolverTemporadas' => $puedeResolverTemporadas,
                'equiposLiga' => $equiposLiga,
                'ligaTemporada' => $ligaTemporada,
                'equiposDisponibles' => $equiposDisponibles
            ]
        );
    }

    /**
     * Solicita la vinculación de una temporada perteneciente al ámbito de la liga.
     */
    public function vincularTemporada(): void
    {
        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $temporadaId = (int) ($_POST['temporada_id'] ?? 0);
        $scopeIds = $this->authorization->getScopedEntityIds('liga');
        $liga = $this->ligaModel->getById($ligaId, $scopeIds);

        if (!$liga || $temporadaId <= 0) {
            http_response_code(403);
            view('403.php');
            return;
        }

        $organismoGestor = null;
        foreach (($liga['organismos'] ?? []) as $organismo) {
            if ((int) ($organismo['es_gestor'] ?? 0) === 1) {
                $organismoGestor = $organismo;
                break;
            }
        }

        $tipoEsperado = $organismoGestor ? 'organismo' : 'liga';
        $propietarioEsperado = $organismoGestor ? (int) $organismoGestor['id'] : $ligaId;
        $temporada = $this->temporadaModel->getById($temporadaId);
        $propietarioReal = $temporada
            ? (int) ($temporada['organismo_id'] ?: $temporada['liga_propietaria_id'])
            : 0;

        if (!$temporada || $temporada['ambito_tipo'] !== $tipoEsperado || $propietarioReal !== $propietarioEsperado) {
            flashError('La temporada seleccionada no pertenece al ámbito de esta liga.');
            header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#temporada');
            exit;
        }

        try {
            $this->ligaModel->vincularTemporada($ligaId, $temporadaId);
            flashSuccess('La temporada quedó vinculada como solicitud pendiente.');
        } catch (PDOException $e) {
            flashError('La temporada ya está vinculada o no fue posible registrar la solicitud.');
        }

        header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#temporada');
        exit;
    }

    /**
     * Resuelve una solicitud de vinculación de temporada.
     * El organismo gestor autoriza las ligas gestionadas; las ligas independientes se autorizan a sí mismas.
     */
    public function resolverVinculacionTemporada(): void
    {
        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $ligaTemporadaId = (int) ($_POST['liga_temporada_id'] ?? 0);
        $decision = trim((string) ($_POST['decision'] ?? ''));

        $liga = $this->ligaModel->getById($ligaId);
        if (!$liga || $ligaTemporadaId <= 0 || !in_array($decision, ['aprobar', 'rechazar'], true)) {
            http_response_code(403);
            view('403.php');
            return;
        }

        $organismoGestor = null;
        foreach (($liga['organismos'] ?? []) as $organismo) {
            if ((int) ($organismo['es_gestor'] ?? 0) === 1) {
                $organismoGestor = $organismo;
                break;
            }
        }

        $autorizado = $organismoGestor
            ? $this->authorization->hasScope('organismo', (int) $organismoGestor['id'])
            : $this->authorization->hasScope('liga', $ligaId);

        if (!$autorizado) {
            http_response_code(403);
            view('403.php');
            return;
        }

        try {
            $estado = $decision === 'aprobar' ? 'activo' : 'rechazado';
            $resuelto = $this->ligaModel->resolverVinculacionTemporada(
                $ligaId,
                $ligaTemporadaId,
                $estado
            );

            if ($resuelto) {
                flashSuccess($decision === 'aprobar'
                    ? 'La vinculación de la temporada fue aprobada.'
                    : 'La solicitud de vinculación fue rechazada.');
            } else {
                flashError('La solicitud ya fue resuelta o no pertenece a esta liga.');
            }
        } catch (Throwable $e) {
            error_log('Error al resolver vinculación de temporada: ' . $e->getMessage());
            flashError('No fue posible resolver la solicitud de vinculación.');
        }

        header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#temporada');
        exit;
    }


    /**
     * Registra una solicitud de inscripción de equipo en la temporada actual de la liga.
     */
    public function inscribirEquipo(): void
    {
        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $temporadaId = (int) ($_POST['temporada_id'] ?? 0);
        $equipoId = (int) ($_POST['equipo_id'] ?? 0);
        $numeroRegistro = trim((string) ($_POST['numero_registro'] ?? ''));

        $scopeIds = $this->authorization->getScopedEntityIds('liga');
        $liga = $this->ligaModel->getById($ligaId, $scopeIds);

        if (!$liga || $temporadaId <= 0 || $equipoId <= 0) {
            http_response_code(403);
            view('403.php');
            return;
        }

        $temporada = $this->temporadaModel->getById($temporadaId);
        $organismoGestor = null;
        foreach (($liga['organismos'] ?? []) as $organismo) {
            if ((int) ($organismo['es_gestor'] ?? 0) === 1) {
                $organismoGestor = $organismo;
                break;
            }
        }
        $tipoEsperado = $organismoGestor ? 'organismo' : 'liga';
        $propietarioEsperado = $organismoGestor ? (int) $organismoGestor['id'] : $ligaId;
        $propietarioReal = $temporada
            ? (int) ($temporada['organismo_id'] ?: $temporada['liga_propietaria_id'])
            : 0;

        if (
            !$temporada
            || $temporada['ambito_tipo'] !== $tipoEsperado
            || $propietarioReal !== $propietarioEsperado
            || !$this->ligaModel->obtenerVinculacionTemporada($ligaId, $temporadaId)
        ) {
            flashError('La temporada no está vinculada correctamente con esta liga.');
            header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#equipos');
            exit;
        }

        try {
            $registrado = $this->ligaModel->inscribirEquipo(
                $ligaId,
                $temporadaId,
                $equipoId,
                $numeroRegistro !== '' ? $numeroRegistro : null
            );
            if (!$registrado) {
                flashError('No se pudo registrar la solicitud de inscripción.');
            } else {
                flashSuccess('La solicitud de inscripción del equipo quedó pendiente de revisión.');
            }
        } catch (PDOException $e) {
            flashError('El equipo ya está inscrito o no fue posible registrar la solicitud.');
        }

        header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#equipos');
        exit;
    }


    /**
     * Aprueba o rechaza una solicitud pendiente de inscripción de equipo.
     */
    public function resolverInscripcionEquipo(): void
    {
        $ligaId = (int) ($_POST['liga_id'] ?? 0);
        $ligaEquipoId = (int) ($_POST['liga_equipo_id'] ?? 0);
        $decision = trim((string) ($_POST['decision'] ?? ''));

        $scopeIds = $this->authorization->getScopedEntityIds('liga');
        $liga = $this->ligaModel->getById($ligaId, $scopeIds);

        if (!$liga || $ligaEquipoId <= 0 || !in_array($decision, ['aprobar', 'rechazar'], true)) {
            http_response_code(403);
            view('403.php');
            return;
        }

        try {
            $estado = $decision === 'aprobar' ? 'activo' : 'rechazado';
            $resuelto = $this->ligaModel->resolverInscripcionEquipo($ligaId, $ligaEquipoId, $estado);

            if ($resuelto) {
                flashSuccess($decision === 'aprobar'
                    ? 'La inscripción del equipo fue aprobada.'
                    : 'La solicitud de inscripción fue rechazada.');
            } else {
                flashError('La solicitud no está pendiente, no pertenece a esta liga o la temporada aún no ha sido autorizada.');
            }
        } catch (Throwable $e) {
            error_log('Error al resolver inscripción de equipo: ' . $e->getMessage());
            flashError('No fue posible resolver la solicitud de inscripción.');
        }

        header('Location: index.php?page=ligas&action=detalle&id=' . $ligaId . '#equipos');
        exit;
    }


}

?>