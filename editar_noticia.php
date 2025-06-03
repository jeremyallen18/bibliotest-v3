<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$id = (int)$_GET['id'];
$error = null;
$success = null;

// Obtener noticia actual
$stmt = $conexion->prepare("SELECT id, titulo, contenido, imagen_path FROM noticias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();

if (!$noticia) {
    die("Noticia no encontrada.");
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titulo = trim($conexion->real_escape_string($_POST['titulo']));
        $contenido = trim($conexion->real_escape_string($_POST['contenido']));
        
        if (empty($titulo) || empty($contenido)) {
            throw new Exception("Título y contenido son requeridos");
        }

        // Manejo de la imagen
        $imagenPath = $noticia['imagen_path']; // Mantener la imagen actual por defecto
        
        if (!empty($_FILES['imagen']['name'])) {
            $targetDir = "imagenes_de_noticias/";
            
            // Validar nueva imagen
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
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                // Eliminar imagen anterior si existe
                if (!empty($noticia['imagen_path']) && file_exists($noticia['imagen_path'])) {
                    unlink($noticia['imagen_path']);
                }
                $imagenPath = $targetFile;
            } else {
                throw new Exception("Error al subir la nueva imagen");
            }
        }

        // Actualizar en BD
        $updateStmt = $conexion->prepare("UPDATE noticias SET titulo=?, contenido=?, imagen_path=? WHERE id=?");
        $updateStmt->bind_param("sssi", $titulo, $contenido, $imagenPath, $id);
        
        if ($updateStmt->execute()) {
            $success = "Noticia actualizada exitosamente!";
            // Actualizar datos mostrados
            $noticia['titulo'] = $titulo;
            $noticia['contenido'] = $contenido;
            $noticia['imagen_path'] = $imagenPath;
        } else {
            // Revertir cambios en imagen si falla la BD
            if ($imagenPath !== $noticia['imagen_path'] && file_exists($imagenPath)) {
                unlink($imagenPath);
            }
            throw new Exception("Error al actualizar: " . $updateStmt->error);
        }
        
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
    <title>Editar Noticia - Panel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/editar_noticia.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h2 class="page-title">
                <i class="fas fa-edit"></i> Editar Noticia
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
                           value="<?= htmlspecialchars($noticia['titulo']) ?>"
                           placeholder="Título de la noticia">
                </div>

                <div class="form-group">
                    <label for="contenido">
                        <i class="fas fa-align-left"></i> Contenido
                    </label>
                    <textarea id="contenido" name="contenido" rows="8" required
                              placeholder="Contenido completo..."><?= htmlspecialchars($noticia['contenido']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen">
                        <i class="fas fa-image"></i> Imagen
                    </label>
                    <?php if (!empty($noticia['imagen_path'])): ?>
                        <div class="current-image">
                            <img src="/<?= htmlspecialchars($noticia['imagen_path']) ?>" 
                                 alt="Imagen actual" style="max-width: 200px; display: block; margin-bottom: 10px;">
                            <label>
                                <input type="checkbox" name="eliminar_imagen" value="1"> 
                                Eliminar imagen actual
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                    <small class="file-hint">Formatos: JPG, PNG, GIF (Máx. 2MB)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="admin_noticias.php" class="cancel-btn">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación del formulario
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

        // Confirmación antes de eliminar imagen
        const eliminarCheckbox = document.querySelector('input[name="eliminar_imagen"]');
        if (eliminarCheckbox) {
            eliminarCheckbox.addEventListener('change', function() {
                if (this.checked && !confirm('¿Estás seguro de eliminar esta imagen?')) {
                    this.checked = false;
                }
            });
        }
    </script>
</body>
</html>