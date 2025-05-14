<?php
include '../db/dbHotel.php';

$conn = $pdo ?? $GLOBALS['pdo'];

//print_r($_POST);die();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Recebe os dados do POST
    $reserva_id = $_POST['reserva_id'] ?? null;
    $nota = $_POST['nota'] ?? null;
    $comentario = $_POST['comentarios'] ?? '';

    // Validações básicas
    if (empty($reserva_id)) {
        throw new Exception("ID da reserva é obrigatório");
    }
    
    if (empty($nota)) {
        throw new Exception("Nota é obrigatória");
    }

    // Verifica se já existe avaliação para esta reserva
    $stmt = $pdo->prepare("SELECT id FROM avaliacoes WHERE reserva_id = :reserva_id LIMIT 1");
    $stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $avaliacao_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($avaliacao_existente) {
        // Se existir, faz UPDATE
        $stmt = $pdo->prepare("UPDATE avaliacoes 
                              SET nota = :nota, 
                                  comentario = :comentario, 
                                  criado_em = NOW() 
                              WHERE id = :id");
        
        $stmt->bindParam(':id', $avaliacao_existente['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        
        $stmt->execute();

        $message = 'Avaliação atualizada com sucesso!';
        $avaliacao_id = $avaliacao_existente['id'];
    } else {
        // Se não existir, faz INSERT
        $stmt = $pdo->prepare("INSERT INTO avaliacoes (reserva_id, nota, comentario) 
                              VALUES (:reserva_id, :nota, :comentario)");
        
        $stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        
        $stmt->execute();

        $message = 'Avaliação cadastrada com sucesso!';
        $avaliacao_id = $pdo->lastInsertId();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    
  } catch (Exception $e) {
      error_log("Erro no processamento: " . $e->getMessage());
      header("Location: " . $_SERVER['HTTP_REFERER'] . "?erro=1");
      exit;
  }
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/components.css">

<!-- Modal de Avaliação -->
<div class="modal fade" id="avaliacaoModal" tabindex="-1" aria-labelledby="avaliacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Cabeçalho do Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="avaliacaoModalLabel">
                    <i class="fas fa-star me-2"></i>Avaliar Hospedagem
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <form action="#" method="POST">
                    <input type="hidden" name="reserva_id" id="reserva_id">

                    <!-- Avaliação -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Sua Avaliação:</label>
                        <div class="star-rating">
                            <?php 
                              $defaultRating = 5;
                              for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $defaultRating ? 'text-warning' : '' ?>"
                                data-rating="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="nota" id="nota" value="<?= $defaultRating ?>" required>
                    </div>

                    <!-- Comentários -->
                    <div class="mb-4">
                        <label for="comentarios" class="form-label fw-bold">Comentários:</label>
                        <textarea class="form-control" name="comentarios" id="comentarios" rows="4"
                            placeholder="Conte-nos sobre sua experiência..."></textarea>
                    </div>

                    <!-- Botão de Enviar -->
                    <button type="submit" class="btn btn-success w-100 py-2">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Avaliação
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de classificação por estrelas
document.querySelectorAll('.star-rating .fa-star').forEach(star => {
    star.addEventListener('click', () => {
        const rating = star.dataset.rating;
        document.getElementById('nota').value = rating;

        // Atualiza visualização das estrelas
        document.querySelectorAll('.star-rating .fa-star').forEach((s, index) => {
            s.classList.toggle('fas', index < rating);
            s.classList.toggle('far', index >= rating);
        });
    });
});

// Função para abrir modal de avaliação
function abrirModalAvaliacao(reservaId) {
    const modal = document.getElementById('avaliacaoModal'); // Corrigido o ID
    if (modal) {
        // Configurar o modal com o ID da reserva
        document.getElementById('reserva_id').value = reservaId;

        // Abrir o modal
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    } else {
        console.error('Modal de avaliação não encontrado.');
    }
}

// Atualiza visual inicial das estrelas
document.querySelectorAll('.star-rating .fa-star').forEach(star => {
    star.classList.add('far');
});
</script>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>