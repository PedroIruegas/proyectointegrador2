<?php
// Iniciar la sesión antes de cualquier acción
session_start();

// Definir las variables de conexión a la base de datos
$host = "localhost";           
$dbname = "proyin1";          
$username = "root";            
$password = "";              

// Intentar la conexión
try {
    // Crear la conexión PDO
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

// Obtener las citas agendadas
$sql = "SELECT citas.id_cita, servicios.nombre AS servicio, servicios.precio 
        FROM citas 
        JOIN servicios ON citas.id_servicio = servicios.id_servicio
        WHERE citas.estado = 'pendiente'";
$stmt = $pdo->query($sql);
$citas_agendadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular el total de las citas agendadas
$total_precio = 0;
foreach ($citas_agendadas as $cita) {
    $total_precio += $cita['precio'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios del Dentista</title>
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
        overflow: hidden; 
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

    table {
        width: 80%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: rgb(77, 139, 255);
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
        
        .contenedor {
            flex-direction: column;
        }

        .barra-lateral {
            width: 250px;
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
        }

        table {
            width: 100%;
        }

        footer {
            margin-top: 20px;
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

    .admin-header {
        background-color: #333; 
        color: #fff; 
        padding: 15px;
        position: sticky;
        top: 0;
        width: 100%;
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
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <h1>Administrador</h1>
        </div>
    </header>
    <div class="contenedor">
    <!-- Botón de menú -->
    <div class="menu-btn" id="menu-btn">&#9776;</div>
    <!-- Barra de navegación lateral -->
    <nav class="barra-lateral" id="barra-lateral">
            <ul>
                <li><a href="serviciosdentista.php">🦷 Registro de servicios</a></li>
                <li><a href="agendadentista.php">📅 Registro de citas</a></li>
                <li><a href="gananciasdentista.php">$ Registro de ganancias</a></li>
                <li><a href="logout.php" class="cerrar-sesion-btn">Cerrar sesión</a></li>
            </ul>
        </nav>

        <main class="contenido">
            <h1>Registro de servicios</h1>

            <!-- Mostrar las citas agendadas -->
            <?php if (count($citas_agendadas) > 0): ?>
                <table>
                    <tr>
                        <th>ID Cita</th>
                        <th>Servicio</th>
                        <th>Precio</th>
                    </tr>
                    <?php foreach ($citas_agendadas as $cita): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cita['id_cita']); ?></td>
                            <td><?php echo htmlspecialchars($cita['servicio']); ?></td>
                            <td>$<?php echo htmlspecialchars($cita['precio']); ?> pesos mexicanos.</td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <h3>Total de las citas agendadas: $<?php echo number_format($total_precio, 2);?> pesos mexicanos.</h3>
            <?php else: ?>
                <p>No hay citas pendientes.</p>
            <?php endif; ?>
        </main>
    </div>

    <footer>
    <div class="version">Versión 1.0</div>
    <div class="emails">
        <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
    </div>
    </footer>

    <script>
    // Toggle la clase "open" para la barra lateral al hacer clic en el botón de menú
    $('#menu-btn').click(function() {
    $('#barra-lateral').toggleClass('open');
    });
    </script>
</body>
</html>