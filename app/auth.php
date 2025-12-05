<?php
// app/auth.php
session_start(); // Inicia la sesión para poder usar $_SESSION

//GUARDA AL USUARIO
function login_user($user_data) {
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['username'] = $user_data['username'];
    $_SESSION['role'] = $user_data['role'];
}

//CIERRA LA SESION
function logout_user() {
    session_unset();
    session_destroy();
}

//VERIFICA AL USUARIO LOGEADO
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

//EN PAGINAS COMO EL CART_ADD, LLAMA AL is_logged_in y si no esta loggeado, usa header para forzarlo.
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>