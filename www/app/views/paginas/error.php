<h1>Error</h1>

<p>Se ha producido un error.</p>
<?php
echo "<p class='msgerror'> ERROR: ".(isset($datos['error']))?$datos['error']:'Error genérico.'."</p>";
echo "<a href='/'>Volver a página principal</a>";
