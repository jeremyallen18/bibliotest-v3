<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: admin_libros.php");
    exit;
}

$id = $_GET['id'];

// Obtener datos actuales del libro
$stmt = $conexion->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$libro = $result->fetch_assoc();

if (!$libro) {
    header("Location: admin_libros.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = trim($_POST['imagen']);

    // Validación mejorada
    if (empty($titulo)) {
        $error = 'El título es obligatorio';
    } elseif (strlen($titulo) > 120) {
        $error = 'El título no debe exceder los 120 caracteres';
    } elseif (empty($descripcion)) {
        $error = 'La descripción es obligatoria';
    } elseif (strlen($descripcion) < 50) {
        $error = 'La descripción debe tener al menos 50 caracteres';
    } elseif (!filter_var($imagen, FILTER_VALIDATE_URL) && !empty($imagen)) {
        $error = 'La URL de la imagen no es válida';
    } else {
        // Actualizar el libro
        $stmt = $conexion->prepare("UPDATE libros SET titulo = ?, descripcion = ?, imagen = ? WHERE id = ?");
        $stmt->bind_param("sssi", $titulo, $descripcion, $imagen, $id);
        
        if ($stmt->execute()) {
            $success = 'Libro actualizado correctamente';
            // Actualizar los datos mostrados
            $libro['titulo'] = $titulo;
            $libro['descripcion'] = $descripcion;
            $libro['imagen'] = $imagen;
        } else {
            $error = 'Error al actualizar el libro: ' . $conexion->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro | Panel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/editar_libro.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1 class="page-title">
                <i class="fas fa-edit"></i> Editar Libro: <?= htmlspecialchars($libro['titulo']) ?>
            </h1>
            <div class="breadcrumb">
                <a href="admin_panel.php">Inicio</a> / <a href="admin_libros.php">Libros</a> / <span>Editar</span>
            </div>
        </div>

        <!-- Mensajes de estado -->
        <?php if ($error): ?>
            <div class="alert alert-error animate__animated animate__shakeX">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <h4>Error</h4>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success animate__animated animate__fadeIn">
                <div class="alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Éxito</h4>
                    <p><?= htmlspecialchars($success) ?></p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <div class="form-container animate__animated animate__fadeInUp">
            <form method="POST" class="book-form" id="bookForm">
                <div class="form-group">
                    <label for="titulo">
                        <i class="fas fa-heading"></i> Título del Libro
                    </label>
                    <input type="text" id="titulo" name="titulo" required 
                           value="<?= htmlspecialchars($libro['titulo']) ?>"
                           placeholder="Ingrese el título del libro" maxlength="120">
                    <div class="form-hint"><span id="title-counter"><?= strlen($libro['titulo']) ?></span>/120 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i> Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="6" required
                              placeholder="Escriba una descripción detallada del libro..."><?= htmlspecialchars($libro['descripcion']) ?></textarea>
                    <div class="form-hint"><span id="desc-counter"><?= strlen($libro['descripcion']) ?></span> caracteres (mínimo 50)</div>
                </div>

                <div class="form-group">
                    <label for="imagen">
                        <i class="fas fa-image"></i> URL de la Portada
                    </label>
                    <input type="text" id="imagen" name="imagen" 
                           value="<?= htmlspecialchars($libro['imagen']) ?>"
                           placeholder="https://ejemplo.com/imagen.jpg"
                           oninput="updatePreview(this.value)">
                    <div class="form-hint">Formato: URL válida (http:// o https://)</div>
                </div>

                <div class="image-preview-container">
                    <div id="image-preview" style="display: <?= !empty($libro['imagen']) ? 'block' : 'none' ?>;">
                        <?php if (!empty($libro['imagen'])): ?>
                            <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="Portada actual del libro" class="preview-image">
                            <div class="preview-overlay">
                                <span>Vista previa</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="no-image" style="display: <?= empty($libro['imagen']) ? 'flex' : 'none' ?>;">
                        <i class="fas fa-image"></i>
                        <span>No hay imagen para mostrar</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="admin_libros.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt"></i> Eliminar Libro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Contadores de caracteres
        const titleInput = document.getElementById('titulo');
        const titleCounter = document.getElementById('title-counter');
        const descInput = document.getElementById('descripcion');
        const descCounter = document.getElementById('desc-counter');
        
        titleInput.addEventListener('input', function() {
            const length = this.value.length;
            titleCounter.textContent = length;
            
            if (length > 100) {
                titleCounter.style.color = '#f72585';
            } else {
                titleCounter.style.color = '#6c757d';
            }
        });
        
        descInput.addEventListener('input', function() {
            const length = this.value.length;
            descCounter.textContent = length;
            
            if (length < 50) {
                descCounter.style.color = '#f72585';
            } else {
                descCounter.style.color = '#6c757d';
            }
        });

        // Vista previa de la imagen
        function updatePreview(url) {
            const preview = document.getElementById('image-preview');
            const noImage = document.getElementById('no-image');
            
            if (url && isValidUrl(url)) {
                preview.innerHTML = `
                    <img src="${url}" alt="Vista previa de la portada" class="preview-image">
                    <div class="preview-overlay">
                        <span>Vista previa</span>
                    </div>
                `;
                preview.style.display = 'block';
                noImage.style.display = 'none';
            } else {
                preview.style.display = 'none';
                noImage.style.display = 'flex';
            }
        }
        
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Validación del formulario
        document.getElementById('bookForm').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const imagen = document.getElementById('imagen').value.trim();
            
            if (!titulo) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campo requerido',
                    text: 'El título del libro es obligatorio',
                    confirmButtonColor: '#f72585'
                });
                return false;
            }
            
            if (titulo.length > 120) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Título muy largo',
                    text: 'El título no debe exceder los 120 caracteres',
                    confirmButtonColor: '#f72585'
                });
                return false;
            }
            
            if (!descripcion) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campo requerido',
                    text: 'La descripción del libro es obligatoria',
                    confirmButtonColor: '#f72585'
                });
                return false;
            }
            
            if (descripcion.length < 50) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Descripción muy corta',
                    text: 'La descripción debe tener al menos 50 caracteres',
                    confirmButtonColor: '#f72585'
                });
                return false;
            }
            
            if (imagen && !isValidUrl(imagen)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'URL inválida',
                    text: 'Por favor ingrese una URL válida para la imagen',
                    confirmButtonColor: '#f72585'
                });
                return false;
            }
            
            // Mostrar loader al enviar
            Swal.fire({
                title: 'Guardando cambios...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
        
        // Confirmación para eliminar libro
        function confirmDelete() {
            Swal.fire({
                title: '¿Eliminar este libro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `eliminar_libro.php?id=<?= $id ?>`;
                }
            });
        }
    </script>
</body>
</html>