<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.html');
    exit();
}

include 'db.php';

if (isset($_POST['devolver'])) {
    $prestamo_id = $_POST['prestamo_id'];
    $fecha_devolucion = date('Y-m-d');

    $stmt = $conexion->prepare("UPDATE prestamos SET estado = 'Devuelto', fecha_devolucion = ? WHERE id = ?");
    $stmt->bind_param('si', $fecha_devolucion, $prestamo_id);

    if ($stmt->execute()) {
        $mensaje = "<div class='mensaje-exito'>Libro devuelto exitosamente!</div>";
    } else {
        $mensaje = "<div class='mensaje-error'>Error al devolver el libro.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesar Devolución</title>
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --color-primary: #45f3ff;
            --color-secondary: #ff2770;
            --color-dark: #252432;
            --color-dark-light: #2d2d39;
            --color-text: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--color-dark);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background: var(--color-dark-light);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent,
                transparent,
                transparent,
                var(--color-primary),
                linear-gradient(
                to bottom left,
                transparent,
                transparent,
                transparent,
                var(--color-secondary));
            transform: rotate(30deg);
            animation: shine 6s linear infinite;
            opacity: 0.1;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }
        
        .mensaje-exito, .mensaje-error {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: 500;
            animation: slideDown 0.5s ease-out;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .mensaje-exito {
            background: rgba(0, 200, 83, 0.2);
            border: 1px solid #00c853;
            color: #00c853;
        }
        
        .mensaje-error {
            background: rgba(255, 23, 68, 0.2);
            border: 1px solid #ff1744;
            color: #ff1744;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            width: 100%;
            max-width: 800px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: var(--color-primary);
            color: var(--color-dark);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(69, 243, 255, 0.3);
            animation: pulse 2s infinite;
            text-align: center;
            min-width: 200px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(69, 243, 255, 0.4);
        }
        
        .btn-secondary {
            background: var(--color-secondary);
            box-shadow: 0 5px 15px rgba(255, 39, 112, 0.3);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 8px 20px rgba(255, 39, 112, 0.4);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .libro-icono {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--color-primary);
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="libro-icono">📚</div>
        <?php if (isset($mensaje)) echo $mensaje; ?>
    </div>
    
    <div class="btn-container">
        <a href="contenido.php" class="btn">Volver al Menú Principal</a>
        <a href="devolver_libro.php" class="btn btn-secondary">Regresar a préstamos</a>
    </div>
</body>
</html>