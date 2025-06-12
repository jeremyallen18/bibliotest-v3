<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

// Inicializar variables
$mensaje = '';
$error = '';
$titulo = $descripcion = '';

// Directorio para guardar PDFs
$directorioPDF = 'uploads/libros_digitales/';
if (!file_exists($directorioPDF)) {
    mkdir($directorioPDF, 0777, true);
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conexion->real_escape_string(trim($_POST['titulo'] ?? ''));
    $descripcion = $conexion->real_escape_string(trim($_POST['descripcion'] ?? ''));
    
    // Validaciones
    if (empty($titulo)) {
        $error = "El título es obligatorio";
    } elseif (empty($_FILES['archivo_pdf']['name'])) {
        $error = "Debe seleccionar un archivo PDF";
    } else {
        // Validar archivo PDF
        $allowed = ['application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['archivo_pdf']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed)) {
            $error = "Solo se permiten archivos PDF";
        } elseif ($_FILES['archivo_pdf']['size'] > 10 * 1024 * 1024) {
            $error = "El archivo no puede ser mayor a 10MB";
        }
    }
    
    // Si no hay errores, subir archivo y guardar en BD
    if (empty($error)) {
        $nombreArchivo = uniqid('libro_') . '.pdf';
        $rutaArchivo = $directorioPDF . $nombreArchivo;
        
        if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $rutaArchivo)) {
            $sql = "INSERT INTO libros_digitales (titulo, descripcion, archivo_pdf) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $titulo, $descripcion, $rutaArchivo);
            
            if ($stmt->execute()) {
                $mensaje = "Libro digital agregado correctamente";
                $titulo = $descripcion = '';
            } else {
                $error = "Error al guardar en la base de datos: " . $conexion->error;
                // Eliminar archivo subido si falló la BD
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }
            $stmt->close();
        } else {
            $error = "Error al subir el archivo";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro Digital - Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 1.5rem;
        }
        
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 1rem;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .header nav a:hover {
            background-color: #34495e;
        }
        
        /* Contenedor principal */
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        /* Formulario */
        .form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group input[type="text"],
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Botones */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        /* Alertas */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .error {
            background-color: #fdecea;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        
        /* Footer */
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .header nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <h1><i class="fas fa-book-open"></i> Biblioteca Digital - Panel Admin</h1>
        <nav>
            <a href="admin_panel.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="admin_libros.php"><i class="fas fa-book"></i> Libros</a>
        </nav>
    </header>
    
    <!-- Contenido principal -->
    <main class="container">
        <h2><i class="fas fa-plus-circle"></i> Agregar Libro Digital</h2>
        
        <?php if ($error): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($mensaje): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <form action="agregar_libro_digital.php" method="post" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="titulo"><i class="fas fa-heading"></i> Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="archivo_pdf"><i class="fas fa-file-pdf"></i> Archivo PDF (Máx. 10MB):</label>
                <input type="file" id="archivo_pdf" name="archivo_pdf" accept=".pdf" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Libro Digital
                </button>
                <a href="admin_libros.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a la lista
                </a>
            </div>
        </form>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <p>Sistema de Biblioteca Digital &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
        <p>Versión 1.0.0</p>
    </footer>
</body>
</html>