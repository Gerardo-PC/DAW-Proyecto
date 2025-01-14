<h1>Registro usuario</h1>
<form action=<?=URL.'/login/registra'?> method="POST">
    <label for="usuario">Login:</label>
    <input type="text" name="login" placeholder="nombre" <?php if(!empty($datos['login'])) echo "value=\"".$datos['login']."\""?>/>
    <?php if(isset($datos["usuarioExiste"])){echo "<span style='color:red;'>".$datos["usuarioExiste"]."</span>";}?>
    <label for="contraseña">Contraseña:</label>
    <input type="password" name="pass">
    <?php if(!empty($datos['error'])){echo "<p class='msgerror'>".$datos['error']."</p>";}?>
    <input type="submit" name="submit" value="REGISTRAR">
    <a href="./login">Volver a login.</a>
</form>