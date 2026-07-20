<?php

require_once __DIR__.'../../core/Auth.php';

require_once __DIR__.'../../models/Liga.php';


class LigasController 
{

    private PDO $db;
    private $ligaModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->ligaModel = new Liga($db);
    }

    /* ============ LISTADOS ============ */

    // Listar todas las ligas
    public function index() {
        if(!Auth::check()){
            header("Location: /afec/public/index.php?page=login");
            exit;
        }
        
        //para filtro en tolbox
         $estados = [
                ''              => 'Todos',
                'activo'        =>  'Activa',
                'inactivo'      =>  'Inactiva',
                'suspendido'    =>  'Suspendida'
        ];

        //filtros
        $filtros = [
                'estado'    => $_GET['estado'] ?? null,
                'buscar'    => $_GET['buscar'] ?? null
        ];

        //paginacion
        $total = $this->ligaModel->contar($filtros);
        $pagination = Pagination::paginate($total, 10);

        //datos
        $ligas = $this->ligaModel->listar(
            $filtros, 
            $pagination['limit'], 
            $pagination['offset']
        );

        //toolbox
        $toolbox = [
            'create' => 'index.php?page=ligas&action=create',
            'filters' => [
                [
                    'type'=>'select',
                    'name'=>'estado',
                    'label'=>null,
                    'options'=>$estados,
                    'default' => ''
                ],
                [
                    'type' =>'search',
                    'name' =>'buscar',
                    'placeholder' => 'buscar liga'
                ]
            ],
        ];

        $tableData = TableService::make($ligas,'ligas');
        $columns = $tableData['columns'];
        $rows = $tableData['rows'];

        if(isset($_GET['ajax'])){
            include ROOT_PATH.'/admin/views/components/table-content.php';
            return; 
        }
     
        view('pages.ligas.index.php',[
                'ligas'         => $ligas,
                'columns'       => $columns,
                'rows'          => $rows,
                'toolbox'       => $toolbox,
                'pagination'    => $pagination
            ]
        );
    }

    // Listar ligas por estado (activa, inactiva, suspendida)
    public function porEstado($estado) {
        $ligas = $this->ligaModel->getByEstado($estado);
        require 'views/ligas/index.php';
    }

    /* ======== CREAR / EDITAR ============ */

    // Vista para crear liga
    public function create() {
        $viewPath = ROOT_PATH.'/admin/views/pages/ligas/create.php';
        if(!file_exists($viewPath)){
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        include ROOT_PATH.'/admin/views/layout.php';
    }

    // Guardar nueva liga
    public function store() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            if(empty($data['nombre'])) {
                $error = "El nombre de la liga es obligatorio";
                require 'views/ligas/create.php';
                return;
            }

            $ligaId = $this->ligaModel->crear($data);
            header("Location: index.php?page=ligas&action=detalles&id=".$ligaId);
        }
    }

    // Vista para editar liga
    public function edit($id) {
        $liga = $this->ligaModel->getById($id);
        $viewPath = ROOT_PATH.'/admin/views/pages/ligas/edit.php';
        if(!file_exists($viewPath)){
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        include ROOT_PATH.'/admin/views/layout.php';
    }

    // Actualizar liga
    public function update($id) {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->ligaModel->actualizar($id, $data);
            header("Location: index.php?page=ligas");
        }
    }

    /* ================ DETALLE / RESUMEN ================ */

    // Ver detalle de liga
    public function detalles($id) {

        $liga = $this->ligaModel->getById($id);
        $directivos = $this->ligaModel->getDirectivos($id);
        $documentos = $this->ligaModel->getDocumentos($id);
        $resumen = $this->ligaModel->getResumen($id);

        $viewPath = ROOT_PATH.'/admin/views/pages/ligas/detalle.php';
        if(!file_exists($viewPath)){
            $viewPath = ROOT_PATH.'/admin/views/404.php';
        }

        // 👇 estas variables quedan disponibles para la vista
        include ROOT_PATH.'/admin/views/layout.php';
    }

    /* ================ DIRECTIVOS ============= */

    public function addDirectivo() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $liga_id = $_POST['liga_id'];
            $afiliado_id = $_POST['afiliado_id'];
            $rol = $_POST['rol'];

            $this->ligaModel->addDirectivo($liga_id, $afiliado_id, $rol);
            header("Location: index.php?c=ligas&a=detalle&id=$liga_id");
        }
    }

    /* =============== DOCUMENTOS ================ */

    public function addDocumento() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $liga_id = $_POST['liga_id'];
            $tipo = $_POST['tipo'];
            $archivo = $_FILES['archivo']['name'];

            // Mover archivo a carpeta uploads/ligas
            $rutaDestino = "uploads/ligas/$archivo";
            move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino);

            $this->ligaModel->addDocumento($liga_id, $tipo, $archivo);
            header("Location: index.php?c=ligas&a=detalle&id=$liga_id");
        }
    }

    /* ============ ELIMINAR LIGA ================ */

    public function delete($id) {
        // Aquí podrías implementar lógica de seguridad o verificación de relaciones  
    }

    /* ============== AGREGAR DIRECCION A LA LIGA =========== */
    public function guardarDireccion()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $ligaId = $_POST['liga_id'] ?? null;

            if (!$ligaId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de liga no recibido'
                ]);
                exit;
            }

            $direccion = [
                'calle' => $_POST['calle'] ?? null,
                'colonia' => $_POST['colonia'] ?? null,
                'codigo_postal' => $_POST['codigo_postal'] ?? null,
                'ciudad' => $_POST['ciudad'] ?? null,
                'estado_direccion' => $_POST['estado_direccion'] ?? null,
                'pais' => $_POST['pais'] ?? null,
            ];

            $success = $this->ligaModel->guardarDireccion($ligaId, $direccion);

            if ($success) {
                $direccionTexto = trim(implode(', ', array_filter($direccion)));

                echo json_encode([
                    'success' => true,
                    'direccion' => $direccionTexto
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo guardar la dirección'
                ]);
            }
            exit;
        }

    }

    /* ========== FUNCION PARA GUARDAR/ACTUALIZAR EL ESCUDO ====== */
    public function guardarEscudo()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_FILES['escudo'], $_POST['liga_id'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $ligaId = (int) $_POST['liga_id'];
        $archivo = $_FILES['escudo'];

        $liga = $this->ligaModel->getById($ligaId);
        if (!$liga) {
            echo json_encode(['success' => false, 'message' => 'Liga no encontrada']);
            exit;
        }

        $carpeta = ROOT_PATH . '/public/assets/img/ligas/';
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nuevoNombre = 'liga_' . $ligaId . '_' . time() . '.' . $extension;
        $rutaNueva = $carpeta . $nuevoNombre;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaNueva)) {
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar el escudo']);
            exit;
        }

        if (!file_exists($rutaNueva)) {
            echo json_encode(['success' => false, 'message' => 'El archivo no se guardó correctamente']);
            exit;
        }

        if (!$this->ligaModel->actualizarEscudo($ligaId, $nuevoNombre)) {
            @unlink($rutaNueva);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar base de datos']);
            exit;
        }

        if (!empty($liga['escudo']) && $liga['escudo'] !== 'default.png' && $liga['escudo'] !== $nuevoNombre) {
            $rutaAnterior = $carpeta . $liga['escudo'];
            if (file_exists($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        echo json_encode(['success' => true, 'escudo' => $nuevoNombre]);
        exit;
    }

}
?>
