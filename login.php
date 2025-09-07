<?php 
session_start();

// Conexión a la base de datos
$host = "localhost";
$dbname = "proyin1";
$username = "root";
$password = "";

try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

// Verificar si el usuario ya está logueado
if (isset($_SESSION['usuario'])) {
    // Redirigir a indexpaciente.php sin importar el tipo de usuario
    header("Location: indexpaciente.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe en la base de datos
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    if ($user && $user['contraseña'] == $password) {
        // Guardar la información del usuario en la sesión
        $_SESSION['usuario'] = [
            'id' => $user['id_usuario'], 
            'nombre' => $user['nombre'],
            'email' => $user['correo'],
            'tipo' => $user['tipo'] // Guardar tipo de usuario (paciente o dentista)
        ];

        // Redirigir a indexpaciente.php
        header("Location: indexpaciente.php");
        exit();
    } else {
        // Mostrar un mensaje de error si las credenciales no son correctas
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
    /* Estilos Generales */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Asegura que el body ocupe toda la altura de la ventana */
        background: linear-gradient(to right, rgb(77, 139, 255), #FFFFFF);
        box-sizing: border-box; /* Asegura que el padding no afecte el tamaño total */
    }

    .content {
        flex: 1; /* Hace que el contenido ocupe el espacio disponible entre el encabezado y el pie de página */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box; /* Asegura que el padding no afecte el tamaño */
    }

    .login-container {
        background-color: rgba(255, 255, 255, 0.8);
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
        width: 100%;
        max-width: 400px;
        position: relative;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    input {
        width: calc(100% - 24px);
        padding: 12px;
        margin: 10px 0;
        border-radius: 5px;
        border: 2px solid #ADD8E6;
        font-size: 16px;
        transition: border-color 0.3s ease;
        display: inline-block;
    }

    input:focus {
        border-color: #007BFF;
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 20px; 
    }

    button:hover {
        background-color: #45a049;
    }

    .error {
        color: red;
        text-align: center;
        margin-top: 10px;
    }

    .links {
        text-align: center;
        margin-top: 30px; 
    }

    .links a {
        color: #007BFF;
        text-decoration: none;
        font-size: 14px;
        display: inline-block; 
        margin: 10px 0; 
    }

    .links a:hover {
        text-decoration: underline;
    }

    .links a:first-child {
        margin-right: 15px;
    }

    .links a {
        font-weight: bold;
        font-size: 14px;
        background-color: #007BFF;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .links a:hover {
        background-color: #0056b3;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .password-container {
        position: relative;
        width: 100%;
    }

    .password-container input {
        width: calc(100% - 40px);
        padding-right: 30px; 
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 18px;
    }

    footer {
        background-color: #333;
        color: white;
        text-align: center;
        font-size: 14px;
        padding: 15px 0;
        width: 100%;
        margin-top: auto; /* Asegura que el pie de página se quede en la parte inferior */
        box-sizing: border-box; /* Asegura que el padding no afecte el tamaño */
    }

    footer .version {
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
    }

    footer .emails {
        margin-top: 5px;
    }

    footer .emails span {
        display: block;
        margin: 3px 0;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        body {
            padding: 0; /* Elimina el padding global */
        }

        .login-container {
            padding: 20px;
            width: 100%;
            max-width: 300px;
        }

        .links a {
            font-size: 12px;
            padding: 6px 12px;
        }

        footer {
            min-height: 80px; /* Asegura que el pie de página tenga suficiente altura en pantallas pequeñas */
            padding: 15px 0;
            margin-top: auto;
        }
    }

    </style>
</head>
<body>

    <div class="content">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                    <span id="toggle-password" class="toggle-password">👁️</span>
                </div>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
            <div class="links">
                <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
                <a href="registro.php">¿Crear una cuenta?</a>
                <a href="loginadmin.php">Iniciar sesión como administrador</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="version">Versión 1.0</div>
        <div class="emails">
            <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
        </div>
    </footer>

    <script>
        // Función para alternar la visibilidad de la contraseña
        document.getElementById('toggle-password').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            // Cambiar el ícono de ojo
            this.textContent = type === 'password' ? '👁️' : '🚫';
        });
    </script>
</body>
</html>