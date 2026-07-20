<?php
require_once __DIR__.'../../core/Auth.php';
require_once __DIR__.'../../models/User.php';

class LoginController {

    public function login() {

        header('Content-Type: application/json; charset=utf-8');

        
        require_once __DIR__.'../../../config/database.php';
        Auth::start();

         // Leer JSON en lugar de $_POST
        $input = json_decode(file_get_contents('php://input'), true);
        //capturamos datos enviados por el formulario
        $inputUsuario = $input['usuario'] ?? '';
        $inputPassword = $input['password'] ?? '';

        //creamos el modelo User y buscamos cualquier usuario con ese nombre
        //sin importar el estado
        $userModel = new User($pdo);
     
        $user = $userModel->usuarioExiste($inputUsuario);
       
        //preparamos respuesta por default (JSON)
        $response = ['success'=>false,'mensaje'=>"Error desconocido"];

        //verificamos que el usuario y el password no vengan vacios
        if($inputUsuario==null && $inputPassword==null){
            $response=[
                'mensaje'=>"Los campos usuario y contraseña no pueden estar vacios",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }else //verificar si el usuario viene vacio
        if($inputUsuario==null){
            $response =[
                'mensaje'=>"El campo usuario no puede estar vacio",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }else //verificamos si el el password viene vacio
         if($inputPassword==null){
            $response=[
                'mensaje'=>"El campo password no puede estar vacio",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }

        //validacion de existencia de usuario
        if(!$user){
            $response = [
                'mensaje'=>"El usuario no existe",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }

        //verificar contraseña
        if(!password_verify($inputPassword,$user['password'])){
            $response = [
                'mensaje'=>"La contraseña es incorrecta",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }

        //verificar el estado del usuario
        if ((int)$user['estado'] !== 1) {
            $response = [
                'mensaje'=>"Usuario inhabilitado. Contactar al administrador",
                'success'=>false
            ];
            echo json_encode($response);
            exit;
        }

        //loggin exitoso
        Auth::login($user); //se guarda la sesion
        $response['success']=true;
        $response['mensaje']='Inicio de sesión exitoso';
        $response['redirect']='/afec/admin/index.php';

        echo json_encode($response);
        exit;
    }

    public function logout() {
        session_start();
        $_SESSION['info'] ='Has cerrado sesion correctamente';

        Auth::start();
        Auth::logout();
        
        header("Location: /afec/public/index.php?page=login");
        exit;
    }
}

?>