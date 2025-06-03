<?php
// recuperar.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #252432;
            padding: 20px;
        }

        @property --a {
            syntax: "<angle>";
            inherits: false;
            initial-value: 0deg;
        }

        .box {
            position: relative;
            width: 400px;
            background: repeating-conic-gradient(from var(--a), #ff2770 0%, #ff2770 5%, transparent 5%, transparent 40%, #ff2770 50%);
            filter: drop-shadow(0 15px 50px #000);
            animation: rotating 4s linear infinite;
            border-radius: 20px;
            padding: 2px;
        }

        .box::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-conic-gradient(from var(--a), #45f3ff 0%, #45f3ff 5%, transparent 5%, transparent 40%, #45f3ff 50%);
            filter: drop-shadow(0 15px 50px #000);
            border-radius: 20px;
            animation: rotating 4s linear infinite;
            animation-delay: -1s;
        }

        .box::after {
            content: "";
            position: absolute;
            inset: 4px;
            background: #2d2d39;
            border-radius: 15px;
            border: 8px solid #25252b;
            z-index: 0;
        }

        @keyframes rotating {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }

        .content {
            position: relative;
            z-index: 1;
            padding: 30px;
            color: white;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #ccc;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background-color: #f5f5f5;
            color: #000;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #45f3ff;
            border: none;
            border-radius: 5px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #2e88d0;
            color: #fff;
        }

        .nav-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .nav-button {
            text-align: center;
            padding: 10px 15px;
            background-color: #ff2770;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .nav-button:hover {
            background-color: #d91b5c;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="content">
            <h2>Recuperar Contraseña</h2>
            <form action="enviar_token.php" method="post">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required>
                <button type="submit">Enviar enlace</button>
            </form>
            <div class="nav-buttons">
                <a href="/biblioteca/index.html" class="nav-button">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
