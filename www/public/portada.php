<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/estilo_portada.css" type="text/css"/>
    <title>WebDoc</title>
</head>
<body>
    <div id="contenedor">
        <h1>WebDoc</h1>
        <section>
            <h2>¿Qué es?</h2>
            <p>WebDoc es una plataforma para distribución de documentación desde repositorios privados a un elevado número de consumidores.</p>
            <div id='contenedorBoton'>
                <a id='btnAcceso' href="<?=$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"]."/login/login"?>" >Acceso</a>
            </div>
        <section>
        <article>
            <h2>Descripción</h2>
            <p>Existe una problemática habitual en el mundo empresarial, consistente en dar <b>acceso a muchos consumidores a su documentación</b> desde repositorios privados.</p>
            <h3>Algunos casos de uso</h3>
            <ul>
                <li>Las <b>nóminas</b> de empleados deberían estar accesibles para todos los trabajadores, pero muchas veces se encuentran en repositorios privados en el departamento de recursos humanos.</li>
                <li>Necesitamos distribuir a toda una organización documentación de <b>formación, normas empresariales o comunicados</b> (circulares).</li>
                <li>Nuestros clientes podrían querer acceder a sus <b>facturas</b> o a información de sus <b>pedidos</b> en cualquier instante.</li>
                <li>Podemos dar acceso controlado a alumnos a documentación con <b>notas, ejercicios</b> o contenidos específicos para los <b>cursos</b> a que pertenece.</li>
                <li>Podemos distribuir <b>información técnica</b> en función de perfiles a personal en desplazamiento.</li>
                <li>Y muchos mas...</li>            
            </ul>
            <p>WebDoc proporciona un sistema <i>sencillo</i> y <i>seguro</i> de acceso a la documentación seleccionada por administradores de repositorios privados a un elevado número de consumidores públicos.</p>
        </article>   
        <section> 
        <h2>Para saber más</h2>
            <p>Para saber más sobre este proyecto, o participar, puedes solicitar acceso al <a href="https://gitlab.iessanclemente.net/dawd/a22gerardopc">repositorio Gitlab</a> o ponerte en contacto con el <a href="mailto:a22gerardopc@iessanclemente.net?Subject=Quiero%20saber%20mas">autor.</a> </p>
            <p>También puedes echarle un vistazo a la <a href='./phpdoc/index.html'>documentación</a>, a la <a href='https://drive.google.com/file/d/1TaoxcVwoHlne2YjyIg34Ixkqte0Y5GT7/view?usp=drive_link'>presentación</a> o este <a href='https://1drv.ms/v/s!AgzElRm9Bh99kfBclUCqQDzWEQbAAQ?e=55k3hr'>vídeo de uso</a>.</p>
        </section>
        <!--
        <video width="720" height="480" controls>
            <source src="./img/WebDoc.mp4" type="video/mp4">
            Tu navegador no soporta este vídeo. Puedes <a href='https://1drv.ms/v/s!AgzElRm9Bh99kfBclUCqQDzWEQbAAQ?e=55k3hr'>descargar una copia aquí</a>.
        </video>
        -->
    </div>
</body>
</html>