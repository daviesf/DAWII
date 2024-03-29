<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CRUD - Controle de alunos</title>

    <style>
        #sucess {
            color: green;
            font-weight: bold;
        }

        #error {
            color: red;
            font-weight: bold;
        }

        #warning {
            color: orange;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <a href="index.html">Home</a>
    <hr>

    <h2>Cadastro de Alunos</h2>
    <div>
        <form method="post">

            RA:<br>
            <input type="text" size="10" name="ra"><br><br>

            Nome:<br>
            <input type="text" size="30" name="nome"><br><br>

            Curso:<br>
            <select name="curso">
                <option></option>
                <option value="Edificações">Edificações</option>
                <option value="Enfermagem">Enfermagem</option>
                <option value="GeoCart">Geodésia e Cartografia</option>
                <option value="Informática">Informática</option>
                <option value="Mecânica">Mecânica</option>
                <option value="Qualidade">Qualidade</option>
            </select><br><br>

            <input type="submit" value="Cadastrar">

            <hr>

        </form>
    </div>

</body>

</html>

<?php
define('TAMANHO_MAXIMO', (2 *1024 * 1024));

try {
    if ($SERVER["REQUEST_METHOD"] == "POST") {
        $ra = $_POST["ra"];
        $nome = $_POST["nome"];
        $curso = $_POST["curso"];

        $uploaddir = 'upload/fotos/'; // diretório onde será gravada a imagem

        $foto = $_FILES['foto'];
        $nomeFoto = $foto['name'];
        $tipoFoto = $foto['type'];
        $tamanhoFoto = $foto['size'];

        $info = new SplFileInfo($nomeFoto);
        $extensaoArq = $info->getExtension();
        $novoNomeFoto = $ra . "." . $extensaoArq;

        if (empty(trim($ra)) || empty(trim($nome)) || empty(trim($curso))) {
            echo "<p id='warning'>Preencha todos os campos!</p>";
        } else if ( ($nomeFoto != "") && (!preg_match('/^image\/(jpeg|png|gif)$/', $tipoFoto))) {

        } else {

            include_once("conexao.php");

            $stmt = $pdo->prepare("SELECT * FROM alunos WHERE ra = :ra");
            $stmt->bindParam(':ra', $ra);
            $stmt->execute();

            $rows = $stmt->rowCount();

            if ($rows <= 0) {

                if (($nomeFoto != "") && (move_uploaded_file($_FILES['foto']['tmp_name'], $uploaddir . $novoNomeFoto))) {
                    $uploadfile = $uploaddir . $novoNomeFoto;
                } else {
                    $uploadfile = null;
                    echo "Sem upload de imagem.";
                }
                $stmt = $pdo->prepare("INSERT INTO alunos (ra, nome, curso, arquivoFoto) VALUES (:ra, :nome, :curso, :arquivoFoto)");
                $stmt->bindParam(':ra', $ra);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':curso', $curso);
                $stmt->bindParam(':arquivoFoto', $uploadfile);
                $stmt->execute();

                echo "<p id='sucess'>Aluno cadastrado com sucesso!</p>";
            } else {
                echo "<p id='error'>Aluno já cadastrado!</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$pdo = null;

?>