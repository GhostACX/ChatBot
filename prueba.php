<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot - ItfamIA</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="sidebar">
        <h2>ITFAM-AI</h2>
        <a href="chat_page.html" class="position" title="Ir al chat">
            <img src="IMG/chat.png" alt="Chat"> Chat
        </a>
        <a href="discord_page.html" title="Unirse a Discord">
            <img src="IMG/discord.png" alt="Discord"> Discord
        </a>
        <div class="divider">
            <a href="contact_us.html" title="Ir a la página de contacto">
                <img src="IMG/customer-service.png" alt="Memo"> Contáctanos
            </a>
        </div>
        <div class="user-info">
            <a href="pagina_redirigir.html">
                <img src="IMG/user.png" alt="User Avatar">
            </a>
            <div class="details">
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="welcome-message">
            <h1>¡Bienvenido al ChatBot!</h1>
        </div>
        <div id="chat-container" class="chat-container"></div>
        <div class="chatbar-container">
            <input id="chat-input" class="chat-input" placeholder="Escribe tu mensaje...">
            <button class="send-button">Enviar</button>
        </div>
    </div>

    <script src="01.js"></script>
</body>
</html> 