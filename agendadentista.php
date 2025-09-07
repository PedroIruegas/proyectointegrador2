<?php 
session_start();

// Conexión a la base de datos
$host = "localhost";           
$dbname = "proyin1";          
$username = "root";            
$password = "";              

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'dentista') {
    header("Location: login.php");
    exit();
}

$mensaje = "";

$sql = "SELECT citas.*, usuarios.nombre AS paciente_nombre, usuarios.apellido_paterno AS paciente_apellido, 
        servicios.nombre AS servicio_nombre
        FROM citas 
        JOIN usuarios ON citas.id_paciente = usuarios.id_usuario
        JOIN servicios ON citas.id_servicio = servicios.id_servicio
        ORDER BY citas.fecha DESC";
$stmt = $pdo->query($sql);
$citas_dentista = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejo de formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $id_cita = $_POST['id_cita'];
        $nuevo_estado = $_POST['estado'];
        $stmt = $pdo->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
        $stmt->execute([$nuevo_estado, $id_cita]);
        $mensaje = "Estado de la cita actualizado correctamente.";
    }

    if (isset($_POST['eliminar_cita'])) {
        $id_cita = $_POST['id_cita'];
        // Ejecutar eliminación
        $sql = "DELETE FROM citas WHERE id_cita = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cita]);
        $mensaje = "Cita eliminada correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas - Dentista</title>
    <link rel="shortcut icon" href="img/muela.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            overflow: hidden; 
        }

        .admin-header {
            background-color: #333; 
            color: #fff; 
            padding: 15px;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 10;
        }

        .admin-header .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 24px;
            text-align: center;
        }

        .menu-btn {
            display: none;
        }

        .contenedor {
            display: flex;
            width: 100%;
            flex-grow: 1;
        }

        .barra-lateral {
            width: 250px;
            background: rgb(77, 139, 255);
            padding: 20px;
            position: fixed;
            height: 100vh;
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
        }

        .barra-lateral ul li a {
            text-decoration: none;
            color: white;
            font-size: 20px;
            display: block;
            padding: 14px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .barra-lateral ul li a:hover {
            background-color: #005A9C;
        }

        .contenido {
            margin-left: 300px;
            padding: 40px;
            flex-grow: 1;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px; 
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: rgb(77, 139, 255);
            color: white;
        }

        button {
            background-color: rgb(77, 139, 255);
            color: white;
            font-size: 16px;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #005A9C;
        }

        .boton-eliminar {
            background-color: #e74c3c;
        }

        .boton-eliminar:hover {
            background-color: #c0392b;
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

        .cerrar-sesion-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 30px;
        }

        .cerrar-sesion-btn:hover {
            background-color: #c0392b;
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

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-contenido {
        background-color: white;
        padding: 20px;
        margin: 15% auto;
        width: 300px;
        text-align: center;
        border-radius: 5px;
    }

    .modal-contenido button {
        margin: 10px;
        padding: 8px 16px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
    }

    .modal-contenido .btn-cancelar {
        background-color: #e74c3c;
        color: white;
    }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <h1>Administrador</h1>
        </div>
    </header>

    <div class="menu-btn" id="menu-btn">&#9776;</div>

    <div class="contenedor">
        <nav class="barra-lateral" id="barra-lateral">
            <ul>
                <li><a href="serviciosdentista.php">🦷 Registro de servicios</a></li>
                <li><a href="agendadentista.php">📅 Registro de citas</a></li>
                <li><a href="gananciasdentista.php">$ Registro de ganancias</a></li>
                <li><a href="logout.php" class="cerrar-sesion-btn">Cerrar sesión</a></li>
            </ul>
        </nav>

        <div class="contenido">
            <h1>Gestión de Citas</h1>

            <?php if ($mensaje): ?>
                <div class="mensaje <?php echo strpos($mensaje, 'correctamente') !== false ? 'mensaje-exito' : 'mensaje-error'; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas_dentista as $cita): ?>
                    <tr>
                        <td><?php echo $cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']; ?></td>
                        <td><?php echo $cita['servicio_nombre']; ?></td>
                        <td><?php echo $cita['fecha']; ?></td>
                        <td><?php echo $cita['hora']; ?></td>
                        <td><?php echo ucfirst($cita['estado']); ?></td>
                        <td>
                            <!-- Formulario para cambiar estado -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">
                                <select name="estado" required>
                                <option value="pendiente" <?php echo $cita['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="confirmada" <?php echo $cita['estado'] == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                <option value="cancelada" <?php echo $cita['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>  
                                </select>
                                <button type="submit" name="cambiar_estado">Cambiar Estado</button>
                            </form>
                            <!-- Formulario para eliminar -->
                            <form method="POST" style="display:inline;" id="form-eliminar-<?php echo $cita['id_cita']; ?>">
                                <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">
                                <input type="hidden" name="eliminar_cita" value="1">
                                <button type="button" class="boton-eliminar" onclick="abrirModal('<?php echo $cita['id_cita']; ?>')">Eliminar</button>
                            </form>
                            <!-- Modal de confirmación -->
                            <div id="modal-confirmacion-<?php echo $cita['id_cita']; ?>" class="modal">
                                <div class="modal-contenido">
                                    <p>¿Estás seguro de que deseas eliminar esta cita?</p>
                                    <button type="button" class="btn-confirmar" onclick="confirmarEliminacion('<?php echo $cita['id_cita']; ?>')">Sí, eliminar</button>
                                    <button class="btn-cancelar" onclick="cerrarModal('<?php echo $cita['id_cita']; ?>')">Cancelar</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="version">Versión 1.0</div>
        <div class="emails">
            <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | luis.padillat@uanl.edu.mx</p>
        </div>
    </footer>

    <script>
    function abrirModal(id) {
        const modal = document.getElementById('modal-confirmacion-' + id);
        if (modal) {
            modal.style.display = 'block'; // Muestra el modal
        } else {
            console.error('No se pudo encontrar el modal con ID:', id);
        }
    }

    function cerrarModal(id) {
        const modal = document.getElementById('modal-confirmacion-' + id);
        if (modal) {
            modal.style.display = 'none'; // Cierra el modal
        } else {
            console.error('No se pudo encontrar el modal con ID:', id);
        }
    }

    function confirmarEliminacion(id) {
        const form = document.getElementById('form-eliminar-' + id);
        if (form) {
            console.log('Formulario encontrado. Enviando...');
            form.submit(); // Envía el formulario para eliminar la cita
        } else {
            console.error('Formulario no encontrado para la cita con ID:', id);
            alert("Formulario no encontrado para la cita con ID: " + id);
        }
    }

    $(document).ready(function() {
        $('#menu-btn').click(function() {
            $('#barra-lateral').toggleClass('open');
        });
    });
    </script>
</body>
</html>