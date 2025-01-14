<h1>Recuperar contraseña</h1>
<p>Indicar login y mail de usuario para enviar contraseña.</p>
<?php 
if(!empty($datos["mensaje"])){
    echo "<br><p class='msgOK'>".$datos["mensaje"]."</p>";
}else{
    ?>
        <form action=<?=URL.'/login/recuperaContrasenaMail'?> method="POST">        
            <label for="usuario">Nombre de usuario:</label>
            <input type="text" name="login" placeholder="nombre" <?php if(!empty($datos['login'])) echo "value=\"".$datos['login']."\""?>/>
            <label for="mail">e-mail:</label>
            <input type="email" name="email" <?php if(!empty($datos['email'])) echo "value=\"".$datos['email']."\""?>>
            <?php 
                if(!empty($datos["error"])){
                    echo "<p class='msgerror'>".$datos["error"]."</p>";
                }
            ?>
            <br/>
            <input type="submit" name="recuperar" value="recuperar">
        </form>
    <?php
}
    ?>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Volver a Login',
                'url'=>'/Login/Login/',
                'icono'=>'/img/icon/home.svg']
    ]);
?>

