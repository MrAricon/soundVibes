<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><span>Hola, <?= $_SESSION['username']; ?>!</span></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php">Panel de Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="index.php">Iniciar sesión</a></li>
                <li><a href="register.php">Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
