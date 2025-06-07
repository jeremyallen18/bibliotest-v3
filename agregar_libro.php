<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $cantidad = trim($_POST['cantidad']);
    $imagen = trim($_POST['imagen']);

    // Validación mejorada
    if (empty($titulo)) {
        $error = 'El título es obligatorio';
    } elseif (strlen($titulo) > 120) {
        $error = 'El título no debe exceder los 120 caracteres';
    } elseif (empty($descripcion)) {
        $error = 'La descripción es obligatoria';
    } elseif (!filter_var($imagen, FILTER_VALIDATE_URL) && !empty($imagen)) {
        $error = 'La URL de la imagen no es válida';
    } elseif (!is_numeric($cantidad) || intval($cantidad) < 1) {
        $error = 'La cantidad debe ser un número entero mayor a 0';
    } else {
        // Insertar en la base de datos
        $stmt = $conexion->prepare("INSERT INTO libros (titulo, descripcion, imagen, cantidad_disponible) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $titulo, $descripcion, $imagen, $cantidad);


        if ($stmt->execute()) {
            $success = 'Libro agregado correctamente';
            // Limpiar los campos después de un éxito
            $_POST = array();
        } else {
            $error = 'Error al agregar el libro: ' . $conexion->error;
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
    <title>Agregar Libro | Panel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/agregar_libro.css">
    <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="admin-container">
        <div class="header">
            <h1 class="page-title">
                <i class="fas fa-book"></i> Agregar Nuevo Libro
            </h1>
            <div class="breadcrumb">
                <a href="admin_panel.php">Inicio</a> / <a href="admin_libros.php">Libros</a> / <span>Agregar</span>
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
                        value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                        placeholder="Ingrese el título del libro" maxlength="120">
                    <div class="form-hint"><span id="title-counter">0</span>/120 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="descripcion">
                        <i class="fas fa-align-left"></i> Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="6" required
                        placeholder="Escriba una descripción detallada del libro..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                    <div class="form-hint">Mínimo 50 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="cantidad">
                        <i class="fas fa-sort-numeric-up-alt"></i> Cantidad Disponible
                    </label>
                    <input type="number" id="cantidad" name="cantidad" min="1"
                        value="<?= htmlspecialchars($_POST['cantidad'] ?? '') ?>"
                        placeholder="Ingrese la cantidad disponible" required>
                    <div class="form-hint">Solo números mayores a 0</div>
                </div>


                <div class="form-group">
                    <label for="imagen">
                        <i class="fas fa-image"></i> URL de la Portada (Opcional)
                    </label>
                    <input type="text" id="imagen" name="imagen" value="<?= htmlspecialchars($_POST['imagen'] ?? '') ?>"
                        placeholder="https://ejemplo.com/imagen.jpg">
                    <div class="form-hint">Formato: URL válida (http:// o https://)</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Libro
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                    <a href="admin_libros.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Contador de caracteres para el título
        const titleInput = document.getElementById('titulo');
        const titleCounter = document.getElementById('title-counter');

        titleInput.addEventListener('input', function () {
            const length = this.value.length;
            titleCounter.textContent = length;

            if (length > 100) {
                titleCounter.style.color = '#f72585';
            } else {
                titleCounter.style.color = '#6c757d';
            }
        });

        // Validación del formulario
        document.getElementById('bookForm').addEventListener('submit', function (e) {
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
                title: 'Guardando libro...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
        const cantidad = document.getElementById('cantidad').value.trim();
        if (!cantidad || isNaN(cantidad) || parseInt(cantidad) < 1) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Cantidad inválida',
                text: 'Ingrese una cantidad válida (mayor a 0)',
                confirmButtonColor: '#f72585'
            });
            return false;
        }
    </script>
</body>

</html>