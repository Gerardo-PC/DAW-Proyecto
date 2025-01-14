<h1>Login</h1>
<form class='formLogin' action=<?=URL.'/login/login'?> method="POST">
    <label for="usuario">Usuario:</label>
    <input type="text" name="login" placeholder="login" <?php if(!empty($datos['login'])) echo "value=\"".$datos['login']."\""?>/>
    <label for="contraseña">Contraseña:</label>
    <input type="password" name="pass" placeholder="contraseña" <?php if(!empty($datos['pass'])) echo "value=\"".$datos['pass']."\""?>>   
    <input type="submit" name="submit" value="login">
    <?php 
        if(!empty($datos["usuarioNoValido"])){
            echo "<p class='msgerror'>El usuario o la contraseña no son válidos.</p>";
        }
    ?>
    <a href="/login/registra">Registrar usuario</a>    
    <a href="/login/recuperaContrasenaMail">Recuperar contraseña</a>    
    <a href="/">Volver a inicio</a>
</form>