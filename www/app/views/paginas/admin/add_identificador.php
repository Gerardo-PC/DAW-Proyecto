<script defer type='text/javascript' src='/js/ajaxFormIdentificadores.js'></script>
<h1>Información usuario - Identificadores</h1>
<p>Añadiendo identificador usuario <?=$datos['id']?> </p>
<form action='/AdminUsuarios/crearIdentificador/<?=$datos['id']?>' method="POST">
    <label for="ID_Repositorio"> Repositorio </label>
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

    <!-- Podría mantenerse por compatibilidad integral con HTML, sin Javascript
        <label for="claveTXT">clave</label> <input type="text" id="claveTXT" name="claveTXT" value='<?=empty($datos['clave'])?'':$datos['clave']?>'>
    -->
    
    <label for="clave">clave</label><select name="clave" id="clave"><!--Opciones desde JavaScript--></select>

    <label for="valor">valor</label> <input type="valor" id="valor" name="valor" value='<?=empty($datos['valor'])?'':$datos['valor']?>'>

    <?php if(!empty($datos['error'])){echo "<p style='color:red'>".$datos['error']."</p>";} ?>

    <input type="submit" value="CREAR">
</form>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Volver a usuario',
                'url'=>'/AdminUsuarios/editUsuarioID/'.$datos['id'],
                'icono'=>'/img/icon/user-share.svg'],
        (Object)['tooltip'=>'Listado Usuarios',
                'url'=>'/AdminUsuarios/listaUsuarios',
                'icono'=>'/img/icon/list-details.svg'] ,
        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']         
    ]);
?>

