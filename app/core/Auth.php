<?php
// app/core/Auth.php
class Auth {

    public static function start(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function login($user){
        self::start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'usuario' => $user['usuario'],
            'rol' => $user['rol_id'],
            'estado'=> $user['estado'],
            'liga_id' => $user['liga_id'] ?? null,
            'equipo_id' => $user['equipo_id'] ?? null,
            'avatar' => $user['avatar'] ?? 'default.png'
        ];
    }

    public static function logout(){
        self::start();
        if(isset($_SESSION['user'])){
            unset($_SESSION['user']);
        }
    }

    public static function check(){
        self::start();
        return isset($_SESSION['user']);
    }

    public static function user(){
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(){
        self::start();
        if(!self::check()){
            header("Location: /afec/public/index.php?page=login");
            exit;
        }
    }

    public static function requireRole(array $roles){
        self::start();
        if(!self::check() || !in_array($_SESSION['user']['rol'], $roles)){
            header("Location: /afec/public/index.php?page=login&error=denied");
            exit;
        }
    }
}
?>
