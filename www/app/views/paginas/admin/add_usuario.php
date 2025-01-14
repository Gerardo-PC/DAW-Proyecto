<h1>Añadir usuario</h1>
<p>Añadir nuevo usuario</p>
<form action='/AdminUsuarios/addUsuarioNuevo/' method="POST">
    <label for="nombre">Nombre</label> <input type="text" id = "nombre" name="nombre" value ='<?=empty($datos['nombre'])?"":$datos['nombre']?>'>
    <label for="login">Login</label> <input type="text" id = "login" name="login" value ='<?=empty($datos['login'])?"":$datos['login']?>'>
    <label for="pass">Contraseña</label> <input type="password" id = "pass" name="pass" value ='<?=empty($datos['pass'])?"":$datos['pass']?>'>
    <label for="email">email</label> <input type="email" id = "email" name="email" value ='<?=empty($datos['email'])?"":$datos['email']?>'>
    <fieldset>
        <legend>Rol</legend>
        <?php
        if($_SESSION['rol']=='admin'){
        ?>
        <input type="radio" id="rol_admin" name="rol" value="admin" <?=$datos['rol']=='admin'?"checked":""?> /><label for="rol_admin">Administrador</label>
        <input type="radio" id="rol_repoadmin" name="rol" value="repoadmin" <?=$datos['rol']=='repoadmin'?"checked":""?>/><label for="rol_repoadmin">Admin. Repositorios</label>
        <?php
            }
        ?>
        <input type="radio" id="rol_user" name="rol" value="user" <?=$datos['rol']=='user'?"checked":""?>/><label for="rol_user">Usuario</label>
    </fieldset>
    <input type="submit" value="AÑADIR">
    <?php if(!empty($datos['error'])){echo "<p class='msgerror'>".$datos['error']."</p>";} ?>
</form>
<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Listado Usuarios',
        'url'=>'/AdminUsuarios/listaUsuarios',
        'icono'=>'/img/icon/list-details.svg'] ,

        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']        
    ]);
?>