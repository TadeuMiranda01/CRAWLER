<?php

function montarData( $array, $ajustado){

$data = ['leilao'=> $array[0], 'data'=>$array[3], 'valor'=>$ajustado[0]];

return $data;
}

function data($leilao, $data, $valor) {
    // Declarar um array de leilões
    static $leiloes = [];

    // Adicionar o leilão ao array
    $leiloes[] = [
        "leilao" => $leilao,
        "data" => $data,
        "valor" => $valor,
    ];

    // Retornar o array de leilões
    return $leiloes;
}

// require 'vendor/autoload.php';
// use HeadlessChromium\BrowserFactory;

// $browserFactory = new BrowserFactory();

// // starts headless Chrome
// $browser = $browserFactory->createBrowser();

// try {
//     // creates a new page and navigate to an URL
//     $page = $browser->createPage();
//     $page->navigate('https://amleiloeiro.com.br/lote/21295/casa')->waitForNavigation();

//     // get page title
//     $pageTitle = $page->evaluate('const linhas = document.querySelectorAll("table tr");
//     const textosArray = []; // Array para armazenar os textos
    
//     linhas.forEach((linha) => {
//         const ultimaCelula = linha.querySelector("td.py-3.px-4");
    
//         if (ultimaCelula) {
//             // Obtém e armazena o conteúdo da última célula no array
//             textosArray.push(ultimaCelula.textContent.trim());
//         }
//     });
    
//     console.log(textosArray); // Mostra o array com os textos das células desejadas')
//     ->getReturnValue();

//     var_dump($pageTitle);

// } finally {
//     // bye
//     // $browser->close();
// }


require 'vendor/autoload.php';
use HeadlessChromium\BrowserFactory;
use League\Csv\Writer;

$browserFactory = new BrowserFactory();

// Inicia o Chrome sem interface gráfica
$browser = $browserFactory->createBrowser();

try {
    // Cria uma nova página e navega para uma URL
    $page = $browser->createPage();
    $page->navigate('https://amleiloeiro.com.br/lote/21402/immc-asx-20-20122013')->waitForNavigation();

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
    // var_dump($textosArray);

    $string = $textosArray[3];
    // Separar a string em um array de leilões
    $leiloes = explode(" ", $string);
    // var_dump($leiloes);

    // $infos = explode("-", $leiloes);
    
    $string7 = explode("2º", $leiloes[7]);
    // var_dump($string7);

    // $data1 = montarData($leiloes,$string7);

    // var_dump($data1);

    $leilao1= data($leiloes[0],$leiloes[3],$string7[0]);

    var_dump($leilao1);

    $leilao2= data('2º',$leiloes[10],$leiloes[14]);

    var_dump($leilao2);

    $leiloes = $leilao2;

    // Imprimir cada leilão
    // foreach ($leiloes as $leilao) {
    //     // Separar as informações do leilão
    //     $infos = explode("-", $leilao);

    //     // Formatar a data
    //     $data = date("d/m/Y H:i", strtotime($infos[0]));

    //     // Imprimir as informações do leilão
    //     echo "Leilão: {$infos[1]} - Data: {$data} - Valor: {$infos[2]}";
    // }

    // // Adiciona o link da página como o primeiro elemento no array
    // array_unshift($textosArray, 'https://amleiloeiro.com.br/lote/21295/casa');

    // // Caminho do arquivo CSV
    // $csvFilePath = 'resultados.csv';

    // // Cria um escritor CSV
    // $csvWriter = Writer::createFromPath($csvFilePath, 'w+');

    // // Adiciona os dados ao arquivo CSV
    // $csvWriter->insertAll([$textosArray]);

    // echo "Arquivo CSV gerado com sucesso: $csvFilePath";

} finally {
    // Fecha o navegador
    // $browser->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sua Página</title>
  <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
  
  <table id="minhaTabela">
    <thead>
      <tr>
        <!-- <th>Link</th>; -->
        <th>Leilão</th>
        <th>Datas</th>
        <th>Preço</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($leiloes as $leilao): ?>
        <tr>
            <td><?php echo $leilao['leilao']; ?></td>
            <td><?php echo $leilao['data']; ?></td>
            <td><?php echo $leilao['valor']; ?></td>
        </tr>
    <?php endforeach; ?>
        <!-- <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>       -->
    </tbody>
  </table>
  
  <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

  <script>
  $(document).ready(function(){
      $('#minhaTabela').DataTable({
        	"language": {
                "lengthMenu": "Mostrando _MENU_ registros por página",
                "zeroRecords": "Nada encontrado",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros no total)"
            }
        });
  });
  </script>
  
</body>
</html>