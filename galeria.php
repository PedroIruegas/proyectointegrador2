<?php
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
    // En caso de error de conexión, mostrar el mensaje
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

// Define un array con las imágenes y sus descripciones
$imagenes = [
    /*
    [
        'url' => 'img/galeria_exitos.png',  // Ruta de la imagen
        'descripcion' => ''  // Descripción de la imagen
    ],
    */
];

// Insertar las imágenes manualmente en la base de datos
foreach ($imagenes as $imagen) {
    $sql = "INSERT INTO galeria (url, descripcion) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$imagen['url'], $imagen['descripcion']]);
}

// Consulta las imágenes de la galería
$sql = "SELECT * FROM galeria";
$stmt = $pdo->query($sql);
$imagenesDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería</title>
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
        text-align: center;
    }

    .galeria-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
    }

    .imagen {
        width: 650px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        text-align: center;
        cursor: default; 
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .imagen:hover {
        transform: scale(1.05);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
    }

    .imagen img {
        width: 100%; 
        height: auto; 
        border-radius: 8px;
    }

    .imagen h3 {
        margin: 15px 0;
        font-size: 20px;
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

        .imagen {
            width: 100%; 
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

        .imagen {
            width: 100%; 
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

        <main class="contenido">
            <h1>Galería de Servicios</h1>

            <div class="galeria-container">
                <?php
                // Mostrar cada imagen de la galería
                foreach ($imagenesDB as $imagen) {
                    echo '<div class="imagen">';
                    echo '<img src="' . $imagen['url'] . '" alt="Imagen de servicio">';
                    echo '<h3>' . $imagen['descripcion'] . '</h3>';
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer>
        <div class="version">Versión 1.0</div>
        <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
    </footer>

    <script>
    // Toggle la clase "open" para la barra lateral al hacer clic en el botón de menú
        $('#menu-btn').click(function() {
            $('#barra-lateral').toggleClass('open');
        });
    </script>
</body>
</html>