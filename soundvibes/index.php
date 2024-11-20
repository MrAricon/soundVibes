<?php
session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Procesar el inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = json_decode(file_get_contents('data/users.json'), true);
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit();
        }
    }

    $error = 'Usuario o contraseña incorrectos';
}
?>

<?php include('header.php');?>

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>

<body>
    <section class="view">
        <h1>Iniciar sesión</h1>

        <?php if (isset($error)) {
            echo "<p>$error</p>";
        } ?>

        <form method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Iniciar sesión</button>
        </form>
    </section>
</body>

<?php include('footer.php');?>

</html>