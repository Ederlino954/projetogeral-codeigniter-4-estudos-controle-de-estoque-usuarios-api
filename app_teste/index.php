<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>App Teste</title>
</head>
<body>
    <h3>App Teste</h3>
    <hr>

    <?php     

    require_once 'api.php';

    // Inserir produtos no stock 
    $post_vars = array( // alterar opções conforme a necessidade de cada endpoint
            'app_key' => 'IjV3fSmmMXdJ9rm0Ui0GGGQzzI4PO2XN',
            'id_produto' => 1,
            'quantidade' => 20
    );

    $resultados = api('http://localhost/projetogeral/api/add_to_stock/', $post_vars); // posso mudar o link dos endpoints para qual necessitar // no caso inserindo 

    echo '<pre>';
    print_r($resultados);
    echo '</pre>';


    ?>


</body>
</html>