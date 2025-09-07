<?php
include("conexion.php");

$mensaje = ""; // Variable para almacenar el mensaje de la ventana emergente

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sql = "SELECT * FROM usuarios WHERE correo='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Recuperar la contraseña del usuario
        $user = $result->fetch_assoc();
        $password = $user['contraseña'];

        // Asignar el mensaje para la ventana emergente
        $mensaje = "Tu contraseña es: $password";
    } else {
        // Asignar el mensaje de error si el correo no se encuentra
        $mensaje = "Correo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, rgb(77, 139, 255), #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .recover-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ADD8E6;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        input[type="submit"] {
            background-color: #ADD8E6;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #007BFF;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .login-link {
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        /* Estilo para la ventana emergente */
        .modal {
            display: none; /* Ocultamos la ventana por defecto */
            position: fixed;
            z-index: 1; /* La ventana emergente estará encima de todo */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .modal button {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .modal button:hover {
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

    footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 10px 0;
        background-color: #333;
        color: white;
        text-align: center;
        font-size: 14px;
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

    </style>
</head>
<body>
    <div class="recover-container">
        <h2>Recuperar Contraseña</h2>
        <form method="post">
            Correo: <input type="email" name="email" required><br>
            <input type="submit" value="Recuperar">
        </form>
        <div class="links">
            <a href="login.php" class="login-link">Volver</a>
        </div>
    </div>

    <!-- Ventana emergente (modal) -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <p><?php echo $mensaje; ?></p>
            <button onclick="closeModal()">Cerrar</button>
        </div>
    </div>

    <footer>
    <div class="version">Versión 1.0</div>
    <div class="emails">
        <p>walter.arrambidecst@uanl.edu.mx | miguel.gutierrezgrz@uanl.edu.mx | jorge.villanuevapmn@uanl.edu.mx | Luis.padillat@uanl.edu.mx</p>
    </div>
    </footer>

    <script>
        // Función para abrir la ventana emergente
        <?php if ($mensaje != ""): ?>
            document.getElementById('myModal').style.display = "flex";
        <?php endif; ?>

        // Función para cerrar la ventana emergente
        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }
    </script>
</body>
</html>