<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios</title>
    <link rel="shortcut icon" href="img/muela.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex; 
        flex-direction: column; 
        min-height: 100vh;
        background: linear-gradient(to right, rgb(77, 139, 255), #FFFFFF);
        overflow-x: hidden;
        overflow-y: auto; 
    }

    .contenedor {
        display: flex;
        width: 100%;
        flex-grow: 1;
        overflow: hidden;
    }

    .barra-lateral {
        width: 250px;
        background: rgb(77, 139, 255);
        padding: 20px;
        position: fixed;
        height: 100vh;
        overflow: hidden;
        transition: width 0.3s;
    }

    .barra-lateral:hover {
        width: 270px;
    }

    .barra-lateral ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .barra-lateral ul li {
        margin: 20px 0;
        transition: 0.3s;
    }

    .barra-lateral ul li a {
        text-decoration: none;
        color: white;
        font-size: 20px;
        display: block;
        padding: 14px;
        transition: 0.3s;
        border-radius: 5px;
    }

    .barra-lateral ul li a:hover {
        background: #005A9C;
        border-radius: 5px;
    }

    .contenido {
        margin-left: 300px;
        padding: 40px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        height: auto;
        text-align: center;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeIn 0.8s forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .servicios-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
        max-width: 900px;
    }

    .servicio {
        width: 280px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .servicio:hover {
        transform: scale(1.05);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
    }

    .servicio img {
        width: 100%;
        border-radius: 8px;
    }

    .servicio h3 {
        margin: 15px 0;
        font-size: 20px;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 500px;
        width: 100%;
    }

    .modal-content h2 {
        margin-bottom: 10px;
    }

    .modal-content p {
        font-size: 16px;
        margin-bottom: 20px;
    }

    .close {
        background: #FF5733;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .close:hover {
        background: #C70039;
    }

    .reserva-container {
        margin-top: 40px;
        text-align: center;
    }

    .valoracion-gratis {
        font-size: 18px;
        font-weight: bold;
        color: #007ACC;
        margin-bottom: 5px;
    }

    .boton-reserva {
        background: #007ACC;
        color: white;
        font-size: 20px;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .boton-reserva:hover {
        background: #005A9C;
    }

    .cerrar-sesion-btn {
        background-color: #e74c3c; 
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 30px;
    }

    .cerrar-sesion-btn:hover {
        background-color: #c0392b; 
    }

    .barra-lateral ul li.cerrar-sesion {
        margin-top: auto;
    }

    footer {
        position: relative;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 10px 0;
        background-color: #333;
        color: white;
        text-align: center;
        font-size: 14px;
    }

    footer .version {
        font-weight: bold;
    }

    @media (max-width: 768px) {
        body, html {
            overflow: hidden; 
            height: 100%;
        }

        .barra-lateral {
            transform: translateX(-100%);
            position: absolute;
            z-index: 10;
        }

        .barra-lateral.open {
                transform: translateX(0);
        }

        .menu-btn {
            display: block;
            position: absolute;
            top: 70px;
            left: 20px;
            font-size: 30px;
            color: white;
            cursor: pointer;
            z-index: 20;
        }

        .contenido {
            margin-left: 0;
            padding: 20px;
            overflow-y: auto; 
            max-height: 100vh; 
        }

        table {
            font-size: 12px;
            width: 100%; 
        }

        th, td {
            padding: 6px;
        }

        select, button {
            font-size: 12px;
            padding: 4px 8px;
        }

        .contenido h1 {
            font-size: 20px;
        }

        .mensaje {
            font-size: 14px;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        body, html {
            overflow: hidden; 
            height: 100%;
        }

        .contenedor {
            flex-direction: column;
        }

        .barra-lateral {
            width: 200px;
        }

        .contenido {
            padding: 15px;
        }

        .cerrar-sesion-btn {
            font-size: 16px;
        }

        table {
            width: 100%;
        }

        footer {
            padding: 10px;
        }
    }

    .usuario-header {
        background-color: #333; 
        color: #fff; 
        padding: 15px;
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 1000;
    }

    .usuario-header .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .usuario-header h1 {
        margin: 0;
        font-size: 24px;
        text-align: center;
    }

    .contenedor-imagenes {
        position: sticky;
        top: 50px;
        z-index: 10; 
    }
    </style>
</head>
<body>
    <header class="usuario-header">
        <div class="header-content">
            <h1>Usuario</h1>
        </div>
    </header>
    <div class="contenedor">
        <!-- Botón de menú -->
        <div class="menu-btn" id="menu-btn">&#9776;</div>
        <!-- Barra de navegación lateral -->
        <nav class="barra-lateral" id="barra-lateral">
            <ul>
                <li><a href="indexpaciente.php">🏠 Dentista</a></li>
                <li><a href="servicios.php">🦷 Servicios</a></li>
                <li><a href="galeria.php">📷 Galería</a></li>
                <li><a href="agenda.php">📅 Agenda</a></li>
                <li><a href="testimonios.php">💬 Testimonios</a></li>
                <li><a href="blog.php">📰 Blog</a></li>
                <li class="cerrar-sesion"><a href="logout.php" class="cerrar-sesion-btn">Cerrar sesión</a></li>
            </ul>
        </nav>

        <!-- Contenido principal -->
        <main class="contenido">
            <h1>Nuestros Servicios</h1>
            <div class="servicios-container">
                <!-- Servicios -->
                <div class="servicio" onclick="mostrarInfo('Limpieza Dental (profilaxis)', 'Precio: $425.00 pesos mexicanos. Duración: 45 minutos. Elimina placa y sarro para mantener una sonrisa saludable.')">
                    <img src="img/limpieza.jpg" alt="Limpieza Dental">
                    <h3>Limpieza Dental (profilaxis)</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Revisión / Consulta Inicial', 'Precio: $340.00 pesos mexicanos. Duración: 30 minutos. Evaluación y diagnóstico para determinar el tratamiento necesario.')">
                    <img src="img/revision.jpg" alt="Revisión / Consulta Inicial">
                    <h3>Revisión / Consulta Inicial</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Blanqueamiento Dental', 'Precio: $1,700 pesos mexicanos. Duración: 60 minutos. Aclara el color de tus dientes para una sonrisa más brillante.')">
                    <img src="img/blanqueamiento.jpg" alt="Blanqueamiento Dental">
                    <h3>Blanqueamiento Dental</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Obturación (empaste)', 'Precio: $680 pesos mexicanos. Duración: 60 minutos. Reparación de dientes con empaste para restaurar su función.')">
                    <img src="img/empaste.jpg" alt="Obturación (empaste)">
                    <h3>Obturación (empaste)</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Extracción Dental Simple', 'Precio: $850 pesos mexicanos. Duración: 45 minutos. Extracción de un diente dañado o no recuperable.')">
                    <img src="img/extraccion.jpg" alt="Extracción Dental Simple">
                    <h3>Extracción Dental Simple</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Extracción de Muela del Juicio', 'Precio: $1,700 pesos mexicanos. Duración: 90 minutos. Extracción de muelas del juicio que causan molestias.')">
                    <img src="img/muelajuicio.jpg" alt="Extracción de Muela del Juicio">
                    <h3>Extracción de Muela del Juicio</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Colocación de Carillas', 'Precio: $2,550 pesos mexicanos. Duración: 90 minutos. Mejora estética dental con carillas en una sesión.')">
                    <img src="img/carillas.jpg" alt="Colocación de Carillas">
                    <h3>Colocación de Carillas</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Tratamiento de Conducto', 'Precio: $3,000 pesos mexicanos. Duración: 90 minutos. Tratamiento para dientes con infecciones o daños en el nervio.')">
                    <img src="img/conducto.png" alt="Tratamiento de Conducto">
                    <h3>Tratamiento de Conducto</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Colocación de Corona Dental', 'Precio: $5,100 pesos mexicanos. Duración: 90 minutos (requiere 2 citas). Colocación de una corona para restaurar un diente dañado.')">
                    <img src="img/corona.jpg" alt="Colocación de Corona Dental">
                    <h3>Colocación de Corona Dental</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Colocación de Implante Dental', 'Precio: $17,000 pesos mexicanos. Duración: 120 minutos. Sustitución de dientes perdidos con implantes de titanio.')">
                    <img src="img/implante.jpg" alt="Colocación de Implante Dental">
                    <h3>Colocación de Implante Dental</h3>
                </div>

                <div class="servicio" onclick="mostrarInfo('Ortodoncia (consulta o mantenimiento)', 'Precio: $680 pesos mexicanos. Duración: 60 minutos. Consulta o mantenimiento de ortodoncia para mejorar la alineación dental.')">
                    <img src="img/ortodoncia.jpg" alt="Ortodoncia">
                    <h3>Ortodoncia</h3>
                </div>
            </div>

            <!-- Botón de Reserva -->
            <div class="reserva-container">
                <p class="valoracion-gratis">⭐ Valoración Gratis</p>
                <button class="boton-reserva" onclick="window.location.href='agenda.php'">Reserva tu cita</button>
            </div>
        </main>
    </div>

    <!-- Modal (Ventana emergente para mostrar info del servicio) -->
    <div class="modal" id="modal-info">
        <div class="modal-content">
            <h2 id="servicio-titulo"></h2>
            <p id="servicio-descripcion"></p>
            <button class="close" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <footer>
        <div class="version">Versión 1.0</div>
        <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
    </footer>

    <script>
        function mostrarInfo(servicio, descripcion) {
            // Mostrar el modal con la información del servicio
            document.getElementById('servicio-titulo').textContent = servicio;
            document.getElementById('servicio-descripcion').textContent = descripcion;
            document.getElementById('modal-info').style.display = 'flex';
        }

        function cerrarModal() {
            // Cerrar el modal
            document.getElementById('modal-info').style.display = 'none';
        }

        // Toggle la clase "open" para la barra lateral al hacer clic en el botón de menú
        $('#menu-btn').click(function() {
            $('#barra-lateral').toggleClass('open');
        });
    </script>
</body>
</html>