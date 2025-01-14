<h1>Información repositorio <?=$datos['id']?></h1>

<table>
<tr><th colspan='2'>Información</th></tr>
<?php
    foreach((array) $datos['info'] as $c => $v){
        if(!is_array($c) && !is_array($v)){
            echo "<tr><td>$c</td><td>$v</td></tr>";
        }
    }
?>
<table>
<br/>
<table>
<tr><th>Campos</th></tr>
<?php
    foreach((array) $datos['campos'] as $c => $v){
        echo "<tr><td>$v</td></tr>";
    }
?>
<table>

<?php
//var_dump($datos);

VistaComponentes::menuBar([
    (Object)['tooltip'=>'Listado repositorios',
            'url'=>'/AdminRepositorios/listaRepositorios',
            'icono'=>'/img/icon/layout-list.svg'],

    (Object)['tooltip'=>'Página principal',
            'url'=>'/principal/portada',
            'icono'=>'/img/icon/home.svg']
]);
