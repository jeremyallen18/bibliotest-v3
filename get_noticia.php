<?php
require 'db.php';

if (!isset($_GET['id'])) {
    die('ID de noticia no especificado');
}

$noticiaId = intval($_GET['id']);

try {
    $stmt = $conexion->prepare("SELECT titulo, contenido, fecha, imagen FROM noticias WHERE id = ?");
    $stmt->bind_param('i', $noticiaId);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    
    if (!$noticia) {
        die('Noticia no encontrada');
    }
} catch (Exception $e) {
    die("Error al cargar la noticia: " . $e->getMessage());
}
?>

<div class="modal-news">
    <?php if (!empty($noticia['imagen'])): ?>
    <div class="modal-image">
        <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
    </div>
    <?php endif; ?>
    
    <h2 class="modal-title"><?= htmlspecialchars($noticia['titulo']) ?></h2>
    
    <div class="modal-meta">
        <span class="modal-date">
            <i class="far fa-calendar-alt"></i>
            <?= date('d M Y', strtotime($noticia['fecha'])) ?>
        </span>
    </div>
    
    <div class="modal-content-text">
        <?= nl2br(htmlspecialchars($noticia['contenido'])) ?>
    </div>
</div>

<style>
    .modal-news {
        padding: 20px;
    }
    
    .modal-image {
        width: 100%;
        height: 300px;
        overflow: hidden;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    
    .modal-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .modal-title {
        color: var(--cyan);
        margin-bottom: 15px;
        font-size: 1.8rem;
    }
    
    .modal-meta {
        margin-bottom: 20px;
        color: var(--text-muted);
    }
    
    .modal-content-text {
        line-height: 1.8;
    }
    
    @media (max-width: 768px) {
        .modal-image {
            height: 200px;
        }
        
        .modal-title {
            font-size: 1.5rem;
        }
    }
</style>