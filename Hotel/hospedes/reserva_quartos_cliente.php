<?php
session_start();
require_once '.././db/dbHotel.php';

// Verificação de autenticação
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$usuarioId = (int)$_SESSION['id'];

// Função para sanitizar e validar entradas
function sanitizeInput($data, $type = 'string') {
    if (empty($data)) return null;
    
    $data = trim($data);
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            $clean = preg_replace('/[^\d.,]/', '', $data);
            if (strpos($clean, ',') !== false) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            }
            return (float) $clean;
        case 'date':
            // Aceita formatos: Y-m-d (HTML5 date input) e d/m/Y (formato brasileiro)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                return $data;
            } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
                $date = DateTime::createFromFormat('d/m/Y', $data);
                return $date ? $date->format('Y-m-d') : null;
            }
            return null;
        case 'string':
        default:
            return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
}

// Processar filtros com tratamento robusto
$filtros = [
    'tipo_cama' => isset($_GET['tipo_cama']) && in_array($_GET['tipo_cama'], ['Solteiro', 'Casal', 'Beliche', ''])
        ? $_GET['tipo_cama'] // Já validado, não precisa sanitizar
        : null,
    'preco_max' => null,
    'checkin' => null,
    'checkout' => null
];

// Processar preço máximo
if (isset($_GET['preco_max']) && !empty(trim($_GET['preco_max']))) {
    $preco = sanitizeInput($_GET['preco_max'], 'float');
    $filtros['preco_max'] = ($preco !== null && $preco > 0) ? $preco : null;
}

// Processar datas com validação cruzada
if (isset($_GET['checkin']) && !empty(trim($_GET['checkin']))) {
    $filtros['checkin'] = sanitizeInput($_GET['checkin'], 'date');
}

if (isset($_GET['checkout']) && !empty(trim($_GET['checkout']))) {
    $filtros['checkout'] = sanitizeInput($_GET['checkout'], 'date');
}

// Validar consistência das datas
if ($filtros['checkin'] && $filtros['checkout']) {
    try {
        $checkin = new DateTime($filtros['checkin']);
        $checkout = new DateTime($filtros['checkout']);
        
        if ($checkin >= $checkout) {
            $_SESSION['erro_data'] = "A data de check-out deve ser posterior ao check-in";
            $filtros['checkin'] = null;
            $filtros['checkout'] = null;
        }
    } catch (Exception $e) {
        $_SESSION['erro_data'] = "Formato de data inválido";
        $filtros['checkin'] = null;
        $filtros['checkout'] = null;
    }
}

// Função para buscar quartos com tratamento de erros
function buscarQuartos(array $filtros = [], PDO $pdo): array
{
    try {
        $sql = "SELECT q.*, (q.camas_solteiro + (q.beliches * 2) + q.camas_casal * 2) AS capacidade 
                FROM quartos q
                WHERE q.status = 'Disponível'";

        $params = [];
        $conditions = [];

        // Filtro por tipo de cama
        if (!empty($filtros['tipo_cama'])) {
            switch ($filtros['tipo_cama']) {
                case 'Solteiro':
                    $conditions[] = "q.camas_solteiro > 0";
                    break;
                case 'Casal':
                    $conditions[] = "q.camas_casal > 0";
                    break;
                case 'Beliche':
                    $conditions[] = "q.beliches > 0";
                    break;
            }
        }

        // Filtro por preço máximo
        if (!empty($filtros['preco_max']) && is_numeric($filtros['preco_max'])) {
            $conditions[] = "q.preco <= ?";
            $params[] = $filtros['preco_max'];
        }

        // Filtro por disponibilidade nas datas (VERSÃO CORRIGIDA)
        if (!empty($filtros['checkin']) && !empty($filtros['checkout'])) {
          $conditions[] = "q.id NOT IN (
              SELECT r.quarto_id 
              FROM reservas r 
              WHERE (
                  (r.data_checkin <= ? AND r.data_checkout >= ?) OR  -- Reserva que cobre o período pesquisado
                  (r.data_checkin <= ? AND r.data_checkout >= ?) OR  -- Reserva que começa antes e termina durante
                  (r.data_checkin >= ? AND r.data_checkout <= ?) OR  -- Reserva dentro do período pesquisado
                  (r.data_checkin <= ? AND r.data_checkout >= ?)     -- Reserva que começa durante e termina depois
              )
              AND r.status NOT IN ('cancelada', 'finalizada')
          )";
      
          $params = array_merge($params, [
              $filtros['checkin'], $filtros['checkout'],  // Para primeira condição
              $filtros['checkin'], $filtros['checkin'],  // Para segunda condição
              $filtros['checkin'], $filtros['checkout'],  // Para terceira condição
              $filtros['checkout'], $filtros['checkout']  // Para quarta condição
          ]);
      }

        // Adicionar condições à query
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY q.preco ASC";

        $stmt = $pdo->prepare($sql);
        //print_r($stmt);die();
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar quartos: " . $e->getMessage());
        return [];
    }
}

// Obter informações do usuário
try {
    $stmtUsuario = $pdo->prepare("SELECT tipo_documento, cpf_cnpj FROM usuarios WHERE id = ?");
    $stmtUsuario->execute([$usuarioId]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: ../index.php");
        exit;
    }

    $tipoUsuario = ($usuario['tipo_documento'] === 'CPF') ? 'pf' : 'pj';
    $bloqueioPF = false;

    // Buscar quartos disponíveis
    $quartos = buscarQuartos($filtros, $pdo);
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    $quartos = [];
    $tipoUsuario = 'pf';
    $bloqueioPF = false;
}

$tipoUsuario = 'pj';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quartos Disponíveis - Apê Pousada</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/reserva_quartos_cliente.css">
</head>

<body class="bg-light">
  <?php include("../components/navbar.php"); ?>

  <!-- Seção Hero -->
  <header class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="display-4 mt-5 text-light"><i class="fas fa-door-open text-light me-2"></i>Quartos Disponíveis</h1>
    </div>
  </header>

  <!-- Container Principal -->
  <main class="container container-main my-5 bg-light">
    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-5">
      <div class="card-body">
        <form class="row g-3 align-items-end" method="get">
          <div class="row g-2">
          <div class="col-md-3">
    <label class="form-label">Período</label>
    <div class="input-group">
        <input type="date" class="form-control" id="checkin" name="checkin" 
               value="<?= htmlspecialchars($filtros['checkin'] ?? '') ?>"
               min="<?php echo date('Y-m-d'); ?>"
               onchange="habilitarCheckout()">
        <input type="date" class="form-control" id="checkout" name="checkout" 
               value="<?= htmlspecialchars($filtros['checkout'] ?? '') ?>" disabled>
    </div>
</div>
            <div class="col-md-3">
              <label class="form-label">Tipo de Cama</label>
              <select class="form-select" name="tipo_cama">
                <option value="">Todos</option>
                <option value="Solteiro" <?= ($filtros['tipo_cama'] ?? '') === 'Solteiro' ? 'selected' : '' ?>>Solteiro</option>
                <option value="Casal" <?= ($filtros['tipo_cama'] ?? '') === 'Casal' ? 'selected' : '' ?>>Casal</option>
                <option value="Beliche" <?= ($filtros['tipo_cama'] ?? '') === 'Beliche' ? 'selected' : '' ?>>Beliche</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Preço Máximo</label>
              <div class="input-group">
                <input type="text" class="form-control" id="preco_max" name="preco_max"
                  placeholder="0,00" oninput="formatarPreco(this)"
                  value="<?= isset($_GET['preco_max']) ? htmlspecialchars($_GET['preco_max']) : '' ?>">
              </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button class="btn btn-success w-100">
                <i class="fas fa-filter me-2"></i>Filtrar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php if (empty($quartos)) : ?>
      <div class="alert alert-warning">
        <i class="fas fa-info-circle me-2"></i>
        Nenhum quarto disponível encontrado com os filtros aplicados.
        <?php if (!empty($filtros['checkin']) || !empty($filtros['checkout'])) : ?>
          <a href="?tipo_cama=<?= htmlspecialchars($filtros['tipo_cama']) ?>&preco_max=<?= htmlspecialchars($_GET['preco_max'] ?? '') ?>" class="alert-link">Ver disponibilidade sem filtro de datas</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Cards para Pessoa Física -->
    <?php if ($tipoUsuario == "pf") : ?>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($quartos as $quarto) : ?>
          <div class="col">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
              <div class="position-relative">
                <div class="image-overlay" style="height: 200px; overflow: hidden;">
                  <img src="../uploads/<?= htmlspecialchars($quarto['imagem'] ?? '1740695620_teste.jpeg') ?>"
                    class="img-fluid w-100 h-100 object-fit-cover transition-scale"
                    alt="Quarto <?= htmlspecialchars($quarto['numero']) ?>">
                </div>
                <span class="badge bg-success position-absolute top-0 start-0 m-2">
                  <i class="fas fa-coffee me-2"></i>Café da Manhã
                </span>
                <span class="badge bg-success-subtle text-success position-absolute bottom-0 end-0 m-2">
                  R$ <?= number_format($quarto['preco'], 2, ',', '.') ?>/noite
                </span>
              </div>
              <div class="card-body">
                <h5 class="card-title mb-2 fw-semibold">
                  Quarto #<?= htmlspecialchars($quarto['numero']) ?>
                </h5>
                <p class="text-muted small mb-3">
                  <?= htmlspecialchars($quarto['descricao']) ?>
                </p>

                <div class="d-flex flex-column gap-2 mb-4">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-users text-success me-3 fs-5"></i>
                    <span class="text-muted">Capacidade: <?= $quarto['capacidade'] ?> pessoas</span>
                  </div>
                </div>

                <button class="btn btn-success w-100 d-flex align-items-center justify-content-center py-2"
                  data-bs-toggle="modal"
                  data-bs-target="#reservaModal"
                  data-quarto-id="<?= $quarto['id'] ?>"
                  data-quarto-numero="<?= $quarto['numero'] ?>"
                  data-quarto-preco="<?= $quarto['preco'] ?>"
                  data-quarto-img="../uploads/<?= htmlspecialchars($quarto['imagem'] ?? '1740695620_teste.jpeg') ?>"
                  data-quarto-descricao="<?= htmlspecialchars($quarto['descricao']) ?>"
                  data-quarto-capacidade="<?= $quarto['capacidade'] ?>"
                  data-quarto-regras='<?= isset($quarto['regras']) ? json_encode(json_decode($quarto['regras'])) : json_encode([]) ?>'>
                  <i class="fas fa-calendar-check me-2"></i>Reservar Agora
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Cards para Pessoa Jurídica -->
    <?php elseif ($tipoUsuario == "pj") : ?>
      <div class="row g-4">
        <?php foreach ($quartos as $quarto) : ?>
          <div class="col-12">
            <div class="card border-0 shadow-sm h-100 hover-effect">
              <div class="row g-0 h-100">
                <div class="col-md-4 position-relative">
                  <div class="image-overlay" style="height: 250px;">
                    <img src="../uploads/<?= htmlspecialchars($quarto['imagem'] ?? '1740695620_teste.jpeg') ?>"
                      class="img-fluid h-100 w-100 object-fit-cover"
                      alt="Quarto <?= htmlspecialchars($quarto['numero']) ?>">
                    <span class="badge bg-success position-absolute top-0 start-0 m-2">
                      <i class="fas fa-coffee me-2"></i>Café da Manhã
                    </span>
                  </div>
                </div>

                <div class="col-md-8 d-flex flex-column">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h3 class="h5 mb-1 fw-semibold">Quarto #<?= htmlspecialchars($quarto['numero']) ?></h3>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($quarto['descricao']) ?></p>
                      </div>
                      <span class="badge bg-success-subtle text-success fs-6">
                        R$ <?= number_format($quarto['preco'], 2, ',', '.') ?>/noite
                      </span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-users text-success me-3 fs-5"></i>
                        <span class="text-muted"><?= $quarto['capacidade'] ?> pessoa(s)</span>
                      </div>
                    </div>
                  </div>

                <div class="card-footer bg-transparent border-0 mt-auto">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check form-switch">
                      <input type="checkbox" class="form-check-input quarto-checkbox" id="quarto<?= $quarto['id'] ?>">
                      <label class="form-check-label small" for="quarto<?= $quarto['id'] ?>">Selecionar</label>
                    </div>
                    <button class="btn btn-outline-success d-flex align-items-center"
                      data-bs-toggle="modal"
                      data-bs-target="#reservaModal"
                      data-quarto-img="../uploads/<?= htmlspecialchars($quarto['imagem'] ?? '1740695620_teste.jpeg') ?>">
                      <i class="fas fa-info-circle me-2"></i>Ver Detalhes
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>


      <?php if (!empty($quartos)) : ?>
        <div class="d-grid mt-4">
          <button class="btn btn-success py-3" id="finalizarReservaBtn" style="display: none;"
            data-bs-toggle="modal" data-bs-target="#reservaModalGrupo">
            <i class="fas fa-check-circle me-2"></i>Finalizar Reserva dos Quartos Selecionados
          </button>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </main>

  <!-- Modal de Reserva Individual-->
  <div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form method="POST" action="./funcoes.php">
        <input type="hidden" name="quarto_id" id="modal_quarto_id">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h3 class="modal-title fw-bold mb-0"><i class="fas fa-calendar-check me-2"></i>Reserva do Quarto</h3>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>

          <div class="modal-body p-4">
            <div class="row g-4 mb-4">
              <div class="col-md-6">
                <div class="position-relative rounded-4 overflow-hidden shadow-sm">
                  <img id="modal-img" src="" class="img-fluid rounded-top-3 w-100"
                    style="height: 250px; object-fit: cover;" alt="Imagem do Quarto">
                  <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5 class="text-white mb-0">Quarto #<span id="modal-quarto-numero"></span></h5>
                      <span class="badge bg-success fs-6">R$ <span id="modal-quarto-preco"></span>/noite</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card border-success h-100">
                  <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Informações e Regras</h5>
                  </div>
                  <div class="card-body">
                    <p id="modal-quarto-descricao" class="mb-3"></p>
                    <div class="d-flex align-items-center mb-2">
                      <i class="fas fa-users text-success me-2"></i>
                      <span id="modal-quarto-capacidade"></span>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-success mb-3">Regras do Quarto:</h6>
                    <ul class="list-unstyled mb-0" id="modal-quarto-regras">
                      <!-- Regras serão preenchidas via JavaScript -->
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label text-success fw-bold">Data de Check-in</label>
                <input type="date" class="form-control border-success"
                  name="check_in" id="modal-check-in" required
                  min="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label text-success fw-bold">Data de Check-out</label>
                <input type="date" class="form-control border-success"
                  name="check_out" id="modal-check-out" required
                  min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label text-success fw-bold">Valor Total</label>
                <div class="input-group">
                  <span class="input-group-text bg-success text-white">R$</span>
                  <input type="text" class="form-control fw-bold border-success"
                    id="modal-valor-total" name="valor_total" disabled>
                </div>
              </div>

              <?php if ($tipoUsuario === 'pf' && !$bloqueioPF): ?>
                <div class="col-md-4">
                  <label class="form-label text-success fw-bold">Forma de Pagamento</label>
                  <select class="form-select border-success" name="pagamento" id="modal-pagamento">
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão de Crédito</option>
                    <option value="debito">Cartão de Débito</option>
                  </select>
                </div>
              <?php endif; ?>

              <div class="col-md-4">
                <div id="modal-parcelas-div" style="display: none;">
                  <label class="form-label text-success fw-bold">Parcelas</label>
                  <input type="number" class="form-control fw-bold border-success"
                    name="parcelas" id="modal-parcelas" min="1" max="12" value="1">
                </div>
              </div>
            </div>

            <div class="alert alert-success mb-4" id="modal-valor-parcela-div" style="display: none;">
              <i class="fas fa-info-circle me-2"></i>
              <span id="modal-valor-parcela-text"></span>
            </div>

            <div class="mb-4">
              <label class="form-label text-success fw-bold">Observações Adicionais</label>
              <textarea class="form-control border-success"
                name="observacoes" id="modal-observacoes"
                rows="3"></textarea>
            </div>

            <?php if ($tipoUsuario === 'pf' && !$bloqueioPF): ?>
              <button type="submit" name="reservar_quarto" class="btn btn-success btn-lg w-100 py-3">
                <i class="fas fa-check-circle me-2"></i>Confirmar Reserva
              </button>
            <?php elseif ($tipoUsuario === 'pj'): ?>
              <div class="alert alert-info mt-4">
                Selecione os quartos desejados e clique em "Finalizar Reserva"
              </div>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal de Reserva de Grupo de Quartos -->
  <div class="modal fade" id="reservaModalGrupo" tabindex="-1" aria-labelledby="reservaModalGrupoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h3 class="modal-title fw-bold mb-0">
            <i class="fas fa-calendar-check me-2"></i>Reserva de Grupo de Quartos
          </h3>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <div class="modal-body p-4">
          <div class="row g-4 mb-4">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                  <thead class="table-success">
                    <tr>
                      <th scope="col">Quarto</th>
                      <th scope="col">Capacidade</th>
                      <th scope="col">Valor (R$/noite)</th>
                    </tr>
                  </thead>
                  <tbody id="quartos-selecionados-table">
                    <!-- Preenchido via JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label text-success fw-bold">Data de Check-in</label>
              <input type="date" class="form-control border-success"
                name="check_in_grupo" id="check-in-grupo" required
                min="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label text-success fw-bold">Data de Check-out</label>
              <input type="date" class="form-control border-success"
                name="check_out_grupo" id="check-out-grupo" required
                min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label text-success fw-bold">Valor Total</label>
              <div class="input-group">
                <span class="input-group-text bg-success text-white">R$</span>
                <input type="text" class="form-control fw-bold border-success"
                  id="valor-total-grupo" disabled>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label text-success fw-bold">Forma de Pagamento</label>
              <select class="form-select border-success" name="pagamento_grupo" id="pagamento-grupo">
                <option value="dinheiro">Dinheiro</option>
                <option value="pix">PIX</option>
                <option value="cartao">Cartão de Crédito</option>
                <option value="debito">Cartão de Débito</option>
              </select>
            </div>

            <div class="col-md-4">
              <div id="modal-parcelas-div" style="display: none;">
                <label class="form-label text-success fw-bold">Parcelas</label>
                <input type="number" class="form-control fw-bold border-success"
                  name="parcelas" id="modal-parcelas" min="1" max="12" value="1">
              </div>
            </div>
          </div>

          <div class="alert alert-success mb-4" id="valor-parcela-grupo-div" style="display: none;">
            <i class="fas fa-info-circle me-2"></i>
            <span id="valor-parcela-grupo-text"></span>
          </div>

          <div class="mb-4">
            <label class="form-label text-success fw-bold">Observações Adicionais</label>
            <textarea class="form-control border-success"
              name="observacoes_grupo" id="observacoes-grupo"
              rows="3"></textarea>
          </div>

          <button type="button" class="btn btn-success btn-lg w-100 py-3" id="confirmar-reserva-grupo">
            <i class="fas fa-check-circle me-2"></i>Confirmar Reserva
          </button>
        </div>
      </div>
    </div>
  </div>

  <?php include("../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function habilitarCheckout() {
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');
    
    console.log('Função habilitarCheckout chamada'); // Debug
    
    if (checkin.value) {
        console.log('Check-in selecionado:', checkin.value); // Debug
        // Habilita o campo checkout
        checkout.disabled = false;
        
        // Calcula a data mínima (dia seguinte)
        const minDate = new Date(checkin.value);
        minDate.setDate(minDate.getDate() + 1);
        
        // Calcula a data máxima (1 mês depois)
        const maxDate = new Date(minDate);
        maxDate.setMonth(maxDate.getMonth() + 1);
        
        // Formata as datas
        const minStr = minDate.toISOString().split('T')[0];
        const maxStr = maxDate.toISOString().split('T')[0];
        
        // Aplica as restrições
        checkout.min = minStr;
        checkout.max = maxStr;
        
        console.log('Período permitido para check-out:', minStr, 'até', maxStr); // Debug
        
        // Se não tiver valor ou for inválido, define o dia seguinte
        if (!checkout.value || new Date(checkout.value) < minDate) {
            checkout.value = minStr;
        }
    } else {
        console.log('Check-in não selecionado - desabilitando check-out'); // Debug
        // Desabilita se não tiver checkin
        checkout.disabled = true;
        checkout.value = '';
    }
}
    $(document).ready(function() {
            if (document.getElementById('checkin').value) {
              console.log('Inicializando check-out na carga da página'); // Debug
              habilitarCheckout();
          }
          // Máscara para o campo de preço máximo (formata enquanto digita)
          function formatarPreco(input) {
            // Remove todos os caracteres não numéricos, exceto vírgula
            let valor = input.value.replace(/[^\d,]/g, '');

            // Remove vírgulas extras, mantendo apenas a última
            const partes = valor.split(',');
            if (partes.length > 2) {
              valor = partes[0] + ',' + partes.slice(1).join('');
            }

            // Separa parte inteira e decimal
            let [inteira, decimal] = valor.split(',');

            // Formata a parte inteira com separadores de milhar (corrigido)
            if (inteira) {
              inteira = inteira.replace(/\D/g, '');
              inteira = inteira.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Limita a parte decimal a 2 dígitos
            if (decimal) {
              decimal = decimal.replace(/\D/g, '');
              decimal = decimal.substring(0, 2);
            }

            // Recompõe o valor formatado
            let valorFormatado = inteira;
            if (decimal !== undefined) {
              valorFormatado += ',' + decimal;
            }

            // Atualiza o campo com o valor formatado
            input.value = valorFormatado;
          }

          // Função para converter o valor formatado para float antes de enviar
          function prepararPrecoParaEnvio() {
            const precoInput = document.getElementById('preco_max');
            if (precoInput.value) {
              precoInput.value = precoInput.value.replace(/\./g, '').replace(',', '.');
            }
          }

          // Adiciona o event listener para o submit do formulário
          document.querySelector('form').addEventListener('submit', prepararPrecoParaEnvio);

          // Formata o valor inicial se existir
          document.getElementById('preco_max').addEventListener('focus', function() {
            if (!this.value || this.value === '0,00') {
              this.value = '';
            }
          });

          // Completa com ",00" quando o usuário sai do campo sem digitar decimais
          document.getElementById('preco_max').addEventListener('blur', function() {
            if (this.value && !this.value.includes(',')) {
              this.value += ',00';
            } else if (this.value.includes(',')) {
              const partes = this.value.split(',');
              if (partes[1].length === 1) {
                this.value = partes[0] + ',' + partes[1] + '0';
              }
            }
          });

          // Inicializa a máscara quando a página carrega
          window.addEventListener('DOMContentLoaded', function() {
            const precoInput = document.getElementById('preco_max');
            if (precoInput.value) {
              // Converte de float para formato brasileiro
              const valor = parseFloat(precoInput.value.replace(',', '.')).toFixed(2);
              precoInput.value = valor.replace('.', ',');
              formatarPreco({
                target: precoInput
              });
            }
          });

          $('#preco_max').on('blur', function() {
            if (!this.value) {
              this.value = '0,00';
            } else if (!this.value.includes(',')) {
              this.value += ',00';
            }
          });
          // Modal de reserva individual
          const reservaModal = document.getElementById('reservaModal');
          if (reservaModal) {
            reservaModal.addEventListener('show.bs.modal', function(event) {
              const button = event.relatedTarget;
              const quartoId = button.getAttribute('data-quarto-id');
              const quartoNumero = button.getAttribute('data-quarto-numero');
              const quartoPreco = parseFloat(button.getAttribute('data-quarto-preco'));
              const quartoImg = button.getAttribute('data-quarto-img');
              const quartoDescricao = button.getAttribute('data-quarto-descricao');
              const quartoCapacidade = button.getAttribute('data-quarto-capacidade');
              const quartoRegras = JSON.parse(button.getAttribute('data-quarto-regras')) || [];

              // Atualizar os elementos do modal
              document.getElementById('modal_quarto_id').value = quartoId;
              document.getElementById('modal-img').src = quartoImg;
              document.getElementById('modal-img').alt = `Quarto ${quartoNumero}`;
              document.getElementById('modal-quarto-numero').textContent = quartoNumero;
              document.getElementById('modal-quarto-preco').textContent = quartoPreco.toLocaleString('pt-BR', {
                minimumFractionDigits: 2
              });
              document.getElementById('modal-quarto-descricao').textContent = quartoDescricao;
              document.getElementById('modal-quarto-capacidade').textContent = `Capacidade: ${quartoCapacidade} pessoas`;
              document.getElementById('modal-valor-total').value = quartoPreco.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
              });

              // Preencher regras do quarto
              const regrasList = document.getElementById('modal-quarto-regras');
              regrasList.innerHTML = '';

              if (quartoRegras.length > 0) {
                quartoRegras.forEach(regra => {
                  const li = document.createElement('li');
                  li.className = 'd-flex align-items-center mb-2';
                  li.innerHTML = `
                <i class="fas fa-check-circle text-success me-2"></i>
                ${regra}
            `;
                  regrasList.appendChild(li);
                });
              } else {
                const li = document.createElement('li');
                li.className = 'text-muted';
                li.textContent = 'Nenhuma regra específica';
                regrasList.appendChild(li);
              }

              // Resetar campos
              document.getElementById('modal-check-in').value = '';
              document.getElementById('modal-check-out').value = '';
              document.getElementById('modal-observacoes').value = '';
            });
          }

          // Atualizar valor total quando as datas mudam (modal individual)
          $('#modal-check-in, #modal-check-out').change(function() {
            const checkIn = new Date($('#modal-check-in').val());
            const checkOut = new Date($('#modal-check-out').val());

            if (checkIn && checkOut && checkOut > checkIn) {
              const diffTime = Math.abs(checkOut - checkIn);
              const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
              const precoNoite = parseFloat($('#modal-quarto-preco').text().replace(/[^\d,]/g, '').replace(',', '.'));
              const valorTotal = diffDays * precoNoite;

              // Aplicar desconto se for PIX
              const pagamento = $('#modal-pagamento').val();
              const valorComDesconto = pagamento === 'pix' ? valorTotal * 0.95 : valorTotal;

              $('#modal-valor-total').val(valorComDesconto.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
              }));
              calcularParcelas();
            }
          });

          // Atualizar valor total quando o método de pagamento muda (modal individual)
          $('#modal-pagamento').change(function() {
            const checkIn = new Date($('#modal-check-in').val());
            const checkOut = new Date($('#modal-check-out').val());

            if (checkIn && checkOut && checkOut > checkIn) {
              const diffTime = Math.abs(checkOut - checkIn);
              const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
              const precoNoite = parseFloat($('#modal-quarto-preco').text().replace(/[^\d,]/g, '').replace(',', '.'));
              let valorTotal = diffDays * precoNoite;

              // Aplicar desconto se for PIX
              const pagamento = $(this).val();
              valorTotal = pagamento === 'pix' ? valorTotal * 0.95 : valorTotal;

              $('#modal-valor-total').val(valorTotal.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
              }));

              // Mostrar/ocultar parcelas
              if (pagamento === 'cartao') {
                $('#modal-parcelas-div').show();
                calcularParcelas();
              } else {
                $('#modal-parcelas-div').hide();
                $('#modal-valor-parcela-div').hide();
              }
            }
          });

          // Calcular parcelas (modal individual)
          function calcularParcelas() {
            const valorTotalText = document.getElementById('modal-valor-total').value;
            const valorTotal = parseFloat(valorTotalText.replace(/[^\d,]/g, '').replace(',', '.'));
            let parcelas = parseInt(document.getElementById('modal-parcelas').value) || 1;

            // Limitar a 12 parcelas
            if (parcelas > 12) {
              parcelas = 12;
              document.getElementById('modal-parcelas').value = 12;
            }

            if (!isNaN(valorTotal)) {
                const valorParcela = valorTotal / parcelas;
                document.getElementById('modal-valor-parcela-text').textContent =
                  `${valorParcela.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })} em ${parcelas}x (máx. 12)`;
                document.getElementById('modal-valor-parcela-div').style.display = 'block';
              } else {
                document.getElementById('modal-valor-parcela-div').style.display = 'none';
              }
            }

            document.getElementById('modal-pagamento').addEventListener('change', function() {
              const parcelasDiv = document.getElementById('modal-parcelas-div');
              const valorParcelaDiv = document.getElementById('modal-valor-parcela-div');

              if (this.value === 'cartao') {
                parcelasDiv.style.display = 'block';
                calcularParcelas(); // Chama a função para calcular e mostrar o valor das parcelas
              } else {
                parcelasDiv.style.display = 'none';
                valorParcelaDiv.style.display = 'none';
              }

              // Atualiza o valor total considerando possível desconto PIX
              atualizarValorTotal();
            });

            // Função para atualizar o valor total
            function atualizarValorTotal() {
              const checkIn = document.getElementById('modal-check-in').value;
              const checkOut = document.getElementById('modal-check-out').value;
              const precoNoite = parseFloat(document.getElementById('modal-quarto-preco').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
              const pagamento = document.getElementById('modal-pagamento').value;

              if (checkIn && checkOut) {
                const diffTime = Math.abs(new Date(checkOut) - new Date(checkIn));
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                let valorTotal = diffDays * precoNoite;

                // Aplicar desconto se for PIX
                if (pagamento === 'pix') {
                  valorTotal *= 0.95; // 5% de desconto
                }

                document.getElementById('modal-valor-total').value = valorTotal.toLocaleString('pt-BR', {
                  style: 'currency',
                  currency: 'BRL'
                });

                // Se for cartão, recalcular parcelas
                if (pagamento === 'cartao') {
                  calcularParcelas();
                }
              }
            }

            // Para o modal individual
            document.getElementById('modal-parcelas').addEventListener('input', function() {
              if (this.value > 12) {
                this.value = 12;
              }
              calcularParcelas();
            });

            // Para o modal de grupo (dentro do $(document).ready())
            $('#parcelas-grupo').on('input', function() {
              if (this.value > 12) {
                this.value = 12;
              }
              calcularParcelasGrupo();
            });

            document.getElementById('modal-parcelas').addEventListener('input', calcularParcelas);

            document.addEventListener('DOMContentLoaded', function() {
              // Inicialmente esconde o campo de parcelas
              document.getElementById('modal-parcelas-div').style.display = 'none';
              document.getElementById('modal-valor-parcela-div').style.display = 'none';

              // ... resto do seu código de inicialização ...
            });

            $('#modal-parcelas').on('input', calcularParcelas);

            // Atualizar data mínima do checkout quando checkin é alterado (modal individual)
            $('#modal-check-in').change(function() {
              if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                $('#modal-check-out').attr('min', nextDay.toISOString().split('T')[0]);

                // Se o checkout atual é anterior ao novo checkin+1, limpar
                const checkout = $('#modal-check-out');
                if (checkout.val() && new Date(checkout.val()) < nextDay) {
                  checkout.val('');
                }
              }
            });

            // Formatação do preço máximo
            $('#preco_max').on('blur', function() {
              let value = this.value.replace(/\D/g, '');
              value = value ? (parseInt(value) / 100).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
              }) : '';
              this.value = value;
            });

            // Mostrar loading durante a busca
            $('form').on('submit', function() {
              const submitBtn = $(this).find('button[type="submit"]');
              submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Filtrando...');
              submitBtn.prop('disabled', true);
            });

            // Atualizar botão de finalizar reserva para PJ
            $('.quarto-checkbox').change(function() {
              const algumSelecionado = $('.quarto-checkbox:checked').length > 0;
              $('#finalizarReservaBtn').toggle(algumSelecionado);
            });

            // Modal de reserva em grupo
            const reservaModalGrupo = document.getElementById('reservaModalGrupo');
            if (reservaModalGrupo) {
              reservaModalGrupo.addEventListener('show.bs.modal', function() {
                const quartosSelecionados = [];
                $('.quarto-checkbox:checked').each(function() {
                  const quartoId = this.id.replace('quarto', '');
                  const card = $(this).closest('.card');
                  const numero = card.find('.fw-semibold').text().split('#')[1].trim();
                  const precoText = card.find('.badge.bg-success-subtle').text();
                  const preco = parseFloat(precoText.match(/R\$\s*([\d.,]+)/)[1].replace('.', '').replace(',', '.'));

                  quartosSelecionados.push({
                    id: quartoId,
                    numero: numero,
                    preco: preco
                  });
                });

                // Preencher tabela de quartos selecionados
                const tbody = $('#quartos-selecionados-table');
                tbody.empty();
                let valorTotal = 0;

                quartosSelecionados.forEach(quarto => {
                  tbody.append(`
                            <tr>
                                <td>${quarto.numero}</td>
                                <td>2 pessoas</td>
                                <td>${quarto.preco.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `);
                  valorTotal += quarto.preco;
                });

                // Atualizar valor total
                $('#valor-total-grupo').val(valorTotal.toLocaleString('pt-BR', {
                  style: 'currency',
                  currency: 'BRL'
                }));

                // Resetar campos
                $('#check-in-grupo').val('');
                $('#check-out-grupo').val('');
                $('#observacoes-grupo').val('');
              });
            }

            // Atualizar valor total quando as datas mudam (modal grupo)
            $('#check-in-grupo, #check-out-grupo').change(function() {
              const checkIn = new Date($('#check-in-grupo').val());
              const checkOut = new Date($('#check-out-grupo').val());

              if (checkIn && checkOut && checkOut > checkIn) {
                const diffTime = Math.abs(checkOut - checkIn);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const valorDiaria = parseFloat($('#valor-total-grupo').val().replace(/[^\d,]/g, '').replace(',', '.'));
                const valorTotal = diffDays * valorDiaria;

                // Aplicar desconto se for PIX
                const pagamento = $('#pagamento-grupo').val();
                const valorComDesconto = pagamento === 'pix' ? valorTotal * 0.95 : valorTotal;

                $('#valor-total-grupo').val(valorComDesconto.toLocaleString('pt-BR', {
                  style: 'currency',
                  currency: 'BRL'
                }));
                calcularParcelasGrupo();
              }
            });

            // Atualizar valor total quando o método de pagamento muda (modal grupo)
            $('#pagamento-grupo').change(function() {
              const checkIn = new Date($('#check-in-grupo').val());
              const checkOut = new Date($('#check-out-grupo').val());

              if (checkIn && checkOut && checkOut > checkIn) {
                const diffTime = Math.abs(checkOut - checkIn);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const valorDiaria = parseFloat($('#valor-total-grupo').val().replace(/[^\d,]/g, '').replace(',', '.'));
                let valorTotal = diffDays * valorDiaria;

                // Aplicar desconto se for PIX
                const pagamento = $(this).val();
                valorTotal = pagamento === 'pix' ? valorTotal * 0.95 : valorTotal;

                $('#valor-total-grupo').val(valorTotal.toLocaleString('pt-BR', {
                  style: 'currency',
                  currency: 'BRL'
                }));

                // Mostrar/ocultar parcelas
                if (pagamento === 'cartao') {
                  $('#parcelas-grupo-div').show();
                  calcularParcelasGrupo();
                } else {
                  $('#parcelas-grupo-div').hide();
                  $('#valor-parcela-grupo-div').hide();
                }
              }
            });

            // Calcular parcelas (modal grupo)
            function calcularParcelasGrupo() {
              const valorTotalText = $('#valor-total-grupo').val();
              const valorTotal = parseFloat(valorTotalText.replace(/[^\d,]/g, '').replace(',', '.'));
              let parcelas = parseInt($('#parcelas-grupo').val()) || 1;

              // Limitar a 12 parcelas
              if (parcelas > 12) {
                parcelas = 12;
                $('#parcelas-grupo').val(12);
              }

              if (!isNaN(valorTotal)) {
                if ($('#pagamento-grupo').val() === 'cartao' && parcelas > 0) {
                  const valorParcela = valorTotal / parcelas;
                  $('#valor-parcela-grupo-text').text(
                    `${valorParcela.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'})} em ${parcelas}x (máx. 12)`
                  );
                  $('#valor-parcela-grupo-div').show();
                } else {
                  $('#valor-parcela-grupo-div').hide();
                }
              }
            }

            $('#parcelas-grupo').on('input', calcularParcelasGrupo);

            // Confirmar reserva em grupo
            $('#confirmar-reserva-grupo').click(function() {
              const quartosSelecionados = [];
              $('.quarto-checkbox:checked').each(function() {
                quartosSelecionados.push(this.id.replace('quarto', ''));
              });

              const checkIn = $('#check-in-grupo').val();
              const checkOut = $('#check-out-grupo').val();
              const pagamento = $('#pagamento-grupo').val();
              const observacoes = $('#observacoes-grupo').val();

              if (!checkIn || !checkOut) {
                alert('Por favor, selecione as datas de check-in e check-out');
                return;
              }

              if (new Date(checkOut) <= new Date(checkIn)) {
                alert('A data de check-out deve ser posterior ao check-in');
                return;
              }

              // Enviar dados via AJAX
              $.ajax({
                url: 'processar_reserva_grupo.php',
                method: 'POST',
                data: {
                  quartos: quartosSelecionados,
                  check_in: checkIn,
                  check_out: checkOut,
                  pagamento: pagamento,
                  observacoes: observacoes,
                  usuario_id: <?= $usuarioId ?>
                },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    alert('Reserva em grupo confirmada com sucesso!');
                    location.reload();
                  } else {
                    alert('Erro: ' + response.message);
                  }
                },
                error: function() {
                  alert('Erro ao processar a reserva. Tente novamente.');
                }
              });
            });
          });
  </script>
</body>

</html>
