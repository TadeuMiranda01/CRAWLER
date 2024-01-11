<?php

require 'vendor/autoload.php';
use HeadlessChromium\BrowserFactory;
use League\Csv\Writer;

function data($link,$leilao, $data, $valor) {
    // Declarar um array de leilões
    static $leiloes = [];

    // Adicionar o leilão ao array
    $leiloes[] = [
        "link" =>$link,
        "leilao" => $leilao,
        "data" => $data,
        "valor" => $valor,
    ];

    // Retornar o array de leilões
    return $leiloes;
}

$browserFactory = new BrowserFactory();

// Inicia o Chrome sem interface gráfica
$browser = $browserFactory->createBrowser();

try {

    $links = [
        'https://amleiloeiro.com.br/lote/21295/casa',
        'https://amleiloeiro.com.br/lote/21402/immc-asx-20-20122013'
    ];

    foreach ($links as $link) {
        // Cria uma nova página e navega para uma URL
        $page = $browser->createPage();
        $page->navigate($link)->waitForNavigation();

        // Executa o script JavaScript na página
        $evaluation = $page->evaluate("
            const linhas = document.querySelectorAll('table tr');
            const textosArray = []; // Array para armazenar os textos
            
            linhas.forEach((linha) => {
                const ultimaCelula = linha.querySelector('td.py-3.px-4');
            
                if (ultimaCelula) {
                    // Obtém e armazena o conteúdo da última célula no array
                    textosArray.push(ultimaCelula.textContent.trim());
                }
            });
            
            textosArray; // Retorna o array com os textos das células desejadas
        ");

        // Obtém o resultado da execução do script (o array de textos)
        $textosArray = $evaluation->getReturnValue();
        var_dump($link);
        $string = $textosArray[3];
        // Separar a string em um array de leilões
        $leiloes = explode(" ", $string);
        
        $string7 = explode("2º", $leiloes[7]);
    
        $leilao1= data($link,$leiloes[0],$leiloes[3],$string7[0]);
    
        $leilao2= data($link,'2º',$leiloes[10],$leiloes[14]);
    
        $leiloes = $leilao2;
        var_dump($leiloes);
    
        $csvFilePath = 'resultados.csv';

        $csvWriter = Writer::createFromPath($csvFilePath, 'w+');
    
        // Adiciona os cabeçalhos ao arquivo CSV
        $csvWriter->insertOne(['Link','Leilão', 'Datas', 'Preço']);
        
        // Adiciona os dados da tabela ao arquivo CSV
        foreach ($leiloes as $leilao) {
            $csvWriter->insertOne([$link ,$leilao['leilao'], $leilao['data'], $leilao['valor']]);
        }

    }


} finally {
    // Fecha o navegador
    $browser->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crawler</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css"/>
  <script type="text/javascript" src="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
  
  <table id="minhaTabela">
    <thead>
      <tr>
        <th>Link</th>
        <th>Leilão</th>
        <th>Datas</th>
        <th>Preço</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($leiloes as $leilao): ?>
        <tr>
            <td><?php echo $leilao['link']; ?></td>
            <td><?php echo $leilao['leilao']; ?></td>
            <td><?php echo $leilao['data']; ?></td>
            <td><?php echo $leilao['valor']; ?></td>
        </tr>
    <?php endforeach; ?>     
    </tbody>
  </table>

<script>
$('#minhaTabela').DataTable( {
} );
  </script>
  
</body>
</html>