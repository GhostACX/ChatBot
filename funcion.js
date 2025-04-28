document.addEventListener("DOMContentLoaded", function () {
    const chatInput = document.getElementById("chat-input");
    const sendButton = document.querySelector(".send-button");
    const chatContainer = document.getElementById("chat-container");

    sendButton.addEventListener("click", sendMessage);
    chatInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") sendMessage();
    });

    function sendMessage() {
        const userMessage = chatInput.value.trim();
        if (userMessage === "") return;

        appendMessage(userMessage, "user");
        chatInput.value = "";

        getBotResponse(userMessage);
    }

    function appendMessage(message, sender) {
        const messageDiv = document.createElement("div");
        messageDiv.classList.add(sender);

        if (sender === "bot") {
            // Verificar si la respuesta tiene formato de áreas y docentes (con |)
            if (message.includes("|")) {
                const sections = message.split("|");
                const ul = document.createElement("ul");

                for (let i = 0; i < sections.length; i += 2) {
                    const area = sections[i]?.trim();
                    const docentes = sections[i + 1]?.split(";").map(item => item.trim()).filter(item => item !== "");

                    if (area && docentes) {
                        // Crear elemento para el área con título
                        const liArea = document.createElement("li");
                        liArea.innerHTML = `<strong>${area}</strong>`;
                        ul.appendChild(liArea);

                        // Crear sublista numerada para los docentes
                        const ulDocentes = document.createElement("ul");
                        docentes.forEach((docente, index) => {
                            const liDocente = document.createElement("li");
                            liDocente.textContent = `${index + 1}. ${docente}`;
                            ulDocentes.appendChild(liDocente);
                        });
                        liArea.appendChild(ulDocentes);
                    }
                }
                messageDiv.appendChild(ul);
            } else {
                // Procesar respuestas simples con ; o \n
                let items = message.includes(";") ? message.split(";") : message.split("\n");
                items = items.map(item => item.trim()).filter(item => item !== "");

                if (items.length > 1) {
                    // Crear una lista numerada con título
                    const title = document.createElement("strong");
                    title.textContent = "Respuesta";
                    messageDiv.appendChild(title);

                    const ul = document.createElement("ul");
                    items.forEach((item, index) => {
                        const li = document.createElement("li");
                        li.textContent = `${index + 1}. ${item}`;
                        ul.appendChild(li);
                    });
                    messageDiv.appendChild(ul);
                } else {
                    // Respuesta simple sin formato de lista
                    messageDiv.textContent = message;
                }
            }
        } else {
            // Mensajes del usuario se muestran tal cual
            messageDiv.textContent = message;
        }

        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function getBotResponse(userMessage) {
        const formData = new FormData();
        formData.append("message", userMessage);

        fetch("chatbot_response.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.reply) {
                appendMessage(data.reply, "bot");
            } else {
                appendMessage("Lo siento, no entiendo eso.", "bot");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            appendMessage("Hubo un error al procesar tu mensaje.", "bot");
        });
    }
});