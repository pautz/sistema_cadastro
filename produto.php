<!DOCTYPE html>
<html>
<head>
    <title>Produto</title>
   <style>
    body {
        background-color: rgba(240, 240, 240, 0.8); /* Fundo claro com transparência */
        font-family: Arial, sans-serif;
        margin: 20px;
        color: #333;
        text-align: center;
    }

    .product-details {
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        max-width: 800px;
        margin: auto;
        transition: transform 0.3s ease-in-out;
    }

    .product-details:hover {
        transform: scale(1.02);
    }

    h2 {
        text-align: center;
        color: #333;
    }

    .product-info {
        text-align: center;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        background-color: rgba(0, 123, 255, 0.8);
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .back-link a:hover {
        background-color: rgba(0, 86, 179, 0.8);
    }

    .buy-button {
        background-color: rgba(76, 175, 80, 0.8);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .buy-button:hover {
        background-color: rgba(69, 160, 73, 0.8);
        transform: scale(1.05);
    }

    .product-images {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .main-image {
        max-width: 500px;
        border: 3px solid rgba(204, 204, 204, 0.8);
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .main-image:hover {
        transform: scale(1.05);
    }

    .side-image {
        max-width: 250px;
        transition: transform 0.3s ease;
        border-radius: 5px;
    }

    .side-image:hover {
        transform: scale(1.2);
    }
</style>

</head>
<body>
    <h2>Detalhes do Produto</h2>

    <?php
    // Obter o ID do produto da URL
    $product_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // Conecte-se ao banco de dados
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "dbname";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Buscar detalhes do produto
    $sql = "SELECT p.nome, p.valor, p.quantidade, p.url_buy
            FROM cadastro_produto p
            WHERE p.id='$product_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<div class='product-details'>
                <div class='product-images'>";

        // Buscar e exibir todas as imagens do produto
        $sql_images = "SELECT imagem FROM imagens_produto WHERE produto_id='$product_id'";
        $result_images = $conn->query($sql_images);

        $is_first_image = true;
        if ($result_images->num_rows > 0) {
            while ($row_image = $result_images->fetch_assoc()) {
                if (!empty($row_image["imagem"])) {
                    if ($is_first_image) {
                        echo "<img class='main-image' src='" . $row_image["imagem"] . "' alt='" . $row["nome"] . "'>";
                        $is_first_image = false;
                    } else {
                        echo "<img class='side-image' src='" . $row_image["imagem"] . "' alt='" . $row["nome"] . "'>";
                    }
                }
            }
        }

        echo "</div>
                <div class='product-info'>
                    <h3>" . $row["nome"] . "</h3>
                    <p>Valor: R$" . number_format($row["valor"], 2, ',', '.') . "</p>
                    <p>Quantidade Disponível: " . htmlspecialchars($row["quantidade"]) . "</p>";

        // Adicionar mensagem automática ao link do WhatsApp
        $whatsapp_link = $row["url_buy"] . "&text=Olá, estou interessado no produto ID " . $product_id . ". Poderia me fornecer mais informações?";

        echo "<a href='" . htmlspecialchars($whatsapp_link) . "' class='buy-button'>📲 Contato via WhatsApp</a>
                </div>
              </div>";
    } else {
        echo "<div class='message'>Produto não encontrado.</div>";
    }

    $conn->close();
    ?>

    <div class="back-link">
        <a href="https://carlitoslocacoes.com/site2/nossasmaquinas/">Voltar à lista de produtos</a>
    </div>
</body>
</html>
