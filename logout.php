<?php
// Load authentication controller
require_once __DIR__ . '/controllers/auth.php';

$controller = new AuthController();
$result = $controller->logout();

header('Location: views/login.php');
exit;
