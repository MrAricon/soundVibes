<?php
session_start();

// Verificar si ya está logueado, si es así, redirigir al dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Procesar el registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        // Leer los usuarios existentes
        $users = json_decode(file_get_contents('data/users.json'), true);

        // Verificar si el nombre de usuario o el correo electrónico ya están registrados
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = 'El nombre de usuario ya está registrado';
                break;
            }
            if ($user['email'] === $email) {
                $error = 'El correo electrónico ya está registrado';
                break;
            }
        }

        // Si el nombre de usuario o correo electrónico no existe, crear el nuevo usuario
        if (!isset($error)) {
            // Crear un nuevo usuario
            $new_user = [
                'id' => count($users) + 1, // Asignar un ID único
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT), // Hash de la contraseña
                'role' => 'user' // El rol es siempre 'user'
            ];

            // Añadir el nuevo usuario al archivo JSON
            $users[] = $new_user;
            file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));

            // Redirigir al login después del registro exitoso
            header('Location: index.php');
            exit();
        }
    }
}
?>

<?php include('header.php');?>

<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
</head>

<body>
    <section class="view">
        <h1>Registro de Usuario</h1>

        <?php if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        } ?>

        <form method="POST">
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Registrar</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="index.php">Iniciar sesión</a></p>
    </section>
    <?php include('footer.php');?>

</body>

</html>