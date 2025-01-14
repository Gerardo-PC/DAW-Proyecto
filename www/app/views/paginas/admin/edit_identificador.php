<script defer type='text/javascript' src='/js/ajaxFormIdentificadores.js'></script>
<h1>Información usuario</h1>
<p>Editando identificador <?=$datos['identificador']->getID()?> </p>
<form action='/AdminUsuarios/editIdentificador/<?=$datos['identificador']->getID()?>' method="POST">
    <label for="ID_Repositorio">Repositorio</label>
    <select name="ID_Repositorio" id="ID_Repositorio">
        <?php foreach($datos['repositorios'] as $v){
                echo "<option value='".$v->getID()."' ";
                if(!empty($datos['ID_Repositorio']) && $datos['ID_Repositorio']==$v->getID()){
                    echo " selected='selected'";
                }
                echo ">".$v->getNombre()."</option>";
            }
        ?>
    </select>

    <label for="ID_Usuario">Usuario</label>
    <select name="ID_Usuario">
        <?php foreach($datos['usuarios'] as $v){
                    echo "<option value='".$v->getID()."' ";
                    if(!empty($datos['identificador']->getIdUsuario()) && $datos['identificador']->getIdUsuario()==$v->GetID()){
                        echo " selected='selected'";
                    }
                    echo ">".$v->getLogin()." | ".$v->getNombre()."</option>";
                }
        ?>
    </select>

    <!-- Podría mantenerse por compatibilidad integral con HTML, sin Javascript
    <label for="claveTXT">clave</label> <input type="text" id="claveTXT" name="claveTXT" value='<?=empty($datos['identificador']->getClave())?'':$datos['identificador']->getClave()?>'>
    -->

    <label for="clave">clave</label><select name="clave" id="clave"><!--Opciones desde JavaScript--></select>
    
    <label for="valor">valor</label> <input type="valor" id="valor" name="valor" value='<?=empty($datos['identificador']->getValor())?'':$datos['identificador']->getValor()?>'>

    <?php if(!empty($datos['error'])){echo "<p style='color:red'>".$datos['error']."</p>";} ?>


    <input type="submit" value="ACTUALIZAR">
</form>


<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Volver a usuario',
                'url'=>'/AdminUsuarios/editUsuarioID/'.$datos['identificador']->GetIdUsuario(),
                'icono'=>'/img/icon/user-share.svg'],
        (Object)['tooltip'=>'Listado Usuarios',
                'url'=>'/AdminUsuarios/listaUsuarios',
                'icono'=>'/img/icon/list-details.svg'] ,
        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']         
    ]);
?>

