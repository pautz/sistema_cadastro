<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definir a quantidade de produtos por página
$limite = 9; // Mantém múltiplos de 3 colunas
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// Pesquisa personalizada (Apenas busca por ID)
$searchQuery = "WHERE 1=1"; 
if (!empty($_GET['search_id'])) {
    $search_id = $_GET['search_id'];
    $searchQuery .= " AND p.id='$search_id'";
}

// Obtenção dos produtos com paginação
$sql = "SELECT p.id, p.nome, p.valor, p.quantidade, 
               (SELECT imagem FROM imagens_produto WHERE produto_id = p.id LIMIT 1) AS imagem, 
               p.leilao, p.nuvem, p.cidadeTrator, p.estadoTrator, p.destacar
        FROM cadastro_produto p
        $searchQuery
        GROUP BY p.id
        ORDER BY p.destacar DESC, p.id DESC
        LIMIT $limite OFFSET $offset";

$result = $conn->query($sql);

// Contar o total de produtos para calcular páginas
$sqlTotal = "SELECT COUNT(*) AS total FROM cadastro_produto p $searchQuery";
$resultTotal = $conn->query($sqlTotal);
$totalProdutos = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalProdutos / $limite);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Produtos</title>
   <style>
    /* Estilos mantidos */
    body {
        background-color: rgba(240, 240, 240, 0.8);
        font-family: Arial, sans-serif;
        color: #333;
        text-align: center;
    }

    .form-container {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
        align-items: flex-end;
        justify-content: center;
        margin-bottom: 20px;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .search-btn {
        padding: 8px 16px;
        font-size: 16px;
        background-color: rgba(100, 200, 100, 0.8);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 40px;
        margin-left: 15px;
    }

    .search-btn:hover {
        background-color: rgba(80, 180, 80, 0.8);
        transform: scale(1.05);
    }

    .product-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .product-card {
        width: calc(33.33% - 20px);
        max-width: 300px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(200, 200, 255, 0.7), rgba(220, 220, 255, 0.7));
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    .product-img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .btn {
        background-color: rgba(255, 152, 0, 0.8);
        color: white;
        padding: 12px 24px;
        font-size: 18px;
        text-decoration: none;
        border-radius: 8px;
        text-align: center;
        display: inline-block;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background-color: rgba(230, 126, 34, 0.8);
        transform: scale(1.05);
    }
</style>
</head>
<body>
    <h2>Produtos</h2>
    <center><a href="../" class="btn">🏠 Início</a></center>
    <br>

    <!-- Formulário de Pesquisa (Apenas por ID) -->
    <form method="get" action="" class="form-container">
        <div class="form-group">
            <label for="search_id">Pesquisar por ID:</label>
            <input type="number" id="search_id" name="search_id">
        </div>
        <div class="form-group">
            <input type="submit" value="Pesquisar" class="search-btn">
        </div>
    </form>

    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<img src='" . htmlspecialchars($row["imagem"]) . "' class='product-img'>";
                echo "<h3>" . htmlspecialchars($row["nome"]) . "</h3>";
                echo "<p><strong>ID:</strong> " . htmlspecialchars($row["id"]) . "</p>";
                echo "<p><strong>Valor:</strong> R$ " . number_format($row["valor"], 2, ',', '.') . "</p>";
                echo "<p><strong>Horas:</strong> " . htmlspecialchars($row["quantidade"]) . "</p>";
                echo "<p><strong>Cidade:</strong> " . htmlspecialchars($row["cidadeTrator"]) . "</p>";
                echo "<p><strong>Estado:</strong> " . htmlspecialchars($row["estadoTrator"]) . "</p>";
                echo "<p><a href='/produto.php?id=" . $row["id"] . "' class='btn'>🔎 Visualizar</a></p>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhum produto encontrado.</p>";
        }
        ?>
    </div>

    <!-- Paginação -->
    <div class='pagination'>
        <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>
            <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>
    </div>
</body>
</html>
