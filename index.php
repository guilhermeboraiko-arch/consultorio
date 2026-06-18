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
    $sql = $sql . " WHERE p.nome LIKE '%$busca%'";
}

$executar = $conexao->query($sql);
$listaConsultas = $executar->fetchAll(PDO::FETCH_ASSOC);

$sqlContador = "SELECT id_consulta FROM consultas";
$executarContador = $conexao->query($sqlContador);

$totalConsultas = 0;

while ($linha = $executarContador->fetch(PDO::FETCH_ASSOC)) {
    $totalConsultas = $totalConsultas + 1;
}
?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex">
            <input class="form-control me-2" type="text" name="busca" placeholder="Buscar paciente pelo nome..." value="<?php echo $busca; ?>">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </form>
    </div>
</div>

<div class="alert alert-info mb-4">
    Atualmente existem <?php echo $totalConsultas; ?> consultas no sistema.
</div>

<h2>Agenda de Consultas</h2>

<div class="row">
    <?php if (empty($listaConsultas)) { ?>
        <div class="alert alert-warning">Nenhuma consulta encontrada.</div>
    <?php } else { ?>
        
        <?php foreach ($listaConsultas as $consulta) { ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"> 👤 Paciente: <?php echo $consulta['paciente_nome']; ?></h5>
                        <p class="card-text">Dentista: <?php echo $consulta['dentista_nome']; ?></p>
                        <p class="card-text">Procedimento: <?php echo $consulta['nome_procedimento']; ?></p>
                        <p class="card-text">Data: <?php echo $consulta['data_consulta']; ?> às <?php echo $consulta['hora_consulta']; ?></p>
                        
                        <div class="mt-3">
                            Valor Original: R$ <?php echo $consulta['valor_base']; ?>
                            <br>
                            <strong>Valor com Desconto: R$ <span class="preco-final"><?php echo $consulta['valor_base']; ?></span></strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<script src=".js"></script>

</div> 
</body>
</html>