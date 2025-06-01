<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Cadastro de Trator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .checkbox-group label { display: flex; align-items: center; }
        input[type="submit"] { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; font-size: 16px; }
    </style>
</head>
<body>
    <h2>Cadastro de Trator</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="valor">Valor:</label>
        <input type="number" id="valor" name="valor" step="0.01" required>

        <label for="quantidade">Quantidade/Quilometragem:</label>
        <input type="number" id="quantidade" name="quantidade" required>

        <label for="cidadeTrator">Cidade:</label>
        <input type="text" id="cidadeTrator" name="cidadeTrator" required>

        <label for="estadoTrator">Estado:</label>
        <input type="text" id="estadoTrator" name="estadoTrator" required>

        <label>Características do trator:</label>
        <div class="checkbox-group">
            <?php
            $checkbox_options = [
                "Cabinado", "Direção Hidráulica", "Ar Condicionado", "Piloto Automático",
                "Comando Hidráulico", "GPS", "Peso Dianteiro", "Peso Traseiro",
                "Pneus Rodado Duplo", "Comando Duplo", "Pneus Filipados"
            ];
            foreach ($checkbox_options as $option) {
                echo "<label><input type='checkbox' name='nuvem[]' value='" . htmlspecialchars($option) . "'> " . htmlspecialchars($option) . "</label>";
            }
            ?>
        </div>

        <label for="imagem">Imagens:</label>
        <input type="file" id="imagem" name="imagens[]" multiple required>

        <label for="url_buy">URL de Compra:</label>
        <input type="url" id="url_buy" name="url_buy" required>

        <input type="submit" value="Cadastrar">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = htmlspecialchars($_POST['nome']);
        $valor = floatval($_POST['valor']);
        $quantidade = intval($_POST['quantidade']);
        $cidadeTrator = htmlspecialchars($_POST['cidadeTrator']);
        $estadoTrator = htmlspecialchars($_POST['estadoTrator']);
        $nuvem = isset($_POST['nuvem']) ? implode(", ", $_POST['nuvem']) : "";
        $url_buy = htmlspecialchars($_POST['url_buy']);

        // Diretório correto para upload das imagens
        $target_dir = "../../site3/cadastro_produto/up/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Inserir no banco de dados (Sem eq_user)
        $stmt = $conn->prepare("INSERT INTO cadastro_produto (nome, valor, quantidade, cidadeTrator, estadoTrator, nuvem, url_buy) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdissss", $nome, $valor, $quantidade, $cidadeTrator, $estadoTrator, $nuvem, $url_buy);

            if ($stmt->execute()) {
                $produto_id = $stmt->insert_id;

                // Upload de imagens
                foreach ($_FILES['imagens']['name'] as $key => $imagem) {
                    $target_file = $target_dir . basename($imagem);
                    if (move_uploaded_file($_FILES['imagens']['tmp_name'][$key], $target_file)) {
                        $stmt_img = $conn->prepare("INSERT INTO imagens_produto (produto_id, imagem) VALUES (?, ?)");
                        if ($stmt_img) {
                            $stmt_img->bind_param("is", $produto_id, $target_file);
                            $stmt_img->execute();
                        }
                    }
                }

                echo "<h3 style='color: green;'>Trator cadastrado com sucesso!</h3>";
            } else {
                echo "<h3 style='color: red;'>Erro ao cadastrar trator.</h3>";
            }
            $stmt->close();
        }
    }

    $conn->close();
    ?>
</body>
</html>
