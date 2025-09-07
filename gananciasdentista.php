<?php 
session_start();

// Conexión a la base de datos con PDO
$host = "localhost";           
$dbname = "proyin1";          
$username = "root";            
$password = "";          

try {
    // Usamos PDO para la conexión
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Modo de error
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'dentista') {
    header("Location: login.php");
    exit();
}

$mensaje = "";

// Función para obtener el nombre del mes en español 
function obtenerNombreMes($mes_numero) {
    // Asegurarse de que $mes_numero sea un entero
    $mes_numero = (int)$mes_numero; // Convertir el mes a entero (esto elimina ceros a la izquierda)

    // Array con los nombres de los meses en español
    $meses = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    );

    // Verificar si el número del mes está dentro del rango válido (1-12)
    if ($mes_numero >= 1 && $mes_numero <= 12) {
        return $meses[$mes_numero]; // Devolver el nombre del mes
    } else {
        return "Mes inválido"; // En caso de que el número no sea válido
    }
}

// Función para obtener las ganancias de un mes
function obtenerGananciasMes($mes, $anio) {
    global $pdo;

    $query = "SELECT SUM(precio) AS ganancias FROM citas
              JOIN servicios ON citas.id_servicio = servicios.id_servicio
              WHERE MONTH(citas.fecha) = :mes AND YEAR(citas.fecha) = :anio";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['mes' => $mes, 'anio' => $anio]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    return $datos['ganancias'] ?: 0; // Devuelve 0 si no hay ganancias
}

// Variables para el mes y año seleccionados
$mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
$anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');

// Consulta para obtener las citas filtradas por mes y año
$sql = "SELECT citas.*, usuarios.nombre AS paciente_nombre, usuarios.apellido_paterno AS paciente_apellido, 
        servicios.nombre AS servicio_nombre, servicios.precio
        FROM citas 
        JOIN usuarios ON citas.id_paciente = usuarios.id_usuario
        JOIN servicios ON citas.id_servicio = servicios.id_servicio
        WHERE MONTH(citas.fecha) = :mes AND YEAR(citas.fecha) = :anio
        ORDER BY citas.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['mes' => $mes, 'anio' => $anio]);
$citas_dentista = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular las ganancias totales por mes
$sql_ganancias = "SELECT SUM(servicios.precio) AS total_ganancias
                  FROM citas 
                  JOIN servicios ON citas.id_servicio = servicios.id_servicio
                  WHERE MONTH(citas.fecha) = :mes AND YEAR(citas.fecha) = :anio";
$stmt_ganancias = $pdo->prepare($sql_ganancias);
$stmt_ganancias->execute(['mes' => $mes, 'anio' => $anio]);
$ganancias_mes = $stmt_ganancias->fetch(PDO::FETCH_ASSOC)['total_ganancias'];

$mensaje_comparacion = "";
$detalles_comparacion = "";

if (isset($_POST['comparar_mes'])) {
    // Obtener los meses seleccionados
    $mes_comparar_1 = $_POST['mes_comparar_1'];
    $mes_comparar_2 = $_POST['mes_comparar_2'];
    $anio_comparar = $_POST['anio_comparar'];

    // Obtener ganancias para los dos meses
    $ganancias_mes_1 = obtenerGananciasMes($mes_comparar_1, $anio_comparar);
    $ganancias_mes_2 = obtenerGananciasMes($mes_comparar_2, $anio_comparar);

    // Convertir los números de mes a nombres en español
    $mes_comparar_1_nombre = obtenerNombreMes($mes_comparar_1);
    $mes_comparar_2_nombre = obtenerNombreMes($mes_comparar_2);

    // Comparar ganancias
    if ($ganancias_mes_1 > $ganancias_mes_2) {
        $mensaje_comparacion = "Las ganancias de $mes_comparar_1_nombre fueron mayores que las de $mes_comparar_2_nombre.";
        $diferencia = number_format($ganancias_mes_1 - $ganancias_mes_2, 2);  // Formatear con 2 decimales
        $detalles_comparacion = "Mes 1 ($mes_comparar_1_nombre): $ganancias_mes_1 pesos mexicanos<br>Mes 2 ($mes_comparar_2_nombre): $ganancias_mes_2 pesos mexicanos<br>Diferencia: $diferencia pesos mexicanos";
    } elseif ($ganancias_mes_1 < $ganancias_mes_2) {
        $mensaje_comparacion = "Las ganancias de $mes_comparar_2_nombre fueron mayores que las de $mes_comparar_1_nombre.";
        $diferencia = number_format($ganancias_mes_2 - $ganancias_mes_1, 2);  // Formatear con 2 decimales
        $detalles_comparacion = "Mes 1 ($mes_comparar_1_nombre): $ganancias_mes_1 pesos mexicanos<br>Mes 2 ($mes_comparar_2_nombre): $ganancias_mes_2 pesos mexicanos<br>Diferencia: $diferencia pesos mexicanos";
    } else {
        $mensaje_comparacion = "Las ganancias de ambos meses fueron iguales.";
        $diferencia = "0.00";  // Aseguramos que la diferencia sea 0.00
        $detalles_comparacion = "Mes 1 ($mes_comparar_1_nombre): $ganancias_mes_1 pesos mexicanos<br>Mes 2 ($mes_comparar_2_nombre): $ganancias_mes_2 pesos mexicanos<br>Diferencia: $diferencia pesos mexicanos";
    }

    // Mostrar el modal con los resultados
    echo "<script>
            window.onload = function() {
                mostrarModal('$mensaje_comparacion', '$detalles_comparacion');
            }
          </script>";
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
        text-align: center;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    form {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        width: 100%;
        max-width: 500px;
    }

    form label {
        font-size: 18px;
        margin-bottom: 10px;
        display: block;
        color: #333;
    }

    form select, form button {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f7f7f7;
        transition: 0.3s ease;
    }

    form select:focus, form button:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.6);
    }

    form button {
        background-color: #3498db;
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
    }

    form button:hover {
        background-color: #2980b9;
    }

    .mensaje {
        padding: 15px;
        border-radius: 6px;
        margin-top: 20px;
        font-size: 16px;
        width: 100%;
        max-width: 500px;
        text-align: center;
        font-weight: bold;
    }

    .mensaje-exito {
        background-color: #2ecc71;
        color: white;
    }

    .mensaje-error {
        background-color: #e74c3c;
        color: white;
    }

    .mensaje-info {
        background-color: #f39c12;
        color: white;
    }

    h2 {
        font-size: 24px;
        margin-top: 40px;
        color: #333;
        font-weight: bold;
    }

    h3 {
        font-size: 22px;
        color: #333;
        margin-top: 20px;
        font-weight: normal;
    }

    .ganancias-box {
        background-color: #f5f5f5;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        width: 100%;
        max-width: 500px;
    }

    .ganancias-box h2 {
        color: #3498db;
        font-size: 28px;
        margin-bottom: 20px;
    }

    .ganancias-box p {
        font-size: 20px;
        color: #333;
        font-weight: bold;
    }

    .comparar-form {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
        width: 100%;
        max-width: 500px;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    table th, table td {
        padding: 12px;
        text-align: center;
        font-size: 16px;
    }

    table th {
        background-color: #3498db;
        color: white;
        font-weight: bold;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #eaeaea;
        cursor: pointer;
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
            overflow-x: hidden;
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

        .footer {
            overflow: hidden; 
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
            overflow: hidden; 
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

    .modal {
        position: fixed; 
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7); 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        z-index: 1000;
        animation: fadeIn 0.3s ease-in-out; 
    }

    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px; 
        width: 100%;
        max-width: 600px; 
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        text-align: center;
        position: relative;
    }

    .modal-content h2 {
        margin-bottom: 15px;
        font-size: 24px;
        color: #333;
        font-weight: bold;
        line-height: 1.3;
    }

    .modal-content p {
        font-size: 16px;
        margin-bottom: 25px;
        color: #555;
    }

    #close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: transparent;
        border: none;
        color: #aaa;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    #close-btn:hover {
        color: #333;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
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

        <div class="contenido">
            <h1>Gestión de Ganancias</h1>

            <?php if ($mensaje): ?>
                <div class="mensaje <?php echo strpos($mensaje, 'correctamente') !== false ? 'mensaje-exito' : 'mensaje-error'; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="mes">Selecciona un mes:</label>
                <select name="mes" id="mes" required>
                    <option value="01" <?php if ($mes == "01") echo "selected"; ?>>Enero</option>
                    <option value="02" <?php if ($mes == "02") echo "selected"; ?>>Febrero</option>
                    <option value="03" <?php if ($mes == "03") echo "selected"; ?>>Marzo</option>
                    <option value="04" <?php if ($mes == "04") echo "selected"; ?>>Abril</option>
                    <option value="05" <?php if ($mes == "05") echo "selected"; ?>>Mayo</option>
                    <option value="06" <?php if ($mes == "06") echo "selected"; ?>>Junio</option>
                    <option value="07" <?php if ($mes == "07") echo "selected"; ?>>Julio</option>
                    <option value="08" <?php if ($mes == "08") echo "selected"; ?>>Agosto</option>
                    <option value="09" <?php if ($mes == "09") echo "selected"; ?>>Septiembre</option>
                    <option value="10" <?php if ($mes == "10") echo "selected"; ?>>Octubre</option>
                    <option value="11" <?php if ($mes == "11") echo "selected"; ?>>Noviembre</option>
                    <option value="12" <?php if ($mes == "12") echo "selected"; ?>>Diciembre</option>
                </select>

                <select name="anio" id="anio" required>
                    <option value="<?php echo date('Y'); ?>" selected><?php echo date('Y'); ?></option>
                    <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                    <option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                </select>

                <button type="submit">Ver Ganancias</button>
            </form>

            <?php
            $meses = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
            ];
            $mes_nombre = $meses[$mes];  
            ?>

            <h2>Ganancias para <?php echo $mes_nombre . ' ' . $anio; ?>: $<?php echo number_format($ganancias_mes, 2); ?></h2>

            <h3>Comparar Ganancias de Meses</h3>
            <form method="POST">
                <label for="mes_comparar_1">Mes para comparar 1:</label>
                <select name="mes_comparar_1" id="mes_comparar_1" required>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>

                <label for="mes_comparar_2">Mes para comparar 2:</label>
                <select name="mes_comparar_2" id="mes_comparar_2" required>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>

                <label for="anio_comparar">Año para comparar:</label>
                <select name="anio_comparar" id="anio_comparar" required>
                    <option value="<?php echo date('Y'); ?>" selected><?php echo date('Y'); ?></option>
                    <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                    <option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                </select>

                <button type="submit" name="comparar_mes">Comparar</button>
            </form>

            <script>
            // Función para mostrar el modal con los datos
            function mostrarModal(mensaje, detalles) {
                // Crear el modal dinámicamente
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h2>${mensaje}</h2>
                        <p>${detalles}</p>
                    </div>
                `;
                document.body.appendChild(modal);

                // Mostrar el modal
                modal.style.display = 'flex';

                // Cerrar el modal cuando se hace clic en el botón de cierre
                modal.querySelector('.close-btn').addEventListener('click', () => {
                    modal.style.display = 'none';
                    document.body.removeChild(modal);
                });
            }
            </script>
        </div>
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