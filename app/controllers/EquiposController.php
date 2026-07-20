<?php 

    require_once __DIR__.'../../core/Auth.php';

    require_once __DIR__.'../../models/Equipo.php';
    require_once __DIR__.'../../models/Liga.php';


    class EquiposController
    {
        private PDO $db;
        private $equipoModel;
        private $ligaModel;

        public function __construct(PDO $db){
            $this->db = $db;
            $this->equipoModel = new Equipo($db);
        }

        //listar equipos
        public function index(){
            //verificamos autenticacion
            if(!Auth::check()){
                header("Location: /afec/public/index.php?page=login");
                exit;
            }

            // 🔹 Ligas
            $this->ligaModel = new Liga($this->db);
            $ligas = $this->ligaModel->all();

            $ligasSelect = [];

            $estados = [
                ''              => 'Todos',
                'activo'        =>  'Activo',
                'inactivo'      =>  'Inactivo',
                'suspendido'    => 'Suspendido'
            ];

            foreach($ligas as $liga){
                $ligasSelect[$liga['id']] = $liga['nombre'];
            }

            // 🔹 Filtros
            $filtros = [
                'estado' => $_GET['estado'] ?? null,
                'liga'   => $_GET['liga'] ?? null,
                'buscar' => $_GET['buscar'] ?? null
            ];

            // 🔹 Paginación
            $total = $this->equipoModel->contar($filtros);
            $pagination = Pagination::paginate($total, 10);

            // 🔹 Consulta de datos al modelo
            $equipos = $this->equipoModel->listar(
                $filtros,
                $pagination['limit'],
                $pagination['offset']
            );

            // 🔹Configuracion del Toolbox
            $toolbox = [
                'create' => 'index.php?page=equipos&action=create',
                'filters'=>[
                    [
                        'type'=>'select',
                        'name'=>'liga',
                        'label'=>'Selecciona liga',
                        'options'=>$ligasSelect
                    ],
                    [
                        'type'=>'select',
                        'name'=>'estado',
                        'label'=>null,
                        'options'=>$estados,
                        'default' => 1
                    ],
                    [
                        'type'=>'search',
                        'name'=>'buscar',
                        'placeholder'=>'Buscar equipo'
                    ]
                ]
            ];

            // 🔥 TABLA FINAL
            $tableData = TableService::make($equipos, 'equipos');
            $columns = $tableData['columns'];
            $rows = $tableData['rows'];

            //comprobamos si se mando algo por ajax y lo mostramos
            if(isset($_GET['ajax'])){
                include ROOT_PATH.'/admin/views/components/table-content.php';
                return; 
            }

            // 🔥 VISTA
            view('pages.equipos.index.php',[
                'equipos'   => $equipos,
                'ligas'     => $ligas,
                'columns'   => $columns,
                'rows'      => $rows,
                'toolbox'   => $toolbox,
                'pagination'=> $pagination
            ]);
        }

        // Ver detalle de equipo
        public function detalles($id) {

            $equipo = $this->equipoModel->getById($id);
            //$directivos = $this->ligaModel->getDirectivos($id);
            //$documentos = $this->ligaModel->getDocumentos($id);
            //$resumen = $this->ligaModel->getResumen($id);
            view('pages.equipos.detalle.php',[
                'equipo'        => $equipo
            ]);
           
        }

        public function dashboard(){
            $equipo = $this->equipoModel->getById(10);
            $viewPath = ROOT_PATH.'/admin/views/pages/equipos/dashboard.php';
            if(!file_exists($viewPath)){
                $viewPath = ROOT_PATH.'/admin/views/404.php';
            }
            include ROOT_PATH.'/admin/views/layout.php';

        }

        public function guardarEscudo()
        {
            header('Content-Type: application/json; charset=utf-8');

            if (!isset($_FILES['escudo'], $_POST['equipo_id'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                exit;
            }

            $equipoId = (int) $_POST['equipo_id'];
            $archivo = $_FILES['escudo'];

            $equipo = $this->equipoModel->getById($equipoId);
            if (!$equipo) {
                echo json_encode(['success' => false, 'message' => 'Liga no encontrada']);
                exit;
            }

            $carpeta = ROOT_PATH . '/public/assets/img/equipos/';
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nuevoNombre = 'equipo_' . $equipoId . '_' . time() . '.' . $extension;
            $rutaNueva = $carpeta . $nuevoNombre;

            if (!move_uploaded_file($archivo['tmp_name'], $rutaNueva)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el escudo']);
                exit;
            }

            if (!file_exists($rutaNueva)) {
                echo json_encode(['success' => false, 'message' => 'El archivo no se guardó correctamente']);
                exit;
            }

            if (!$this->equipoModel->actualizarEscudo($equipoId, $nuevoNombre)) {
                @unlink($rutaNueva);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar base de datos']);
                exit;
            }

            if (!empty($equipo['escudo']) && $equipo['escudo'] !== 'default.png' && $equipo['escudo'] !== $nuevoNombre) {
                $rutaAnterior = $carpeta . $equipo['escudo'];
                if (file_exists($rutaAnterior)) {
                    @unlink($rutaAnterior);
                }
            }

            echo json_encode(['success' => true, 'escudo' => $nuevoNombre]);
            exit;
        }

        //funcion para guardar generales
        public function guardarGenerales(){
            header('Content-Type: application/json');
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $equipoId = $_POST['equipo_id'] ?? null;

                if(!$equipoId){
                    echo json_encode([
                        'success'   => false,
                        'message'   => 'ID de equipo no recibido'
                    ]);
                    exit;
                }

                //armamos array con generales
                $generales = [
                    'nombreEquipo'      => $_POST['nombreEquipo'] ?? null,
                    'siglas'            => $_POST['siglas'] ?? null,
                    'observaciones'     => $_POST['observaciones'] ?? null
                ];

                //validar que el campo nombre no venga vacio
                if (empty($generales['nombreEquipo'])){
                    echo json_encode([
                        'success'   => false,
                        'type'      => 'validation',
                        'message'   => 'El nombre del equipo no debe venir vacio'
                    ]);
                    exit;
                }
                    
                //si todo en orden ejecutamos update
                $affected = $this->equipoModel->guardarGenerales($equipoId, $generales);

                if ($affected > 0) {
                    $type = 'updated';
                    $message = 'Datos generales actualizados correctamente';
                } else {
                   $type ='no changes';
                   $message = 'No hubo cambios en los generales';
                }
                echo json_encode([
                    'success'   => true,
                    'type'      => $type,
                    'mesage'    => $message,
                    'data'      =>[
                        'nombre'            =>      $generales['nombreEquipo'],
                        'siglas'            =>      $generales['siglas'],
                        'observaciones'     =>      $generales['observaciones']
                    ] 
                ]);
                exit;
            }
        }

        //funcion para guardar direccion
        public function guardarDireccion(){
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $equipoId = $_POST['equipo_id'] ?? null;

                if (!$equipoId) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'ID de equipo no recibido'
                    ]);
                    exit;
                }

                //armamos la direccion
                $direccion = [
                    'calle' => $_POST['calle'] ?? null,
                    'colonia' => $_POST['colonia'] ?? null,
                    'codigo_postal' => $_POST['codigo_postal'] ?? null,
                    'ciudad' => $_POST['ciudad'] ?? null,
                    'estado_direccion' => $_POST['estado_direccion'] ?? null,
                    'pais' => $_POST['pais'] ?? null,
                ];

                //validamos si viene completamente vacia
                $direccionFiltrada = array_filter($direccion, fn($v)=> !empty(trim($v)));

                if(empty($direccionFiltrada)){
                    echo json_encode([
                        'success'   => false,
                        'type'      => 'validation',
                        'message'   => 'Debes ingresar al menos un dato de la direccion'
                    ]);
                    exit;
                }

                //si no viene vacia ejecutamos el update
                $affected = $this->equipoModel->guardarDireccion($equipoId, $direccion);
                // construir texto
                $direccionTexto = trim(implode(', ', $direccionFiltrada));

                if ($affected > 0) {
                    $type = 'updated';
                    $message = 'Direccion actualizada correctamente';
                } else {
                   $type ='no changes';
                   $message = 'No hubo cambios en la direccion';
                }
                echo json_encode([
                    'success'   => true,
                    'type'      => $type,
                    'mesage'    => $message,
                    'direccion' => $direccionTexto
                ]);
                exit;
            }
        }

        //funcion para guardar Contacto
        public function guardarContacto(){
            header('Content-Type: application/json');
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $equipoId = $_POST['equipo_id'] ?? null;

                if (!$equipoId){
                    echo json_encode([
                        'success'   => false,
                        'message'   => 'Id del equipo no proporcionado'   
                    ]);
                    exit;
                }
                $contacto = [
                    'email'     => $_POST['email'],
                    'telefono'  => $_POST['telefono'],
                    'redes'     => $_POST['redes']
                ];
                $success = $this->equipoModel->guardarContacto($equipoId, $contacto);

                if ($success){
                    echo json_encode([
                        'success'       => true,
                        'contacto'       => $contacto
                    ]);
                }else{
                    echo json_encode([
                        'success'       => false,
                        'message'       => 'No se pudo guardar el contacto'
                    ]);
                }
                exit;
            }
        }
     
        // Vista para crear equipo
        public function create() {
            $this->ligaModel = new Liga($this->db);

            $ligas = $this->ligaModel->all();

            $viewPath = ROOT_PATH.'/admin/views/pages/equipos/create.php';
            if(!file_exists($viewPath)){
                $viewPath = ROOT_PATH.'/admin/views/404.php';
            }

            include ROOT_PATH.'/admin/views/layout.php';
        }

        // Guardar nuevo equipo
        public function store() {
            if($_SERVER['REQUEST_METHOD'] === 'POST') 
            {
                $data = $_POST;

                if(empty($data['nombre'])) {
                    $error = "El nombre del equipo es obligatorio";
                    require 'views/ligas/create.php';
                    return;
                }

                $equipoId = $this->equipoModel->crear($data);
                header("Location: index.php?page=equipos&action=detalles&id=".$equipoId);
            }
        }

    }
    
    
?>