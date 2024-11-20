<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$users = json_decode(file_get_contents('data/users.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];

    if (empty($newUsername) || empty($newEmail) || empty($newPassword)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        foreach ($users as $user) {
            if ($user['username'] === $newUsername) {
                $error = 'El nombre de usuario ya está registrado';
                break;
            }
            if ($user['email'] === $newEmail) {
                $error = 'El correo electrónico ya está registrado';
                break;
            }
        }

        if (!isset($error)) {
            $newUser = [
                'id' => count($users) + 1,
                'username' => $newUsername,
                'email' => $newEmail,
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'role' => 'user'
            ];
            $users[] = $newUser;
            file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));

            header('Location: admin.php');
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $userId = $_POST['user_id'];
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newRole = $_POST['role'];

    foreach ($users as &$user) {
        if ($user['id'] == $userId) {
            $user['username'] = $newUsername;
            $user['email'] = $newEmail;
            $user['role'] = $newRole;
            break;
        }
    }

    file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));

    header('Location: admin.php');
    exit();
}

?>

<?php include('header.php');?>

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
</head>

<body>
    <section class="view">
        <h1>Panel de Administración</h1>

        <h2>Crear Usuario</h2>
        <form method="POST">
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit" name="create_user">Crear Usuario</button>
        </form>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <h2>Lista de Usuarios</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de usuario</th>
                    <th>Correo electrónico</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <form action="admin.php" method="GET">
                                <button type="submit" name="edit" value="<?= $user['id'] ?>">Editar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        // Si se solicita editar un usuario
        if (isset($_GET['edit'])):
            $userIdToEdit = $_GET['edit'];
            // Buscar el usuario a editar
            $userToEdit = null;
            foreach ($users as $user) {
                if ($user['id'] == $userIdToEdit) {
                    $userToEdit = $user;
                    break;
                }
            }
        ?>
            <h2>Editar Usuario</h2>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $userToEdit['id'] ?>">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" value="<?= $userToEdit['username'] ?>" required><br>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?= $userToEdit['email'] ?>" required><br>

                <label for="role">Rol:</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $userToEdit['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="user" <?= $userToEdit['role'] === 'user' ? 'selected' : '' ?>>Usuario Normal</option>
                </select><br>

                <button type="submit" name="edit_user">Guardar cambios</button>
            </form>
            <br>
            <br>
            <br>
    </section>
<?php endif; ?>

<?php include('footer.php');?>

</body>

</html>