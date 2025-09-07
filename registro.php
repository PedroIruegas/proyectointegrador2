<?php
include("conexion.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; 
    $telefono = $_POST['telefono'];

    // Establecer tipo de usuario por defecto
    $tipo = 'paciente';  // Todos los usuarios serán 'paciente'

    // Validación de los campos
    if (empty($nombre) || empty($apellido_materno) || empty($email) || empty($password) || empty($telefono)) {
        $message = '<div class="alert error">Por favor, complete todos los campos obligatorios.</div>';
    } elseif ($password !== $confirm_password) {
        // Verificar que las contraseñas coincidan
        $message = '<div class="alert error">Las contraseñas no coinciden.</div>';
    } else {
        // Verificar si el correo ya existe
        $sql_check_email = "SELECT * FROM usuarios WHERE correo = '$email'";
        $result_email = $conn->query($sql_check_email);

        // Verificar si el teléfono ya existe
        $sql_check_telefono = "SELECT * FROM usuarios WHERE telefono = '$telefono'";
        $result_telefono = $conn->query($sql_check_telefono);

        if ($result_email->num_rows > 0) {
            // Si el correo ya existe, mostrar mensaje de error
            $message = '<div class="alert error">El correo electrónico ya está registrado.</div>';
        } elseif ($result_telefono->num_rows > 0) {
            // Si el teléfono ya existe, mostrar mensaje de error
            $message = '<div class="alert error">El número de teléfono ya está registrado.</div>';
        } else {
            // Si el correo y el teléfono no existen, proceder con el registro
            $sql = "INSERT INTO usuarios (nombre, segundo_nombre, apellido_paterno, apellido_materno, correo, contraseña, telefono, tipo) 
                    VALUES ('$nombre', '$segundo_nombre', '$apellido_paterno', '$apellido_materno', '$email', '$password', '$telefono', '$tipo')";

            if ($conn->query($sql) === TRUE) {
                // Enviar correo de bienvenida
                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor SMTP de Gmail
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Usar el servidor SMTP de Gmail
                    $mail->SMTPAuth = true;
                    $mail->Username = 'pruebaaw30@gmail.com';  // Tu dirección de Gmail
                    $mail->Password = 'hhdb nuaj ncrg lsee';  // La contraseña de aplicación generada
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Remitente y destinatario
                    $mail->setFrom('pruebaaw30@gmail.com', 'PROYIN1');
                    $mail->addAddress($email, $nombre); // Dirección del usuario

                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenido a nuestro sitio';
                
                    $mail->Body = '
                    <html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                color: #333;
                                margin: 0;
                                padding: 0;
                                background-color: #f4f7fa;
                            }
                            .container {
                                width: 100%;
                                max-width: 600px;
                                margin: 0 auto;
                                background-color: #ffffff;
                                padding: 20px;
                                border-radius: 10px;
                                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                            }
                            .header {
                                background-color: #4CAF50;
                                padding: 10px;
                                text-align: center;
                                border-radius: 10px 10px 0 0;
                            }
                            .header h1 {
                                color: #ffffff;
                                margin: 0;
                            }
                            .content {
                                padding: 20px;
                                font-size: 16px;
                                line-height: 1.6;
                            }
                            .content a {
                                color: #4CAF50;
                                text-decoration: none;
                                font-weight: bold;
                            }
                            .footer {
                                padding: 20px;
                                text-align: center;
                                font-size: 14px;
                                color: #888;
                                background-color: #f9f9f9;
                                border-radius: 0 0 10px 10px;
                            }
                            .footer a {
                                color: #4CAF50;
                                text-decoration: none;
                            }
                            .footer img {
                                width: 200px;
                                margin-top: 10px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h1>Bienvenido</h1>
                            </div>
                            <div class="content">
                                <p>¡Hola ' . $nombre . '!</p>
                                <p>Gracias por registrarte en nuestra web. Estamos emocionados de tenerte como parte de nuestra familia. Como parte de tu bienvenida, te ofrecemos una <strong>valoración gratuita con nuestra dentista</strong>. Aprovecha esta oportunidad para conocernos mejor y comenzar tu camino hacia una sonrisa más saludable.</p>
                                <p>Te esperamos pronto.</p>
                                <p>Saludos,<br>
                                El equipo de la doctora Ximena Martínez.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';

                    // Enviar el correo
                    $mail->send();
                    $message = '<div class="alert success">Registro exitoso. Te hemos enviado un correo de bienvenida. <a href="login.php">Iniciar sesión</a></div>';
                } catch (Exception $e) {
                    $message = '<div class="alert error">Error al enviar el correo: ' . $mail->ErrorInfo . '</div>';
                }
            } else {
                $message = '<div class="alert error">Error: ' . $conn->error . '</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, rgb(77, 139, 255), #ffffff);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            width: 100%;
            max-width: 400px;
            margin: auto;
            flex-grow: 1; /* Permite que el formulario ocupe el espacio disponible */
            margin-bottom: 50px; /* Para que haya espacio para el footer */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ADD8E6;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus {
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

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            background-color: #333;
            color: white;
            text-align: center;
            font-size: 14px;
            padding: 10px 0;
            width: 100%;
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
<div class="register-container">
        <h2>Crear Cuenta</h2>

        <?php echo $message; ?>

        <form method="post">
            Nombre <span style="color: red;">*</span>: <input type="text" name="nombre" required><br>
            Segundo nombre: <input type="text" name="segundo_nombre"><br>
            Apellido Materno <span style="color: red;">*</span>: <input type="text" name="apellido_materno" required><br>
            Apellido Paterno: <input type="text" name="apellido_paterno"><br>
            Correo <span style="color: red;">*</span>: <input type="email" name="email" required><br>

            <div class="password-container">
                Contraseña <span style="color: red;">*</span>: <input type="password" name="password" id="password" required><br>
                Confirmar Contraseña <span style="color: red;">*</span>: <input type="password" name="confirm_password" id="confirm_password" required><br>
                <span class="toggle-password" id="toggle-password">👁️</span>
            </div>

            Teléfono/Celular <span style="color: red;">*</span>: <input type="tel" name="telefono" required><br>
            
            <input type="submit" value="Registrarse">
        </form>
        <div class="links">
            <a href="login.php" class="login-link">¿Ya tienes cuenta? Inicia sesión</a>
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
            var confirmPasswordField = document.getElementById('confirm_password');
            var type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
            this.textContent = type === 'password' ? '👁️' : '🚫';
        });
    </script>
</body>
</html>