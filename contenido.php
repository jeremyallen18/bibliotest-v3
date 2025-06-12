<?php
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: index.html");
  exit;
}

include 'db.php';

// Verificar conexión
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$email = $_SESSION['email'];
$stmt_user = $conexion->prepare("SELECT bloqueado FROM usuarios WHERE email = ?");
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 1) {
  $user = $result_user->fetch_assoc();
  if ($user['bloqueado'] == 1) {
    session_destroy();
    header("Location: login.php?error=bloqueado");
    exit;
  }
}
$stmt_user->close();

// Consulta de libros con manejo de errores
$libros = [];
$stmt = $conexion->prepare("SELECT id, titulo, imagen, descripcion, cantidad_disponible FROM libros");
if ($stmt === false) {
  die("Error en la preparación de la consulta: " . $conexion->error);
}

if (!$stmt->execute()) {
  die("Error al ejecutar la consulta: " . $stmt->error);
}

$result = $stmt->get_result();

// Verificar si hay resultados
if ($result === false) {
  die("Error al obtener resultados: " . $conexion->error);
}

// Obtener todos los libros como array
$libros = [];
while ($row = $result->fetch_assoc()) {
  $libros[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang='es'>

<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Biblioteca Digital</title>
  <link id='estilo-principal' rel='stylesheet' href='css/biblioteca.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="icon" href="icono/favicon.ico" type="image/x-icon">
</head>

<body>
  <div class='library-container'>
    <div class='library-content'>
      <header class='library-header'>
        <div class='library-title'>
          <i class='fas fa-book'></i>
          <span>Bibliotest</span>
        </div>

        <div class='header-actions'>
          <!-- Botón para cambiar tema -->
          <button id='cambiar-tema' class='btn btn-secondary'>
            <i class="fas fa-sun"></i> <!-- Icono de sol -->
            <span>Cambiar modo</span>
          </button>

          <div class='nav-buttons'>
            <a href='perfil.php' class='btn btn-secondary'>
              <i class='fas fa-user-cog'></i>
              <span>Perfil</span>
            </a>
            <a href='devolver_libro.php' class='btn btn-primary'>
              <i class='fas fa-exchange-alt'></i>
              <span>Devolver libro</span>
            </a>
            <form action='noticias_tec.php' method='POST' class='btn-form'>
              <button type='submit' class='btn btn-logout'>
                <i class='fas fa-newspaper'></i>
                <span>Noticias</span>
              </button>
            </form>
            <a href='biblioteca_digital.php' class='btn btn-secondary'>
              <i class='fas fa-book-open'></i>
              <span>Biblioteca Digital</span>
            </a>
          </div>

          <div class='user-actions'>
            <a href='buscar.php' class='btn btn-primary'>
              <i class='fas fa-search'></i>
              <span>Buscar</span>
            </a>
            <a href='logout.php' class='btn btn-secondary'>
              <i class='fas fa-sign-out-alt'></i>
              <span>Salir</span>
            </a>
          </div>
        </div>
      </header>

      <div class='books-grid'>
        <?php if (!empty($libros)): ?>
          <?php foreach ($libros as $row): ?>
            <div class='book-card' onclick="window.location.href='detalle_libro.php?id=<?= $row['id'] ?>'">
              <div class='book-image-container'>
                <span class='book-badge'>Nuevo</span>
                <img src='<?= htmlspecialchars($row['imagen']) ?>' class='book-image'
                  alt='<?= htmlspecialchars($row['titulo']) ?>'>
              </div>
              <div class='book-info'>
                <h3 class='book-title'><?= htmlspecialchars($row['titulo']) ?></h3>
                <p class='book-description'><?= htmlspecialchars($row['descripcion']) ?></p>
                <div class='book-meta'>
                  <?php if ($row['cantidad_disponible'] > 0): ?>
                    <span class='book-status status-available'>Disponible</span>
                  <?php else: ?>
                    <span class='book-status status-unavailable'>Ocupado</span>
                  <?php endif; ?>

                  <span class='book-rating'><i class='fas fa-star'></i> 4.5</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class='no-books-message'>
            <i class='fas fa-book-open'></i>
            <p>No se encontraron libros en la biblioteca</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const botonTema = document.getElementById('cambiar-tema');
      const estiloPrincipal = document.getElementById('estilo-principal');

      // Verificar si hay un tema guardado en localStorage
      let temaActual = localStorage.getItem('tema') || 'biblioteca';

      // Aplicar el tema guardado al cargar la página
      if (temaActual === 'biblioteca2') {
        estiloPrincipal.href = 'css/biblioteca2.css';
      }

      // Manejar el clic en el botón de cambio de tema
      botonTema.addEventListener('click', function () {
        if (estiloPrincipal.href.includes('biblioteca.css')) {
          estiloPrincipal.href = 'css/biblioteca2.css';
          localStorage.setItem('tema', 'biblioteca2');
        } else {
          estiloPrincipal.href = 'css/biblioteca.css';
          localStorage.setItem('tema', 'biblioteca');
        }
      });
    });
  </script>
</body>

</html>

<?php
$conexion->close();
?>