<?php
require_once "config.php";
include_once "cabecalho.php";

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$sql = "SELECT c.id_consulta, p.nome AS paciente_nome, d.nome AS dentista_nome, 
               pr.nome_procedimento, c.data_consulta, c.hora_consulta, pr.valor_base
        FROM consultas c
        JOIN pacientes p ON c.id_paciente = p.id_paciente
        JOIN dentistas d ON c.id_dentista = d.id_dentista
        JOIN procedimentos pr ON c.id_procedimento = pr.id_procedimento";

if ($busca != '') {
    $sql = $sql . " WHERE p.nome LIKE '%$busca%' OR d.nome LIKE '%$busca%'";
}

$executar = $conexao->query($sql);
$listaConsultas = $executar->fetchAll(PDO::FETCH_ASSOC);

$sqlContador = "SELECT id_consulta FROM consultas";
$executarContador = $conexao->query($sqlContador);

$totalConsultas = 0;

while ($linha = $executarContador->fetch(PDO::FETCH_ASSOC)) {
    $totalConsultas = $totalConsultas + 1;
}

if (isset($_GET['id_excluir'])) {
    $id_para_excluir = $_GET['id_excluir'];
    $sqlDeletar = "DELETE FROM consultas WHERE id_consulta = '$id_para_excluir'";

    if ($conexao->query($sqlDeletar)) {
        echo "<script>window.location.href='index.php';</script>";
        exit();
    }
}

?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex">
            <input class="form-control me-2" type="text" name="busca" placeholder="Buscar paciente pelo nome..." value="<?php echo htmlspecialchars($busca); ?>">
            <button class="btn btn-primary me-2" type="submit">Pesquisar</button>
            <a href="index.php" class="btn btn-secondary">Limpar</a>
        </form>
    </div>
</div>

<div class="alert alert-info mb-4">
    Atualmente existem <?php echo $totalConsultas; ?> consultas no sistema.
</div>

<a href="agendar.php" class="btn btn-success mb-3">+ Agendar Nova Consulta</a>
<h2>Agenda de Consultas</h2>

<div class="row">
    <?php if (empty($listaConsultas)) { ?>
        <div class="alert alert-warning">Nenhuma consulta encontrada.</div>
    <?php } else { ?>

        <?php foreach ($listaConsultas as $consulta) { ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"> 👤 Paciente: <?php echo $consulta['paciente_nome']; ?></h5>
                        <p class="card-text mb-1"><strong>Dentista:</strong> <?php echo $consulta['dentista_nome']; ?></p>
                        <p class="card-text mb-1"><strong>Procedimento:</strong> <?php echo $consulta['nome_procedimento']; ?></p>
                        <p class="card-text mb-3"><strong>Data:</strong> <?php echo $consulta['data_consulta']; ?> às <?php echo $consulta['hora_consulta']; ?></p>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">Valor Original: R$ <?php echo $consulta['valor_base']; ?></small>
                                <br>
                                <strong>Valor com Desconto: R$ <span class="preco-final"><?php echo $consulta['valor_base']; ?></span></strong>
                            </div>

                            <a href="index.php?id_excluir=<?php echo $consulta['id_consulta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">
                                ❌ Excluir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<script src="desconto.js"></script>

</div>
</body>

</html>