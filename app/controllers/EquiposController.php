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

            // El listado general muestra entidades equipo.
            // Los filtros de liga y estado pertenecen a sus relaciones,
            // no a la tabla base tb_equipos.
            $filtros = [
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
                'filters' => [
                    [
                        'type' => 'search',
                        'name' => 'buscar',
                        'placeholder' => 'Buscar equipo'
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
                'columns'   => $columns,
                'rows'      => $rows,
                'toolbox'   => $toolbox,
                'pagination'=> $pagination
            ]);
        }

        // Ver detalle de equipo
        public function detalles($id) {

            $equipo = $this->equipoModel->getById($id);

            if (!$equipo) {
                view('pages.equipos.detalle.php', [
                    'equipo' => null,
                    'clubes' => [],
                    'participaciones' => []
                ]);
                return;
            }

            $clubes = $this->equipoModel->obtenerClubes((int) $id);
            $participaciones = $this->equipoModel->obtenerParticipacionesLigas((int) $id);

            view('pages.equipos.detalle.php', [
                'equipo' => $equipo,
                'clubes' => $clubes,
                'participaciones' => $participaciones
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
     
        // Vista para crear equipo como entidad independiente.
        public function create()
        {
            $error = $_SESSION['equipo_error'] ?? null;
            unset($_SESSION['equipo_error']);

            $success = isset($_GET['created']) && $_GET['created'] === '1';

            view('pages.equipos.create.php', [
                'error' => $error,
                'success' => $success
            ]);
        }

        // Registrar la entidad base del equipo y su escudo opcional.
        public function store()
        {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=equipos&action=create');
                exit;
            }

            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $siglas = trim((string) ($_POST['siglas'] ?? ''));
            $observaciones = trim((string) ($_POST['observaciones'] ?? ''));

            $_SESSION['equipo_old'] = [
                'nombre' => $nombre,
                'siglas' => $siglas,
                'observaciones' => $observaciones
            ];

            if ($nombre === '') {
                $_SESSION['equipo_error'] = 'El nombre del equipo es obligatorio.';
                header('Location: index.php?page=equipos&action=create');
                exit;
            }

            $archivo = $_FILES['escudo'] ?? null;
            $nombreEscudo = null;
            $rutaEscudo = null;

            if ($archivo && $archivo['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($archivo['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['equipo_error'] = 'No se pudo recibir el archivo del escudo.';
                    header('Location: index.php?page=equipos&action=create');
                    exit;
                }

                if ($archivo['size'] > 2 * 1024 * 1024) {
                    $_SESSION['equipo_error'] = 'El escudo no debe superar los 2 MB.';
                    header('Location: index.php?page=equipos&action=create');
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
                    $_SESSION['equipo_error'] = 'El archivo debe ser una imagen JPG, PNG o WEBP válida.';
                    header('Location: index.php?page=equipos&action=create');
                    exit;
                }

                $directorio = ROOT_PATH . '/public/assets/img/equipos/';
                if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                    $_SESSION['equipo_error'] = 'No está disponible la carpeta para guardar escudos.';
                    header('Location: index.php?page=equipos&action=create');
                    exit;
                }

                $nombreEscudo = bin2hex(random_bytes(16)) . '.' . $formatos[$mime];
                $rutaEscudo = $directorio . $nombreEscudo;

                if (!move_uploaded_file($archivo['tmp_name'], $rutaEscudo)) {
                    $_SESSION['equipo_error'] = 'No fue posible guardar el archivo del escudo.';
                    header('Location: index.php?page=equipos&action=create');
                    exit;
                }
            }

            try {
                $equipoId = $this->equipoModel->crearEntidad([
                    'nombre' => $nombre,
                    'siglas' => $siglas,
                    'observaciones' => $observaciones,
                    'escudo' => $nombreEscudo
                ]);

                unset($_SESSION['equipo_old']);
                $_SESSION['success'] = 'El equipo se registró correctamente.';
                header('Location: index.php?page=equipos');
                exit;
            } catch (Throwable $e) {
                if ($rutaEscudo && is_file($rutaEscudo)) {
                    @unlink($rutaEscudo);
                }

                error_log('Error al registrar equipo: ' . $e->getMessage());
                $_SESSION['equipo_error'] = 'No fue posible registrar el equipo. Intenta nuevamente.';
                header('Location: index.php?page=equipos&action=create');
                exit;
            }
        }

    }
    
    
?>