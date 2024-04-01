<?php
/**
 * Script para baixar arquivos SWF de mobis do Habbo usando IDs específicos.
 *
 * Este script recebe um ID de mobi via parâmetro na URL e baixa o arquivo SWF
 * correspondente da URL fornecida para o Habbo. O arquivo SWF é salvo na pasta "mobis".
 *
 * Exemplo de uso: http://localhost:8080/furnimobis.php?id=1621631559
 *
 * Direitos Reservados |  Desenvolvedor: Marcos (INDIO)
 */

// Define o caminho do arquivo furnidata
$furnidata = 'habbo/furnidata.xml';

// Carrega o arquivo XML
$xmlHotel = simplexml_load_file($furnidata);

// Define o URL base para os arquivos SWF dos mobis
$urlMobis = "https://habblize.com/swf/hof_furni/";

// Define o nome da pasta para salvar os arquivos SWF
$swfFolder = "mobis";

/**
 * Função para baixar o mobi pelo ID.
 *
 * @param string $id       O ID do mobi a ser baixado.
 * @param string $urlMobis O URL base dos arquivos SWF dos mobis.
 * @param string $swfFolder O nome da pasta onde os arquivos SWF serão salvos.
 * @return bool             Retorna true se o mobi foi baixado com sucesso, false caso contrário.
 */
function downloadMobiById($id, $urlMobis, $swfFolder) {
    global $xmlHotel;

    // Procura o mobi com o ID fornecido no XML
    foreach ($xmlHotel->roomitemtypes->furnitype as $furnitype) {
        if ((string)$furnitype["id"] === $id) {
            $classname = (string)$furnitype["classname"];
            $contentMobis = file_get_contents($urlMobis . $classname . '.swf');

            // Cria a pasta se não existir
            if (!is_dir($swfFolder)) {
                mkdir($swfFolder, 0777, true);
            }

            // Salva o arquivo SWF
            file_put_contents("$swfFolder/$classname.swf", $contentMobis);

            return true; // Mobi baixado com sucesso
        }
    }

    return false; // Mobi não encontrado
}

// Verifica se foi fornecido um ID na URL
$id = $_GET['id'] ?? '';

// Se o ID foi fornecido, tenta baixar o mobi correspondente
if (!empty($id)) {
    $success = downloadMobiById($id, $urlMobis, $swfFolder);

    if ($success) {
        echo json_encode([
            "response" => true,
            "message" => "Mobi with ID $id downloaded successfully."
        ]);
    } else {
        echo json_encode([
            "response" => false,
            "message" => "Mobi with ID $id not found."
        ]);
    }
} else {
    echo json_encode([
        "response" => false,
        "message" => "No ID specified."
    ]);
}

header("Content-Type: application/json; charset=UTF-8");
?>
