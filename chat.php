<?php
session_start();

// Función para normalizar texto: elimina mayúsculas, acentos y signos de puntuación
function normalizar(string $texto): string {
    // Pasar a minúsculas
    $texto = mb_strtolower($texto, 'UTF-8');
    // Transliterar y eliminar acentos
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    // Eliminar caracteres no alfanuméricos (mantener espacios)
    $texto = preg_replace('/[^a-z0-9\s]/', ' ', mb_strtolower($texto, 'UTF-8'));
    // Unificar espacios
    $texto = preg_replace('/\s+/', ' ', $texto);
    return trim($texto);
}

// Configuración de la base de datos
$host = 'localhost';
$base_datos = 'chatbot';
$usuario = 'root';
$contrasena = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

if (!isset($_SESSION['chat'])) {
    $_SESSION['chat'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto_original = trim($_POST['pregunta'] ?? '');
    if ($texto_original !== '') {
        // Normalizar entrada del usuario
        $pregunta = normalizar($texto_original);

        // Cargar respuestas de la base
        $stmt = $pdo->query("SELECT * FROM respuestas");
        $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $respuesta_encontrada = '';

        // Primero coincidencia de dos palabras clave
        foreach ($respuestas as $fila) {
            if (!empty($fila['palabra_clave2'])) {
                $k1 = normalizar($fila['palabra_clave1']);
                $k2 = normalizar($fila['palabra_clave2']);
                if (strpos($pregunta, $k1) !== false && strpos($pregunta, $k2) !== false) {
                    $respuesta_encontrada = $fila['respuesta'];
                    break;
                }
            }
        }

        // Si no hay, buscar con una sola palabra clave
        if ($respuesta_encontrada === '') {
            foreach ($respuestas as $fila) {
                if (empty($fila['palabra_clave2'])) {
                    $k1 = normalizar($fila['palabra_clave1']);
                    if (strpos($pregunta, $k1) !== false) {
                        $respuesta_encontrada = $fila['respuesta'];
                        break;
                    }
                }
            }
        }

        // Respuesta por defecto
        if ($respuesta_encontrada === '') {
            $respuesta_encontrada = 'Lo siento, no tengo una respuesta para eso.';
        }

        // Guardar en historial de chat
        $_SESSION['chat'][] = [
            'usuario'   => $texto_original,
            'bot'       => $respuesta_encontrada
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot de Palabras Clave</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .contenedor { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        .entrada-pregunta { width: 100%; padding: 10px; margin-bottom: 10px; }
        .boton-enviar { padding: 10px 20px; border: none; border-radius: 4px; background: #007bff; color: white; cursor: pointer; }
        .chat-item { margin-bottom: 15px; }
        .usuario { font-weight: bold; color: #333; }
        .respuesta { margin-top: 5px; padding: 10px; background: #e9ecef; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Chatbot de Palabras Clave</h1>
        <!-- Historial de conversación -->
        <?php foreach ($_SESSION['chat'] as $item): ?>
            <div class="chat-item">
                <div class="usuario">Tú: <?php echo htmlspecialchars($item['usuario']); ?></div>
                <div class="respuesta">Bot: <?php echo htmlspecialchars($item['bot']); ?></div>
            </div>
        <?php endforeach; ?>

        <!-- Formulario de nueva pregunta -->
        <form method="POST">
            <input 
                type="text" 
                name="pregunta" 
                class="entrada-pregunta" 
                placeholder="Escribe tu pregunta aquí..." 
                required>
            <button type="submit" class="boton-enviar">Enviar</button>
        </form>
    </div>
</body>
</html>
