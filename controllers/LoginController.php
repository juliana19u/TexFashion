<?php

require 'models/Login.php';

/**
 * Clase Controlador Login
 */
class LoginController
{
    //atributo para que me conecte al modelo
    private $model;

    //llamado del constructor
    public function __construct()
    {
        //instancia
        $this->model = new Login;
    }

    public function index()
    {
        //variable reservada session verifica si hay un usuario en sesion y lo  redirige al home
        if (isset($_SESSION['user'])) {
            //si existe una sesion se va al home
            header('Location: ?controller=home');
            exit();
        } else {
            //si no se va al formulario
            require 'views/login.php';
        }
    }

    //funcion login que  nos permite hacer el inicio de sesion
    public function login()
    {
        //validateUser metodo creado en el modelo del modelo llegan aqui
        $validateUser = $this->model->validateUser($_POST);
        //si el validateuser es verdadera se dirije al home
        if (is_object($validateUser)) {
            $_SESSION['user'] = $_POST['email'];
            $_SESSION['user_role'] = $validateUser->rol;
            header('Location: ?controller=home');
            exit();
        } else { //si ed falso se devuelve un error
            $error = [
                'errorMessage' => $validateUser,
                //el email que este escrito en post con el fin de poder ponerlo en la vista
                'email' => $_POST['email']
            ];
            //se requiere la vista del login en caso de que validateuser es false
            require 'views/login.php';
        }
    }

    //metodo  para cerrar sesion
    public function logout()
    {
        //si existe una sesion destruye la sesion con el metodo destroy
        if ($_SESSION['user']) {
            session_destroy();
            //me  redirige a la vista iniciar sesion
            header('Location: ?controller=login');
        } else {  //en caso de  sea falso me redirige al inicio sesion
            header('Location: ?controller=login');
        }
    }
}
