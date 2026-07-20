<?php

require_once __DIR__.'../../models/User.php';
require_once __DIR__.'../../models/Rol.php';
require_once __DIR__.'../../models/Liga.php';
require_once __DIR__.'../../core/Auth.php';

class UsersController {
    private PDO $db;
    private $userModel;
    private $rolesModel;
    private $ligasModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    //LISTAR USUARIOS 
    public function index() {
        // Verificar que el usuario esté logueado y tenga permisos
        if (!Auth::check()) {
            header("Location: /afec/public/index.php?page=login");
            exit;
        }

        $estado = $_GET['filtroEstado'] ?? 'all';
        $rol = $_GET['filtroRol'] ?? null;
        $filtro = $_GET['filtro'] ?? '';

        $this->rolesModel = new Role($this->db);
        $this->ligasModel = new Liga($this->db);
        $roles = $this->rolesModel->all();
        $ligas = $this->ligasModel->all();

        $users = $this->userModel->filtrar([
            'estado' => $estado,
            'rol' => $rol,
            'filtro' => $filtro
        ]);

        // Pasar $users y $roles a la vista para renderizar la tabla
        // Vista que se cargará DENTRO del layout
        $viewPath = ROOT_PATH.'/admin/views/pages/users/users.php';

        // El layout tendrá acceso a $users, $roles y $viewPath
        include ROOT_PATH.'/admin/views/layout.php';
    }

    // FORM PARA CREAR USUARIO   
    public function create() 
    {
       
         // 🔐 Validar permisos (solo los admin)
        if (!in_array(Auth::user()['rol'], [1])) 
        {
            header("Location: index.php?page=dashboard");
            exit;
        }

        // Datos que se enviarán a la vista
        $data = [];

        //obtengo catalogo de roles para mostrar en el formulario
        $rolesModel = new Role($this->db);
        $roles = $rolesModel->all();
        $data['roles'] = $roles;

        // Si es POST, procesar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $nombre = trim($_POST['nombre'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';
            $rol_id = $_POST['rol_id'] ?? 0;
            $estado = $_POST['estado'] ?? 1;
            $liga_id = $_POST['liga_id'] ?? null;
            $equipo_id = $_POST['equipo_id'] ?? null;

            // Validar campos obligatorios
            $errors = [];
            if (!$usuario) $errors[] = "El usuario es obligatorio";
            if (!$password) $errors[] = "La contraseña es obligatoria";
            if (!$rol_id)  $errors[] = "El rol es obligatorio";

            //verificar que exista el id del rol
            $rol_valido = false;
            foreach($roles as $r){
                if($r['id'] == $rol_id){
                    $rol_valido = true;
                    break;
                }
            }

            if(!$rol_valido) $errors[] = "El rol seleccionado no es valido";

            //validaciones por rol
            switch($rol_id){
                case 1: //admin
                case 2: //supervisor
                    $liga_id=null;
                    $equipo_id=null;
                break;
                case 3: //usuario liga
                    if(empty($liga_id)){
                    $errors[] = 'Para crear usuario con rol de liga es obligatorio seleccionar una liga.';
                    return;
                    }
                    $equipo_id=null;
                break;
                case 4:
                    if(empty($liga_id) || empty($equipo_id)){
                        $errors[]='Para crear usuario con rol de equipo es obligatorio seleccionar una liga y un equipo.';
                        return;
                    }
                break;
                default:
                    $errors[] = "El rol no es valido";
                    return;   
            }
            //verificar que no se repita el nombre del usuario

            if($this->userModel->usuarioExiste($usuario)){
                $errors[] = "El nombre del usuario ya existe";
            }

            //si no hay errores crear el usuario
            if (empty($errors)) {
                // Hashear contraseña
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                try{
                    // Crear usuario
                    $result = $this->userModel->create([
                        'nombre'   => $nombre,
                        'usuario'  => $usuario,
                        'password' => $hashedPassword,
                        'rol_id'   => $rol_id,
                        'liga_id'  => $liga_id,
                        'equipo_id' => $equipo_id,
                        'estado'   => $estado
                    ]);

                    if ($result) {
                        // Redirigir a lista de usuarios
                        header("Location: /afec/admin/index.php?page=users");
                        exit;
                    } else {
                        $errors[] = "Error al crear el usuario en la base de datos";
                    }
                }catch(PDOException $e){
                    if ($e->getCode() == 2300){
                        $data['errors'][]= "El nombre de usuario ya existe.";
                    }else{
                        $data['errors'][] = "Error al gurdar el usuario";
                    }
                }
            }


            // Si hay errores, enviarlos a la vista
            $data['errors'] = $errors;
        }

        $viewPath = ROOT_PATH . '/admin/views/pages/users/create.php';
        include ROOT_PATH.'/admin/views/layout.php';

        // Retornar los datos al Router para que los pase a la vista
        return $data;
    }
    
   /* ===== GUARDAR USUARIO  ====== */

    public function save()
    {

        header('Content-Type: application/json; charset=utf-8');

        // Seguridad básica
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Método no permitido'
            ]);
            exit;
        }

       
        //validacion de permisos
        if (!in_array(Auth::user()['rol'], [1, 2])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No tienes permisos para esta acción'
            ]);
            exit;
        }

        // Datos del formulario
        $data = [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'usuario'  => trim($_POST['usuario'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'rol_id'   => $_POST['rol_id'] ?? null,
            'correo'   => trim($_POST['correo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'avatar'   => null,
            'liga_id'  => !empty($_POST['liga_id']) ? $_POST['liga_id'] : null,
            'equipo_id'=> !empty($_POST['equipo_id']) ? $_POST['equipo_id'] : null
        ];


        // Validaciones
        if (empty($data['nombre']) || empty($data['usuario']) || empty($data['password']) || empty($data['rol_id']))
        {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Todos los campos obligatorios deben completarse'
            ]);
            exit;
        }

        //validacion de roles
        switch ($_POST['rol_id']) {
            case 1: // Admin
            case 2: // Supervisor
                $data['liga_id'] = null;
                $data['equipo_id'] = null;
            break;
            case 3: // Usuario liga
                if (empty($data['liga_id'])) 
                {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'Debe seleccionar una liga'
                    ]);
                    exit;
                }
                $data['equipo_id'] = null;
            break;
            case 4: // Usuario equipo
                if (empty($data['liga_id']) || empty($data['equipo_id'])) 
                {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'Debe seleccionar liga y equipo'
                    ]);
                    exit;
                }
            break;
            default:
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Rol no válido'
                ]);
            exit;
        }

        
        // Hash de contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        /* ===============================
        AVATAR
        ===============================*/
        if (!empty($_FILES['avatar']['name'])) {

            $permitidos = ['image/jpeg', 'image/png'];
            $tipo = $_FILES['avatar']['type'];

            if (!in_array($tipo, $permitidos)) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Formato de imagen no permitido'
                ]);
                exit;
            }

            $nombreArchivo = uniqid('avatar_') . '.png';
            $ruta = __DIR__ . '/../../public/assets/img/avatars/' . $nombreArchivo;

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $ruta)) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Error al subir el avatar'
                ]);
                exit;
            }

            $data['avatar'] = $nombreArchivo;
        }

        // Guardar usuario
        $this->userModel->create($data);

        // RESPUESTA FINAL
        echo json_encode([
            'success' => true,
            'mensaje' => 'Usuario creado correctamente'
        ]);
        exit;
    }

    /* ============ EDITAR USUARIO ============ */
    public function edit($id) {
        Auth::requireRole([1,2]);
        $user = $this->userModel->findById($id);

        if(!$user){
            header("Location: index.php?page=users");
            exit;
        }

        $roles = (new Role($this->db))->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos del formulario
            $usuario = trim($_POST['usuario'] ?? '');
            $rol = $_POST['rol'] ?? '';
            $estado = $_POST['estado'];
            $password = $_POST['password'];

            // Validar si los campos son vacíos
            if (!$usuario ) $errors[]="Usuario obligatorio";

            //evitar duplicados excepto el mismo usuario
            if($this->userModel->usuarioExiste($usuario, $id)){
                $errors[] = "El nombre de usuario ya existe";
            }

            if (empty($errors)){
                $data = [
                    'usuario'=> $usuario,
                    'rol' => $rol,
                    'estado' => $estado
                ];
            }

            //resvisamos que el password no venga vacio y lo encriptamos para agregar a data
            if(!empty($password))
            {
                $hashedPassword = password_hash($password,PASSWORD_BCRYPT);
                $data['password'] = $hashedPassword;
            }

            // Actualizar usuario
            $result = $this->userModel->update($id, $data);

            if ($result) {
                header("Location: /afec/admin/index.php?page=users"); // Redirigir a lista de usuarios
            } else {
                echo "Error al actualizar el usuario";
            }
        }

        // Mostrar el formulario para editar usuario
        $viewPath = ROOT_PATH.'/admin/views/pages/users/edit.php';
        include ROOT_PATH. '/admin/views/layout.php';

    }

    /* =========== DESACTIVAR USUARIO ============ */
    public function disable($id) {
        $result = $this->userModel->disable($id);

        if ($result) {
            header("Location: /afec/admin/index.php?page=users");
        } else {
            echo "Error al desactivar el usuario";
        }
    }

    /* ========== CAMBIAR CONTRASEÑA ============== */
    public function changePassword($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';

            // Validar que la contraseña no esté vacía
            if (!$newPassword) {
                echo "La nueva contraseña es obligatoria";
                return;
            }

            // Hashear la nueva contraseña
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Actualizar contraseña
            $result = $this->userModel->changePassword($id, $hashedPassword);

            if ($result) {
                echo "Contraseña cambiada con éxito";
            } else {
                echo "Error al cambiar la contraseña";
            }
        }

        // Mostrar formulario para cambiar la contraseña
        include __DIR__ . '/../../admin/views/pages/users/change_password.php';
    }

    /* ============== funcion para boton toggle del estado de un usuario */
    public function toggleState() {
        Auth::requireRole([1]); // solo admin

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estado'] ?? null;

        if (!$id || !isset($estado)) {
            echo json_encode(['success' => false]);
            return;
        }

        $result = $this->userModel->update($id, ['estado' => $estado]);

        echo json_encode(['success' => $result ? true : false]);
    }

    //funcion para aplicar filtros
    public function filter()
    {
        Auth::requireRole([1,2]);

        $data = json_decode(file_get_contents("php://input"), true);

        $search = trim($data['search'] ?? '');
        $estado = $data['estado'] ?? 'all';
        $rol    = $data['rol'] ?? null;

        $users = $this->userModel->filter($search, $estado, $rol);

        $viewPath = ROOT_PATH . '/admin/views/pages/users/partials/table_rows.php';
        exit;
    }

    
}

?>