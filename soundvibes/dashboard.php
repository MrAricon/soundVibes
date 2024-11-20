<?php
session_start();

// Verificar si el usuario está autenticado y redirigir a login si no lo está
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Obtener el estado de ánimo desde la URL, si existe
$mood = isset($_GET['mood']) ? $_GET['mood'] : null;

// Verificar si el usuario tiene permisos de administrador
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Cargar las recomendaciones musicales desde el archivo JSON
$recommendations = json_decode(file_get_contents('data/recommendations.json'), true);
?>

<?php include('header.php')?>

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <section class="view">
        <h1>Bienvenido, <?php echo $_SESSION['username']; ?></h1>

        <!-- Mostrar el estado de ánimo desde la URL -->
        <h2>Estado de ánimo seleccionado:
            <?php
            if ($mood) {
                echo ucfirst($mood);
            } else {
                echo "No seleccionado";
            }
            ?>
        </h2>

        <!-- Enlaces para seleccionar estado de ánimo -->
        <p>Selecciona un estado de ánimo:</p>
        <a href="dashboard.php?mood=happy">Feliz</a> |
        <a href="dashboard.php?mood=sad">Triste</a> |
        <a href="dashboard.php?mood=energetic">Energético</a> |
        <a href="dashboard.php?mood=relaxed">Relajado</a> |
        <a href="dashboard.php?mood=inspired">Inspirado</a> |
        <a href="dashboard.php?mood=stressed">Estresado</a>

        <h2>Recomendaciones Musicales</h2>
        <div id="recommendations">
            <?php
            // Mostrar las recomendaciones según el estado de ánimo
            if ($mood && isset($recommendations[$mood])) {
                // Calculate the starting offset for the mood
                $moodIndex = array_search($mood, array_keys($recommendations)); // Get the position of the mood
                $startId = $moodIndex * 3; // Each mood gets 3 IDs
                
                // Wrap each recommendation in an <a> tag with the corresponding song ID
                $recommendationsLinks = array_map(function ($recommendation, $index) use ($startId) {
                    $id = $startId + $index; // Calculate the song ID
                    return '<a onclick="playTrackById(' . $id . ')">' . htmlspecialchars($recommendation) . '</a>';
                }, $recommendations[$mood], array_keys($recommendations[$mood]));
                
                // Output the recommendations with <br> as a separator
                echo implode('<br>', $recommendationsLinks);
            } else {
                echo "Elige un estado de ánimo para recibir recomendaciones.";
            }             
            ?>
        </div>
        <div class="jukebox-container">
            <section id="player">
                <h2>Canción Actual</h2>
                <div class="track-info">
                    <img id="track-image" alt="Imagen de la canción" width="200">
                    <div id="track-details">
                        <p id="track-title">Título</p>
                        <p id="track-artist">Artista</p>
                    </div>
                </div>
                <div class="controls">
                    <button id="prev">⏮️</button>
                    <button id="play-pause">⏯️</button>
                    <button id="next">⏭️</button>
                </div>
                <p id="current-time">00:00 / 00:00</p>
                <input type="range" id="track-progress" min="0" max="100" value="0">
            </section>
        </div>
    </section>

    <?php include('footer.php');?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>
