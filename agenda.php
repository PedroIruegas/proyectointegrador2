<?php 
// Iniciar la sesión antes de cualquier acción
session_start();

// Definir las variables de conexión a la base de datos
$host = "localhost";           
$dbname = "proyin1";          
$username = "root";            
$password = "";              

// Duración de los servicios en minutos 
$duraciones_servicios = [
    45, // Limpieza Dental
    30, // Revisión / Consulta Inicial
    60, // Blanqueamiento Dental
    60, // Obturación (empaste)
    45, // Extracción Dental Simple
    90, // Extracción de Muela del Juicio
    90, // Colocación de Carillas
    90, // Tratamiento de Conducto
    90, // Colocación de Corona Dental
    120, // Colocación de Implante Dental
    60, // Ortodoncia
];

// Intentar la conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener los servicios de la base de datos
$sql = "SELECT * FROM servicios";
$stmt = $pdo->query($sql);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar una variable para el mensaje de éxito o error
$mensaje = "";
$mostrar_citas = false;  // Variable para mostrar las citas

// Guardar la cita cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cancelar cita (eliminar)
    if (isset($_POST['cancelar_cita'])) {
        $id_cita = $_POST['cancelar_cita'];
        $sql = "DELETE FROM citas WHERE id_cita = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cita]);
        $mensaje = "Cita cancelada correctamente.";
    }
    // Verificar y Agendar cita
    elseif (isset($_POST['agendar_cita'])) {
        $id_servicio = $_POST['servicio'];
        $detalles = $_POST['detalles'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $id_paciente = $_SESSION['usuario']['id'];

        // Verificar si la fecha seleccionada es fin de semana
        $dia_semana = date('w', strtotime($fecha));
        if ($dia_semana == 0 || $dia_semana == 6) {
            $mensaje = "No se pueden agendar citas los fines de semana.";
        } else {
            // Verificar que la cita esté a más de tres días de la fecha actual
            $fecha_actual = new DateTime();
            $fecha_seleccionada = new DateTime($fecha);
            $diferencia = $fecha_actual->diff($fecha_seleccionada);
            
            if ($diferencia->days < 3) {
                $mensaje = "La cita debe ser agendada al menos 3 días de anticipación.";
            } else {
                // Verificar si la hora seleccionada está ocupada
                $duracion_servicio = $duraciones_servicios[$id_servicio - 1];
                $hora_inicio = strtotime($hora); // Convertir la hora en timestamp
                // Calcular la hora de fin sumando la duración del servicio y los 30 minutos de descanso
                $hora_fin = date('H:i:s', $hora_inicio + ($duracion_servicio * 60) + (30 * 60)); // 30 minutos de descanso

                // Verificar que la cita esté dentro del horario laboral (por ejemplo, de 07:00 a 18:00)
                $hora_inicio_valida = strtotime('07:00');
                $hora_fin_valida = strtotime('18:00');
                if ($hora_inicio < $hora_inicio_valida || strtotime($hora_fin) > $hora_fin_valida) {
                    $mensaje = "Las citas deben programarse entre las 07:00 AM y las 06:00 PM.";
                } else {
                    // Verificar si el horario seleccionado y el horario de fin se solapan con otras citas
                    $sql = "SELECT * FROM citas WHERE fecha = ? AND (
                                (hora BETWEEN ? AND ?) OR
                                (hora_fin BETWEEN ? AND ?) OR
                                (hora <= ? AND hora_fin >= ?)
                            )";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$fecha, $hora, $hora_fin, $hora, $hora_fin, $hora, $hora_fin]);
                    $cita_solapada = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($cita_solapada) {
                        $mensaje = "El horario seleccionado no está disponible debido a que ya existe una cita en ese horario.";
                    } else {
                        // Insertar la cita en la base de datos incluyendo la hora de fin
                        $sql = "INSERT INTO citas (id_paciente, id_servicio, detalles, fecha, hora, hora_fin, estado) 
                                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$id_paciente, $id_servicio, $detalles, $fecha, $hora, $hora_fin]);
                        $mensaje = "Cita agendada correctamente.";
                    }
                }
            }
        }
    } 
    // Mostrar las citas
    elseif (isset($_POST['verificar_citas'])) {
        $mostrar_citas = true;
    }
}

// Obtener las citas agendadas por el usuario
$citas_usuario = [];
if ($mostrar_citas) {
    $sql = "SELECT citas.*, servicios.nombre AS servicio_nombre 
            FROM citas 
            JOIN servicios ON citas.id_servicio = servicios.id_servicio
            WHERE citas.id_paciente = ? ORDER BY citas.fecha DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['usuario']['id']]);
    $citas_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Citas</title>
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
        top: 57px;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        flex-grow: 1;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    form {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        margin-top: 30px;
    }

    label {
        font-size: 18px;
        margin-bottom: 10px;
        display: block;
        color: #333;
    }

    input, select, textarea {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 16px;
        box-sizing: border-box; 
    }

    textarea {
        resize: vertical;
    }

    button {
        background-color: rgb(77, 139, 255);
        color: white;
        font-size: 18px;
        padding: 14px 28px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    button:hover {
        background-color: #005A9C;
        transform: translateY(-2px);
    }

    button:active {
        transform: translateY(0);
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

    .mensaje {
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
        text-align: center;
    }

    .mensaje-exito {
        background-color: #2ecc71;
        color: white;
    }

    .mensaje-error {
        background-color: #e74c3c;
        color: white;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-contenido {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
        max-width: 320px;
        min-width: 260px;
        overflow: hidden;
        border-radius: 8px;
        box-sizing: border-box;
        text-align: center;
        position: relative;
    }

    .modal-contenido form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-confirmar,
    .btn-cancelar {
        width: 70%;
        padding: 8px 5px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        box-sizing: border-box;
        word-wrap: break-word;
    }

    .btn-confirmar {
        background-color: #e74c3c;
        color: white;
    }

    .btn-confirmar:hover {
        background-color: #c0392b;
    }

    .btn-cancelar {
        background-color: #bdc3c7;
        color: #333;
    }

    .btn-cancelar:hover {
        background-color: #95a5a6;
    }

    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        border-radius: 8px;
    }

    .modal-dialog {
        position: relative;
        margin: 10% auto;
        max-width: 90%; 
    }

    .modal-content h2 {
        margin-bottom: 15px;
        font-size: 24px;
        color: #333;
        font-weight: bold;
        line-height: 1.3;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modal-table th, .modal-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .modal-table th {
        background-color: rgb(77, 139, 255);
        color: white;
    }

    .modal-footer {
        text-align: center;
        margin-top: 20px;
    }

    .modal-footer button {
        background-color: rgb(77, 139, 255);
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }


    .modal-title {
        font-size: 1.25rem;
        margin: 0;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal.show {
        display: block;
    }


    .modal-footer {
        text-align: center;
        margin-top: 20px;
    }

    .modal-footer button {
        background-color: rgb(77, 139, 255);
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
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
            overflow-x: hidden;
            overflow-y: auto; 
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

        footer {
            top: 50px;
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

    .cancelar-btn {
        font-size: 1rem;
        background: none;
        border: none;
        color: red;
        cursor: pointer;
        padding: 5px 10px; 
        white-space: nowrap; 
        text-overflow: ellipsis; 
        overflow: hidden;
        max-width: 100%; 
        width: auto;
        display: inline-block; 
        text-align: center;
    }

    .cancelar-btn::before {
        content: "✖";
        font-size: 24px;
        margin-right: 5px;
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
            <li><a href="logout.php" class="cerrar-sesion-btn">Cerrar sesión</a></li>
        </ul>
    </nav>

    <div class="contenido">

        <h1>Agendar Cita</h1>

        <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $mensaje == 'Cita agendada correctamente.' ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?php echo $mensaje; ?>
        </div>
        <?php endif; ?>

        <!-- Formulario para Agendar Cita -->
        <form method="POST">
            <label for="servicio">Servicio:</label>
            <select name="servicio" id="servicio" required>
                <?php foreach ($servicios as $servicio): ?>
                <option value="<?php echo $servicio['id_servicio']; ?>">
                    <?php echo $servicio['nombre']; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <label for="hora">Hora:</label>
            <input type="time" name="hora" id="hora" required>

            <label for="detalles">Detalles (Opcional):</label>
            <textarea name="detalles" id="detalles" rows="4"></textarea>

            <button type="submit" name="agendar_cita">Agendar Cita</button>
        </form>

        <!-- Botón para Verificar Citas -->
        <form method="POST">
            <button type="submit" name="verificar_citas">Verificar Citas</button>
        </form>

        <!-- Modal para mostrar las citas -->
        <div id="modalCitas" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Citas Agendadas</h2>
                </div>
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Detalles</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($citas_usuario)): ?>
                            <?php foreach ($citas_usuario as $cita): ?>
                            <tr>
                                <td><?php echo $cita['servicio_nombre']; ?></td>
                                <td><?php echo $cita['fecha']; ?></td>
                                <td><?php echo $cita['hora']; ?></td>
                                <td><?php echo $cita['detalles']; ?></td>
                                <td>
                                    <!-- Botón que abre el modal -->
                                    <button type="button" class="cancelar-btn" onclick="abrirModal(<?php echo $cita['id_cita']; ?>)">
                                        Cancelar
                                    </button>
                                    <!-- Modal de confirmación -->
                                    <div id="modal-<?php echo $cita['id_cita']; ?>" class="modal">
                                        <div class="modal-contenido">
                                            <span class="cerrar" onclick="cerrarModal(<?php echo $cita['id_cita']; ?>)">&times;</span>
                                            <p>¿Estás seguro que deseas cancelar esta cita?</p>
                                            <form method="POST">
                                                <input type="hidden" name="cancelar_cita" value="<?php echo $cita['id_cita']; ?>">
                                                <button type="submit" class="btn-confirmar">Sí, cancelar</button>
                                                <button type="button" class="btn-cancelar" onclick="cerrarModal(<?php echo $cita['id_cita']; ?>)">No, volver</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No tienes citas agendadas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="modal-footer">
                    <button onclick="document.getElementById('modalCitas').style.display='none'">Cerrar</button>
                </div>
            </div>
        </div>

        <!-- Mostrar modal cuando se ha solicitado verificar citas -->
        <script>
            if (<?php echo $mostrar_citas ? 'true' : 'false'; ?>) {
                document.getElementById('modalCitas').style.display = 'block';
            }

            function abrirModal(id) {
                document.getElementById('modal-' + id).style.display = 'block';
            }

            function cerrarModal(id) {
                document.getElementById('modal-' + id).style.display = 'none';
            }

            // Cerrar el modal si el usuario hace clic fuera del contenido
            window.onclick = function(event) {
                const modales = document.querySelectorAll('.modal');
                modales.forEach(modal => {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                });
            };
        </script>
    </div>

    <!-- Footer -->
    <footer>
        <div class="version">Versión 1.0</div>
        <p>walter.arrambidecst@uanl.com | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
    </footer>

    <script>
        // Toggle la clase "open" para la barra lateral al hacer clic en el botón de menú
        $('#menu-btn').click(function() {
        $('#barra-lateral').toggleClass('open');
        });
    </script>
</body>
</html>