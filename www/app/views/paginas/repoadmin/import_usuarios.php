<h1> Importación usuarios repositorio <?=$datos['repositorio']->getID()?></h1>
<form action='/ImportUsuariosRepo/selectImportOptions/<?=$datos['repositorio']->getID()?>' method='POST' enctype="multipart/form-data">
    <label for='ficheroCSV'>Fichero CSV usuarios.</label>
    <input type='file' name='ficheroCSV' id='ficheroCSV' accept='text/csv'></input>
    <label for='separadorCSV'>Caracter separador en CSV:</label>
    <input type='text' name='separadorCSV' id='separadorCSV'value=';'></input>
    <input type='submit' id='submit' name='importFichero' value='IMPORTAR'></input>
</form>

<?php
//error en formulario
if(!empty($datos['error'])){
    echo "<p style='color:red'>".$datos['error']."</p>";
}
?>
<hr/>
<h2>Datos importados</h2>
<div class='tablaOverflow'>
<?php
if(!empty($datos['cabeceraCSV'])){
    //Cabecera de la tabla
    echo "<table>";
    echo "<tr>";
    foreach($datos['cabeceraCSV'] as $v){
        echo "<th>".$v."</th>";
    }
    echo "</tr>";
    
    //Cuerpo de la tabla
    foreach($datos['datosCSV'] as $linea){
        echo "<tr>";
        foreach($linea as $dato){
            echo "<td>$dato</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}else{
    echo "<p>No hay campos importados. Necesario importar fichero CSV antes.</p>";
}
?>
</div>

<hr/>

<h2>Campos mapeo</h2>

<form action='/ImportUsuariosRepo/selectImportOptions/<?=$datos['repositorio']->getID()?>' method="POST">
<h3>Mapeo Usuario</h3>

<label for="nombre">Nombre:</label>
<select name="nombre">
    <?php
    if(!empty($datos['cabeceraCSV'])){
        foreach($datos['cabeceraCSV'] as $c=>$v){
            echo "<option value='$c'>$v</option>";
        }
    }
    ?>
</select>

<label for="login">Login:</label>
<select name="login">
    <?php
    if(!empty($datos['cabeceraCSV'])){
        foreach($datos['cabeceraCSV'] as $c=>$v){
            echo "<option value='$c'>$v</option>";
        }
    }
    ?>
</select>

<label for="pass">Contraseña:</label>
<select name="pass">
    <?php
    if(!empty($datos['cabeceraCSV'])){
        foreach($datos['cabeceraCSV'] as $c=>$v){
            echo "<option value='$c'>$v</option>";
        }
    }
    ?>
</select>

<label for="email">email:</label>
<select name="email">
    <?php
    if(!empty($datos['cabeceraCSV'])){
        foreach($datos['cabeceraCSV'] as $c=>$v){
            echo "<option value='$c'>$v</option>";
        }
    }
    ?>
</select>

<!-- Campos mapeo Repositorio -->
<h3>Campos Repositorio</h3>

<?php
if(!empty($datos['camposRepositorio'])){
    foreach($datos['camposRepositorio'] as $campo){
        echo "<label for='camposRepositorio[$campo]'>$campo</label>";
        echo "<select name='camposRepositorio[$campo]'>";
        echo "<option value='-1'>--- Sin asignar ---</option>";
        if(!empty($datos['cabeceraCSV'])){
            foreach($datos['cabeceraCSV'] as $c=>$v){
                echo "<option value='$c'>$v</option>";
            }
        }
        echo "</select>";
    }
}
?>

<h3>Opciones</h3>
<fieldset id='fieldsetTiposRepo'>
    <legend>En caso de que usuario exista</legend>
    <input type="radio" id="usr_replace" name="usuarioExiste" value="usrReplace" checked/><label for="usr_replace">Reemplazar usuario</label>
    <input type="radio" id="usr_addIdentificadores" name="usuarioExiste" value="usrAdd"/><label for="usr_addIdentificadores">Añadir identificadores</label>
    <input type="radio" id="usr_skip" name="usuarioExiste" value="usrSkip"/><label for="usr_skip">Omitir usuario</label>
</fieldset>

<input type="submit" name="procesarDatos" value="AÑADIR">
</form>

<?php
// var_dump($datos);

    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Listado repositorios',
                'url'=>'/AdminRepositorios/listaRepositorios',
                'icono'=>'/img/icon/layout-list.svg'],

        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']
    ]);
?>