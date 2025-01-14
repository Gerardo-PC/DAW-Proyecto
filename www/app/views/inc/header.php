<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tipo de letra Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">     

    <!-- Tipo de letra Arvo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="/img/FavIcon.svg">
    <link rel="stylesheet" href="/css/estilo.css" type="text/css"/>
    <script defer type="text/javascript" src="/js/comun.js"></script>
    <title>WebDoc</title>
</head>
<body>
    <header>
        <?php 
            if(isset($_SESSION['login']) && isset($_SESSION['rol']) && isset($_SESSION['IdUsuario']) ){
        ?>
                <a class='iconIfaz' href='/AdminUsuarios/editUsuarioId/<?=$_SESSION['IdUsuario']?>'> 
                    <!-- <img class='iconIfaz' src='/img/Usuario.svg'/> -->
                </a>
                <div class='infoUsuarioHeader'>
                    <p><?=$_SESSION['nombre']?></p>
                    <p>
                        <a id='logout' class='btnInline' href='/login/logout'>                        
                        <img src='/img/icon/logout-2.svg' alt='Icono cerrar sesión.'></img>&nbsp; Salir 
                        </a> 
                        <?=$_SESSION['rol']?> (<?=$_SESSION['IdUsuario']?>)
                    </p>
                </div>
        <?php
            }else{
                // si no se está identificado no muestra nada.
                // echo " [ sin identificar. ] ";
            }
        ?>
    </header>
<main>