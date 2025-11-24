<?php
// public/logout.php
require_once __DIR__ . '/../app/auth.php';

logout_user(); // Borramos la sesión
header('Location: login.php'); // Redirigimos al login
exit;