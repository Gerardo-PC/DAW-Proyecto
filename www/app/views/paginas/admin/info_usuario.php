<h1> Usuario</h1>

<h2>Información de usuario: <?=$datos['usuario']->getId()?> </h2>

<form class='formEstandar' action='/AdminUsuarios/editUsuarioID/<?=$datos['usuario']->getId()?>' method="POST">
    <label for="nombre">Nombre</label> <input type="text" id = "nombre" name="nombre" value ='<?=$datos['usuario']->getNombre()?>'>
    <label for="login">Login</label> <input type="text" id = "login" name="login" value ='<?=$datos['usuario']->getLogin()?>'>
    <label for="email">Email</label> <input type="email" id = "email" name="email" value ='<?=$datos['usuario']->getEmail()?>'>
    <fieldset <?php if($_SESSION['rol']!='admin'){echo "style='display:none;'";}?>>
        <legend>Rol</legend>
        <input type="radio" id="rol_admin" name="rol" value="admin" <?=$datos['usuario']->isAdmin()?"checked":""?> /><label for="rol_admin">Administrador</label>
        <input type="radio" id="rol_repoadmin" name="rol" value="repoadmin" <?=$datos['usuario']->isRepoAdmin()?"checked":""?>/><label for="rol_repoadmin">Admin. Repositorios</label>
        <input type="radio" id="rol_user" name="rol" value="user" <?=$datos['usuario']->isUser()?"checked":""?>/><label for="rol_user">Usuario</label>
    </fieldset>
    <input type="submit" value="ACTUALIZAR">
</form>

<?php if(!empty($datos['identificadores']) && $_SESSION['rol']=='admin'){ ?>
    <h2>Identificadores</h2>
    <table>
            <tr>
                <th>ID</th>
                <th>ID_USUARIO</th>
                <th>CLAVE</th>
                <th>VALOR</th>
                <th>ID_REPO</th>
                <th>Acción</th>
            </tr>
            <?php
            foreach($datos['identificadores'] as $v){
                echo "<tr>";
                echo "<td>".$v->getID()."</td>";
                echo "<td>".$v->getIdUsuario()."</td>";
                echo "<td>".$v->getClave()."</td>";
                echo "<td>".$v->getValor()."</td>";
                echo "<td>".$v->getIdRepositorio()."</td>";
                //echo "<td><a href='/AdminUsuarios/editIdentificador/".$v->getID()."'>[ Editar ]</a> <a href='/AdminUsuarios/cloneIdentificador/".$v->GetID()."'>[ Clonar ]</a><a href='/AdminUsuarios/borraIdentificador/".$v->GetID()."'>[ Borrar ]</a> </td>";

                echo "<td>";
                VistaComponentes::actionIcons([
                    (Object)['tooltip'=>'Editar',           'url'=>'/AdminUsuarios/editIdentificador/'.$v->getID(),            'icono'=>'/img/icon/edit.svg'],
                    (Object)['tooltip'=>'Clonar',           'url'=>'/AdminUsuarios/cloneIdentificador/'.$v->getID(),           'icono'=>'/img/icon/squares.svg'],
                    (Object)['tooltip'=>'Borrar',           'url'=>'/AdminUsuarios/borraIdentificador/'.$v->getID(),           'icono'=>'/img/icon/trash.svg'],    
                ]);
                echo "</td>";




                echo "<tr>";
            }
            ?>
    </table>
<?php }?>
<?php if(!empty($datos['error'])){echo "<p class='msgerror'>".$datos['error']."</p>";} ?>

<?php
    //barra de opciones inferior.
    $opciones = [];
    if($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin'){
        array_push($opciones, (Object)['tooltip'=>'Crear identificador','url'=>'/AdminUsuarios/crearIdentificador/'.$datos['usuario']->getId(),'icono'=>'/img/icon/layout-grid-add.svg']);
    }
    array_push($opciones,(Object)['tooltip'=>'Cambiar Contraseña','url'=>'/AdminUsuarios/cambiaPass/'.$datos['usuario']->getId(),'icono'=>'/img/icon/id-badge-2.svg']);
    if($_SESSION['rol']=='admin' || $_SESSION['rol']=='repoadmin'){
        array_push($opciones,(Object)['tooltip'=>'Listado Usuarios','url'=>'/AdminUsuarios/listaUsuarios','icono'=>'/img/icon/list-details.svg']);
    }
    array_push($opciones,(Object)['tooltip'=>'Página principal','url'=>'/principal/portada','icono'=>'/img/icon/home.svg']);
    VistaComponentes::menuBar($opciones);
?>
