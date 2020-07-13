<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Projeto Geral - Users</title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/app.css') ?>">

</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center bg-dark text-light p-3">
            <h3>PROJETO GERAL - Users</h3>
        </div>        
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <!-- área de seção que a view ira injetar nesse espaço  -->
            <?php $this->renderSection('conteudo') ?> <!-- renderizando conteúdo  -->
        </div>
    </div>

</div>
   
    <!-- javascript -->
    <script src="<?php echo base_url('assets/js/jquery-3.4.0.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/app.js') ?>"></script> <!-- tempo de desaparecimento de error  -->
    
</body>
</html>