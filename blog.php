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

// Obtener las publicaciones del blog de la base de datos
$sql = "SELECT * FROM blog ORDER BY fecha DESC";
$stmt = $pdo->query($sql);
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica si hay publicaciones
if (empty($blogs)) {
    echo "No hay publicaciones disponibles.";  // Muestra mensaje si no hay publicaciones
}

// Función para asignar imagen según el id_blog
function obtenerImagenBlog($id_blog) {
    // Asignar imágenes manualmente según el id_blog
    switch ($id_blog) {
        case 1:
            return 'img/image-removebg-preview.png'; // Imagen para el primer blog
        case 2:
            return 'img/image-removebg-preview.png'; // Imagen para el segundo blog
        case 3:
            return 'img/image-removebg-preview.png'; // Imagen para el tercer blog
        // Agregar más casos si tienes más blogs con diferentes imágenes
        default:
            return 'img/image-removebg-preview.png'; // Imagen predeterminada
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
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

    .post-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 20px;
        margin-top: 30px;
    }

    .post {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        width: 45%;
        text-align: left;
        transition: transform 0.3s;
    }

    .post:hover {
        transform: translateY(-5px);
    }

    .post img {
        max-width: 100%;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .post h3 {
        font-size: 18px;
        color: #333;
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

        footer {
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
                <li><a href="logout.php" class="cerrar-sesion-btn">Cerrar sesión</a></li>
            </ul>
        </nav>

        <main class="contenido">
            <h1>Blog</h1>

            <div class="post-container">
                <?php if (!empty($blogs)) : ?>
                    <?php foreach ($blogs as $blog): ?>
                        <div class="post">
                            <!-- Asignar la imagen dependiendo del id_blog -->
                            <img src="<?php echo obtenerImagenBlog($blog['id_blog']); ?>" alt="Imagen del blog" style="width: 100%; height: auto;">
                            <h3>Publicado el: <?php echo date('d/m/Y', strtotime($blog['fecha'])); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($blog['contenido'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay publicaciones disponibles.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

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