<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos
        $titulo = trim($conexion->real_escape_string($_POST['titulo']));
        $contenido = trim($conexion->real_escape_string($_POST['contenido']));
        
        if (empty($titulo) || empty($contenido)) {
            throw new Exception("Título y contenido son requeridos");
        }

        // Procesar imagen
        $imagenPath = null;
        if (!empty($_FILES['imagen']['name'])) {
            $targetDir = "imagenes_de_noticias/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Validaciones
            $fileType = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Solo se permiten imágenes JPG, JPEG, PNG o GIF");
            }
            
            if ($_FILES['imagen']['size'] > $maxSize) {
                throw new Exception("La imagen es demasiado grande (máx. 2MB)");
            }
            
            // Generar nombre único
            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $_FILES['imagen']['name']);
            $targetFile = $targetDir . $fileName;
            
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                throw new Exception("Error al subir la imagen");
            }
            
            $imagenPath = $targetFile;
        }

        // Insertar en BD
        $stmt = $conexion->prepare("INSERT INTO noticias (titulo, contenido, fecha, imagen_path) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("sss", $titulo, $contenido, $imagenPath);
        
        if (!$stmt->execute()) {
            if ($imagenPath && file_exists($imagenPath)) {
                unlink($imagenPath); // Limpiar archivo si falla la BD
            }
            throw new Exception("Error al guardar: " . $stmt->error);
        }
        
        $success = "Noticia publicada exitosamente!";
        $_POST = array(); // Limpiar formulario
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Noticia</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/agregar_noticia.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h2 class="page-title">
                <i class="fas fa-plus-circle"></i> Agregar Noticia
            </h2>
        </div>

        <!-- Mensajes de estado -->
        <?php if ($error): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" enctype="multipart/form-data" class="news-form">
                <div class="form-group">
                    <label for="titulo">
                        <i class="fas fa-heading"></i> Título
                    </label>
                    <input type="text" id="titulo" name="titulo" required 
                           value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                           placeholder="Título de la noticia">
                </div>

                <div class="form-group">
                    <label for="contenido">
                        <i class="fas fa-align-left"></i> Contenido
                    </label>
                    <textarea id="contenido" name="contenido" rows="8" required
                              placeholder="Contenido completo..."><?= htmlspecialchars($_POST['contenido'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen">
                        <i class="fas fa-image"></i> Imagen (Opcional)
                    </label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                    <small class="file-hint">Formatos: JPG, PNG, GIF (Máx. 2MB)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Publicar Noticia
                    </button>
                    <a href="admin_noticias.php" class="cancel-btn">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación básica del formulario
        document.querySelector('.news-form').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const contenido = document.getElementById('contenido').value.trim();
            const imagen = document.getElementById('imagen').files[0];
            
            if (!titulo || !contenido) {
                e.preventDefault();
                alert('Título y contenido son requeridos');
                return false;
            }
            
            if (imagen) {
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(imagen.type)) {
                    e.preventDefault();
                    alert('Solo se permiten imágenes JPG, PNG o GIF');
                    return false;
                }
                
                if (imagen.size > maxSize) {
                    e.preventDefault();
                    alert('La imagen es demasiado grande (máx. 2MB)');
                    return false;
                }
            }
            
            return true;
        });
    </script>
</body>
</html>