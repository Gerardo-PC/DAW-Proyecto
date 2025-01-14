<script defer type='text/javascript' src='/js/filtroFormRepositorios.js'></script>
<h1>Modificar repositorio</h1>
<h2>Editar repositorio <?=$datos['repositorio']->getID()?></h2>
<form id='formRepoInfo' action="/AdminRepositorios/editRepoId/<?=$datos['repositorio']->getID()?>" method="POST">
    <fieldset id='fieldsetTiposRepo'>
        <legend>Tipo</legend>
        <input type="radio" id="tipo_ficheros" name="tipo" value="ficheros" <?=$datos['repositorio']->getTipo()->value=='ficheros'?"checked":""?>/><label for="tipo_ficheros">Ficheros</label>
        <input type="radio" id="tipo_docuware" name="tipo" value="docuware" <?=$datos['repositorio']->getTipo()->value=='docuware'?"checked":""?>/><label for="tipo_docuware">Docuware</label>
    </fieldset>
    <label for="nombre">Nombre</label> <input type="text" id = "nombre" name="nombre" value ='<?=empty($datos['repositorio']->getNombre())?"":$datos['repositorio']->getNombre()?>'>
    <label for="ruta">Ruta</label> <input type="text" id = "ruta" name="ruta" value ='<?=empty($datos['repositorio']->getRuta())?"":$datos['repositorio']->getRuta()?>'>
    <label for="login">Login</label> <input type="text" id = "login" name="login" value ='<?=empty($datos['repositorio']->getLogin())?"":$datos['repositorio']->getLogin()?>'>
    <label for="pass">Contraseña</label> <input type="password" id = "pass" name="pass" value ='<?=empty($datos['repositorio']->getPass())?"":$datos['repositorio']->getPass()?>'>
    <label for="extra">Info. Adicional:</label> <input type="text" id = "extra" name="extra" value ='<?=empty($datos['repositorio']->getExtraObject())?"":$datos['repositorio']->getExtraObject()?>'>
    
    <label for="repoadmin">Administrador</label>
    <select name="ID_admin" id="ID_admin">
        <?php foreach($datos['repoadmins'] as $v){
            echo "<option value='".$v->getID()."' ";
            if(!empty($datos['repositorio']->getIdAdmin()) && $datos['repositorio']->getIdAdmin()==$v->getID()){
                echo " selected='selected'";
            }
            echo ">".$v->getNombre()."</option>";
        }
        ?>
    </select>

    <?php if(!empty($datos['error'])){echo "<p class='msgerror'>".$datos['error']."</p>";} ?>
    <input type="submit" value="GUARDAR">
</form>


<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Listado repositorios',
                'url'=>'/AdminRepositorios/listaRepositorios',
                'icono'=>'/img/icon/layout-list.svg'],

        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']
    ]);
?>