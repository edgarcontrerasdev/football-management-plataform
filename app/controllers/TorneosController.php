<?php

    require_once __DIR__.'../../core/Auth.php';

    require_once __DIR__.'../../models/Torneo.php';
    require_once __DIR__.'../../models/Liga.php';
    require_once __DIR__.'../../models/Temporada.php';
    require_once __DIR__.'../../models/Equipo.php';

class TorneosController{

    private PDO $db;
    private $torneoModel;
    private $ligaModel;
    private $temporadaModel;
    private $equipoModel;

    public function __construct(PDO $db){
        $this->db = $db;
        $this->torneoModel = new Torneo($db);
    }

    public function index()
    {
         //verificamos autenticacion
        if (!Auth::check()){
            header("Location: /afec/public/index.php?page=login");
            exit;
        }

        //configuramos estados
        $estados = [
            ''              => 'Todos',
            'cerrados'      => 'Cerrados',
            'activos'       => 'Activos',
            'borrador'      => 'Borrador'
        ];

        //configuramos filtros
        $filtros = [
            'estado'        =>  $_GET['estado'] ?? null,
            'buscar'        =>  $_GET['buscar'] ?? null 
        ];

        //configuramos paginacion
        $total          =   $this->torneoModel->contar($filtros);
        $pagination     =   Pagination::paginate($total,10);

        //configuramos toolbox
        $toolbox =[
            'create'    =>  'index.php?page=torneos&action=create',
            'filters'   =>  [
                 [
                    'type'      =>  'select',
                    'name'      =>  'estado',
                    'label'     =>  null,
                    'options'   =>  $estados,
                    'default'   =>  1
                ],
                [
                    'type'          => 'search',
                    'name'          => 'buscar',
                    'placeholder'   => 'buscar temporada'
                ]
            ]
        ];

        $this->temporadaModel   =   new Temporada($this->db);
        $this->torneoModel      =   new Torneo($this->db);

        $tor = $this->torneoModel->listar($filtros, $pagination['limit'], $pagination['offset']);

        $temporadaActiva    =   $this->temporadaModel->getActiva();
        $torneos            =   $this->torneoModel->getAll();

        //tabla final
        $tableData  =   TableService::make($tor,'torneos');
        $columns    =   $tableData['columns'];
        $rows       =   $tableData['rows'];

        //configuracion de kpis
        $kpis = [
            [
                'title' => 'Total',
                'value' => $metricas['total'] ?? 0,
                'icon'  => 'fas fa-trophy',
                'class' => 'info'
            ],
            [
                'title' => 'Activos',
                'value' => $metricas['activos'] ?? 0,
                'icon'  => 'fas fa-play-circle',
                'class' => 'success'
            ],
            [
                'title' => 'Planeados',
                'value' => $metricas['planeados'] ?? 0,
                'icon'  => 'fas fa-clock',
                'class' => 'warning'
            ],
            [
                'title' => 'Finalizados',
                'value' => $metricas['finalizados'] ?? 0,
                'icon'  => 'fas fa-flag-checkered',
                'class' => 'danger'
            ]
        ];

        //comprobar si se mando algo por AJAX y se muestra
        if(isset($_GET['ajax'])){
            include ROOT_PATH.'/admin/views/components/table-content.php';
            return;
        }   
 
        //construimos vista y enviamos datos
       view('pages.torneos.index.php',[
            'torneos'           =>  $tor,
            'temporadaActiva'   =>  $temporadaActiva,
            'columns'           =>  $columns,
            'rows'              =>  $rows,
            'toolbox'           => $toolbox,
            'pagination'        =>  $pagination,
            'kpis'              => $kpis
       ]);

    }

    public function create(){
        unset($_SESSION['wizard_torneo']);

        $this->ligaModel = new Liga($this->db);
        $this->temporadaModel = new Temporada($this->db);
        $this->torneoModel = new Torneo($this->db);

        $ligas = $this->ligaModel->all();
        $temporadaActiva = $this->temporadaModel->getActiva();
        $torneos = $this->torneoModel->getAll();

       view('pages.torneos.create.php',[
            'ligas' => $ligas,
            'temporadaActiva' => $temporadaActiva,
            'torneos' => $torneos
       ]);

    }

    public function eliminar(){

        if(!Auth::check()){
            echo json_encode([
                'success' => false,
                'msj' => 'Usuario  no autorizado'
            ]);
            exit;
        }

        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            echo json_encode([
                'success' => false,
                'msj' => 'Metodo no permitido' 
            ]);
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if(!$id){
            echo json_encode([
                'success' => false,
                'msj' => 'No existe ID de torneo'
            ]);
            exit;
        }

        $resultado = $this->torneoModel->eliminar($id);

        echo json_encode([
            'success' => true,
            'msj' => $resultado
        ]);
        exit;
       
    }

    public function detalle() {
        $this->temporadaModel = new Temporada($this->db);
        $this->equipoModel = new Equipo($this->db);
        $this->torneoModel = new Torneo($this->db);

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=torneos');
            exit;
        }

        $temporadaActiva = $this->temporadaModel->getActiva();
        $torneo = $this->torneoModel->getById($id);
        $idLigaTorneo = $torneo['liga_id'];
    
        if (!$torneo) {
            header('Location: index.php?page=torneos');
            exit;
        }

         /**
         * CALCULAR PROGRESO DEL TORNEO
         */
        $progreso = [
            'datos' => (
                !empty($torneo['nombre']) &&
                !empty($torneo['tipo'])
            ),

            'costos' => 0,

            'equipos' => $this->torneoModel->tieneEquipos($id),

            'bloques' => $this->torneoModel->tieneBloques($id),

            'fixture' => $this->torneoModel->tieneFixture($id),

            'activado' => ($torneo['estado'] === 'ACTIVO')
        ];

        $equipos = $this->equipoModel->getByLiga($idLigaTorneo);
        $equiposInscritos = $this->equipoModel->getByTorneo($id);
        $totalEquiposInscritos = $this->torneoModel->totalEquiposInscritos($id);
        //solos IDs de inscritos
        $inscritosIds = array_column($equiposInscritos, 'equipo_id');

        //ruta de la vista detalle.php
        $viewPath = ROOT_PATH."/admin/views/pages/torneos/detalle.php";

        //si no existe la vista cargamos la vista de error 404
        if(!file_exists($viewPath)){
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        //incluimos el layout
        //el layout imprime la variable $viewPath
        //que es la que contiene la vista
        include ROOT_PATH.'/admin/views/layout.php';

    }

    public function asignarEquipos(){
        header('Content-Type: application/json');

        $this->torneoModel = new Torneo($this->db);
        $torneoId = $_POST['torneo_id'] ?? null;
        $equipos = $_POST['equipos'] ?? [];

        if(!$torneoId){
            echo json_encode([
                'success' => false,
                'msj' => 'No existe el torneo'
            ]);
            return;
        }
        //BORRAMOS Y REINSERTAMOS
        $this->torneoModel->limpiarEquipos($torneoId);
        foreach($equipos as $equipoId){
            $this->torneoModel->asignarEquipo($torneoId, $equipoId);
        }
        echo json_encode([
            'success' => 'ok'
        ]);
    }

    public function equiposInscritos($torneoId, $ligaId){
        $this->torneoModel = new Torneo($this->db);
        $this->equipoModel = new Equipo($this->db);
        //solos los IDs

    }

    public function guardarStep1()
    {
        header('Content-Type: application/json');

        $data = [
            'liga_id'      => $_POST['liga_id'] ?? null,
            'temporada_id' => $_POST['temporada_id'] ?? null,
            'nombre'       => $_POST['nombre_torneo'],
            'tipo'         => $_POST['tipo_torneo'],
        ];

        // 👉 SI NO EXISTE WIZARD ACTIVO
        if (empty($_SESSION['wizard_torneo']['torneo_id'])) {

            $data['estado'] = 'PLANEADO';

            $torneoId = $this->torneoModel->crear($data);

            $_SESSION['wizard_torneo'] = [
            'torneo_id' => $torneoId,
            'step'      => 1
            ];

            echo json_encode([
                'success' => true,
                'modo' => 'creado',
                'torneo_id' => $torneoId
            ]);
            return;
        }

        // 👉 SI YA EXISTE, SOLO SE ACTUALIZA
        $torneoId = $_SESSION['wizard_torneo']['torneo_id'];
        $data['estado'] = 'PLANEADO';

        $this->torneoModel->actualizar($torneoId, $data);

        echo json_encode([
            'success' => true,
            'modo' => 'actualizado',
            'torneo_id' => $torneoId
        ]);
    }

    public function guardarStep2()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['wizard_torneo']['torneo_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'No hay torneo en sesión'
            ]);
            return;
        }

        $torneoId = $_SESSION['wizard_torneo']['torneo_id'];

        $data = [
            'inscripcion'       => $_POST['inscripcion'] ?? 0,
            'fianza'            => $_POST['fianza'] ?? 0,
            'moneda'            => $_POST['moneda'] ?? 'MXN',
            'requiere_pagos'     => isset($_POST['requiere_pagos']) ? 1 : 0,
            'permite_invitados' => isset($_POST['permite_invitados']) ? 1 : 0,
        ];

        $this->torneoModel->actualizarStep2($torneoId, $data);

        echo json_encode([
            'success' => true,
            'torneo_id' => $torneoId
        ]);
    }

    public function guardarStep3()
    {
        header('Content-Type: application/json');
        if(!isset($_SESSION['wizard_torneo']['torneo_id'])){
            echo json_encode([
                'success' => false,
                'message' => 'No hay torneo en sesion'
            ]);
            return;
        }

        $torneoId = $_SESSION['wizard_torneo']['torneo_id'];
        $data = [
            'modalidad' => $_POST['modalidad'] ?? 'todos contra todos',
            'tiene_liguilla' => $_POST['tiene_liguilla'] ?? 0,
            'segunda_vuelta_puntos' =>$_POST['segunda_vuelta_puntos'] ?? 0,
            'permite_inconcluso' => $_POST['permite_inconcluso'] ?? 0,
        ];

        $this->torneoModel->actualizarStep3($torneoId, $data);
        echo json_encode([
            'success' => true,
            'torneo_id' => $torneoId
        ]);
    }

    public function guardarStep4()
    {
        header('Content-Type: application/json');

        // Validar wizard activo
        if (empty($_SESSION['wizard_torneo']['torneo_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'No hay torneo activo en el wizard'
            ]);
            return;
        }

        $torneoId = $_SESSION['wizard_torneo']['torneo_id'];

        $data = [
            'tipo_bloques'     => $_POST['tipo_bloques'] ?? 'uno',
            'comparte_bloques' => isset($_POST['comparte_bloques']) ? 1 : 0,
        ];

        $this->torneoModel->actualizarStep4($torneoId, $data);

        // Actualizar step actual del wizard
        $_SESSION['wizard_torneo']['step'] = 4;

        echo json_encode([
            'success'   => true,
            'torneo_id'=> $torneoId
        ]);
    }

    public function bloques()
    {
        $this->temporadaModel = new Temporada($this->db);
        $this->torneoModel = new Torneo($this->db);

        $torneoId = $_GET['id'] ?? null;

        if (!$torneoId) {
            header('Location: index.php?page=torneos');
            exit;
        }

        $torneo = $this->torneoModel->getById($torneoId);

        if (!$torneo) {
            header('Location: index.php?page=torneos');
            exit;
        }

        $temporadaActiva = $this->temporadaModel->getActiva();

        $bloquesDisponibles = $this->torneoModel->getBloquesDisponibles(
            $torneo['liga_id'],
            $temporadaActiva['id']
        );

        $bloquesTorneo = $this->torneoModel->getBloquesPorTorneo($torneoId);

        $viewPath = ROOT_PATH . "/admin/views/pages/torneos/bloques.php";

        if (!file_exists($viewPath)) {
            $viewPath = ROOT_PATH . '/admin/views/404.php';
        }

        include ROOT_PATH . '/admin/views/layout.php';
    }
    public function guardarBloque()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'msg' => 'Método no permitido'
            ]);
            return;
        }

        $torneoId = $_POST['torneo_id'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $formato = $_POST['formato'] ?? 'round_robin';
        $vueltas = $_POST['vueltas_planeadas'] ?? 1;

        if (!$torneoId || $nombre === '') {
            echo json_encode([
                'success' => false,
                'msg' => 'Faltan datos obligatorios'
            ]);
            return;
        }

        $torneo = $this->torneoModel->getById($torneoId);

        if (!$torneo) {
            echo json_encode([
                'success' => false,
                'msg' => 'Torneo no encontrado'
            ]);
            return;
        }

        try {
            $this->db->beginTransaction();

            $orden = $this->torneoModel->siguienteOrdenBloque($torneoId);

            $bloqueId = $this->torneoModel->crearBloqueCompetencia([
                ':nombre' => $nombre,
                ':temporada_id' => $torneo['temporada_id'],
                ':liga_id' => $torneo['liga_id'],
                ':tipo_bloque' => $_POST['tipo_bloque'] ?? 'oficial',
                ':formato' => $formato,
                ':vueltas_planeadas' => $vueltas,
                ':impacta_estadisticas' => isset($_POST['impacta_estadisticas']) ? 1 : 0,
                ':impacta_suspensiones' => isset($_POST['impacta_suspensiones']) ? 1 : 0,
                ':estado' => 'planeado'
            ]);

            $this->torneoModel->asociarBloqueATorneo([
                ':torneo_id' => $torneoId,
                ':bloque_id' => $bloqueId,
                ':orden' => $orden,
                ':impacta_puntos' => isset($_POST['impacta_puntos']) ? 1 : 0,
                ':impacta_suspensiones' => isset($_POST['impacta_suspensiones']) ? 1 : 0,
            ]);

            $this->db->commit();

            echo json_encode([
                'success' => true,
                'msg' => 'Bloque creado correctamente'
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();

            echo json_encode([
                'success' => false,
                'msg' => 'Error al guardar el bloque',
                'error' => $e->getMessage()
            ]);
        }
    }


}


?>