const tracks = [];
const trackListElement = document.getElementById('song-list'); // Este ya no se usará

// Cargar las canciones desde el archivo JSON
fetch('data/tracks.json')
    .then(response => response.json())
    .then(data => {
        data.tracks.forEach(track => {
            tracks.push(track);
        });

        // Play the first track automatically if tracks exist
        if (tracks.length > 0) {
            playTrack(tracks[0]); // Start playing the first track
        }
    });

let currentTrackIndex = -1;
let sound = null;

// Función para reproducir una canción
function playTrack(track) {
    if (sound) {
        sound.stop();
    }

    sound = new Howl({
        src: [track.src],
        html5: true,
        onplay: () => {
            updateTime();
        },
        onload: () => {
            document.getElementById('track-image').src = track.img;
            document.getElementById('track-title').textContent = track.title;
            document.getElementById('track-artist').textContent = track.artist;
        },
        onend: () => {
            if (currentTrackIndex < tracks.length - 1) {
                playTrack(tracks[currentTrackIndex + 1]);
            }
        }
    });

    sound.play();
    currentTrackIndex = tracks.indexOf(track);
    updateProgress();
}

function playTrackById(id) {
    if (sound) {
        sound.stop();
    }

    sound = new Howl({
        src: [tracks[id].src],
        html5: true,
        onplay: () => {
            updateTime();
        },
        onload: () => {
            document.getElementById('track-image').src = tracks[id].img;
            document.getElementById('track-title').textContent = tracks[id].title;
            document.getElementById('track-artist').textContent = tracks[id].artist;
        },
        onend: () => {
            if (currentTrackIndex < tracks.length - 1) {
                playTrack(tracks[currentTrackIndex + 1]);
            }
        }
    });

    sound.play();
    currentTrackIndex = tracks.indexOf(track);
    updateProgress();
}

// Controles del reproductor
document.getElementById('next').addEventListener('click', () => {
    if (currentTrackIndex < tracks.length - 1) {
        playTrack(tracks[currentTrackIndex + 1]);
    }
});

document.getElementById('prev').addEventListener('click', () => {
    if (currentTrackIndex > 0) {
        playTrack(tracks[currentTrackIndex - 1]);
    }
});

document.getElementById('play-pause').addEventListener('click', () => {
    if (sound && sound.playing()) {
        sound.pause();
    } else if (sound) {
        sound.play();
    }
});

// Actualizar el tiempo de la canción
function updateTime() {
    if (sound) {
        const currentTime = sound.seek();
        const duration = sound.duration();
        document.getElementById('current-time').textContent = formatTime(currentTime) + ' / ' + formatTime(duration);
        document.getElementById('track-progress').value = (currentTime / duration) * 100;
        requestAnimationFrame(updateTime);
    }
}

// Actualizar el progreso de la canción
function updateProgress() {
    if (sound) {
        const currentTime = sound.seek();
        const duration = sound.duration();
        document.getElementById('track-progress').value = (currentTime / duration) * 100;
    }
}

// Cambiar el tiempo de la canción al mover el slider
document.getElementById('track-progress').addEventListener('input', function() {
    const newTime = (this.value / 100) * sound.duration();
    sound.seek(newTime); // Actualizar el tiempo de reproducción
});

// Función para formatear el tiempo (minutos:segundos)
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}
