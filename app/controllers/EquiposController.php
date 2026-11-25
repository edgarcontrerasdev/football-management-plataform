<?php 

    require_once __DIR__ . '/../core/Auth.php';

    require_once __DIR__ . '/../models/Equipo.php';
    require_once __DIR__ . '/../models/Liga.php';


    class EquiposController
    {
        private PDO $db;
        private $equipoModel;
        private AuthorizationService $authorization;

        public function __construct(PDO $db){
            $this->db = $db;
            $this->equipoModel = new Equipo($db);
            $this->authorization = new AuthorizationService($db);
        }

        /**
         * IDs de equipos visibles según los alcances efectivos del usuario.
         * ADMIN es global; ORGANISMO y LIGA heredan equipos de sus ligas;
         * EQUIPO recibe únicamente el equipo asignado directamente.
         */
        private function equiposAutorizados(): ?array
        {
            $rol = Auth::role();

            if ($rol === 'ADMIN') {
                return null;
            }

            if ($rol === 'EQUIPO') {
                return $this->authorization->getScopedEntityIds('equipo') ?? [];
            }

            if (in_array($rol, ['ORGANISMO', 'LIGA'], true)) {
                $ligaIds = $this->authorization->getScopedEntityIds('liga') ?? [];
                if (!$ligaIds) {
                    return [];
                }

                $placeholders = [];
                $params = [];
                foreach (array_values($ligaIds) as $i => $ligaId) {
                    $key = ':liga_' . $i;
                    $placeholders[] = $key;
                    $params[$key] = (int) $ligaId;
                }

                $sql = "
                    SELECT DISTINCT le.equipo_id
                    FROM tb_liga_equipos le
                    INNER JOIN tb_liga_temporadas lt
                        ON lt.id = le.liga_temporada_id
                    WHERE lt.liga_id IN (" . implode(',', $placeholders) . ")
                      AND lt.estado = 'activo'
                      AND le.estado_participacion = 'activo'
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
            }

            // SUPERVISOR también requiere un alcance explícito.
            return $this->authorization->getScopedEntityIds('equipo') ?? [];
        }

        private function exigirAccesoEquipo(int $equipoId, bool $json = false): void
        {
            $ids = $this->equiposAutorizados();
            if ($ids === null || in_array($equipoId, $ids, true)) {
                return;
            }

            http_response_code(403);
            if ($json) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'No tienes autorización para administrar este equipo.'
                ]);
            } else {
                view('403.php');
            }
            exit;
        }

        private function exigirCreacionEquipo(): void
        {
            if (Auth::role() !== 'ADMIN') {
                http_response_code(403);
                view('403.php');
                exit;
            }
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
            $idsAutorizados = $this->equiposAutorizados();
            $filtros = [
                'buscar' => $_GET['buscar'] ?? null
            ];
            if ($idsAutorizados !== null) {
                $filtros['ids'] = $idsAutorizados;
            }

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
                'create' => Auth::role() === 'ADMIN' ? 'index.php?page=equipos&action=create' : null,
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

            $id = (int) $id;
            $this->exigirAccesoEquipo($id);
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

        /**
         * Formulario para editar los datos permanentes de un equipo.
         */
        public function editar($id)
        {
            $id = (int) $id;
            $this->exigirAccesoEquipo($id);
            $equipo = $this->equipoModel->getById($id);

            if (!$equipo) {
                http_response_code(404);
                view('404.php');
                return;
            }

            $error = $_SESSION['equipo_error'] ?? null;
            $old = $_SESSION['equipo_old'] ?? [];
            unset($_SESSION['equipo_error'], $_SESSION['equipo_old']);

            view('pages.equipos.edit.php', [
                'equipo' => $equipo,
                'error' => $error,
                'old' => $old
            ]);
        }

        /**
         * Guarda cambios de identidad y escudo de un equipo.
         */
        public function actualizar($id)
        {
            $id = (int) $id;
            $this->exigirAccesoEquipo($id);
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?page=equipos');
                exit;
            }

            $equipo = $this->equipoModel->getById($id);
            if (!$equipo) {
                http_response_code(404);
                view('404.php');
                return;
            }

            $data = [
                'nombre' => trim((string) ($_POST['nombre'] ?? '')),
                'siglas' => trim((string) ($_POST['siglas'] ?? '')),
                'observaciones' => trim((string) ($_POST['observaciones'] ?? '')),
                'escudo' => $equipo['escudo'] ?? null
            ];

            $_SESSION['equipo_old'] = $data;
            if ($data['nombre'] === '') {
                $_SESSION['equipo_error'] = 'El nombre del equipo es obligatorio.';
                header('Location: index.php?page=equipos&action=editar&id=' . $id);
                exit;
            }
            if (mb_strlen($data['nombre']) > 150 || mb_strlen($data['siglas']) > 30) {
                $_SESSION['equipo_error'] = 'Revisa la longitud del nombre (máximo 150) y las siglas (máximo 30).';
                header('Location: index.php?page=equipos&action=editar&id=' . $id);
                exit;
            }

            $rutaNueva = null;
            $nombreAnterior = $equipo['escudo'] ?? null;
            $archivo = $_FILES['escudo'] ?? null;

            if ($archivo && $archivo['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024) {
                    $_SESSION['equipo_error'] = 'El escudo debe cargarse correctamente y no superar 2 MB.';
                    header('Location: index.php?page=equipos&action=editar&id=' . $id);
                    exit;
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($archivo['tmp_name']);
                $formatos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!isset($formatos[$mime]) || @getimagesize($archivo['tmp_name']) === false) {
                    $_SESSION['equipo_error'] = 'El escudo debe ser una imagen JPG, PNG o WEBP válida.';
                    header('Location: index.php?page=equipos&action=editar&id=' . $id);
                    exit;
                }

                $directorio = ROOT_PATH . '/public/assets/img/equipos/';
                if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                    $_SESSION['equipo_error'] = 'No está disponible la carpeta de escudos.';
                    header('Location: index.php?page=equipos&action=editar&id=' . $id);
                    exit;
                }

                $data['escudo'] = bin2hex(random_bytes(16)) . '.' . $formatos[$mime];
                $rutaNueva = $directorio . $data['escudo'];
                if (!move_uploaded_file($archivo['tmp_name'], $rutaNueva)) {
                    $_SESSION['equipo_error'] = 'No fue posible guardar el nuevo escudo.';
                    header('Location: index.php?page=equipos&action=editar&id=' . $id);
                    exit;
                }
            }

            try {
                if (!$this->equipoModel->actualizarEntidad($id, $data)) {
                    throw new RuntimeException('La actualización del equipo no se completó.');
                }
                unset($_SESSION['equipo_old']);
                if ($rutaNueva && $nombreAnterior && $nombreAnterior !== 'default.png') {
                    $rutaAnterior = ROOT_PATH . '/public/assets/img/equipos/' . $nombreAnterior;
                    if (is_file($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $_SESSION['success'] = 'Los datos del equipo se actualizaron correctamente.';
                header('Location: index.php?page=equipos&action=detalles&id=' . $id);
                exit;
            } catch (Throwable $e) {
                if ($rutaNueva && is_file($rutaNueva)) {
                    @unlink($rutaNueva);
                }
                error_log('Error al actualizar equipo ' . $id . ': ' . $e->getMessage());
                $_SESSION['equipo_error'] = 'No fue posible actualizar el equipo. Intenta nuevamente.';
                header('Location: index.php?page=equipos&action=editar&id=' . $id);
                exit;
            }
        }

        public function dashboard(){
            $ids = $this->equiposAutorizados();
            $equipoId = $ids === null ? 10 : (int) ($ids[0] ?? 0);
            if ($equipoId <= 0) {
                http_response_code(403);
                view('403.php');
                return;
            }
            $this->exigirAccesoEquipo($equipoId);
            $equipo = $this->equipoModel->getById($equipoId);
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
            $this->exigirAccesoEquipo($equipoId, true);
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
                $equipoId = (int) ($_POST['equipo_id'] ?? 0);
                if ($equipoId > 0) {
                    $this->exigirAccesoEquipo($equipoId, true);
                }

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
            $this->exigirCreacionEquipo();
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
            $this->exigirCreacionEquipo();
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