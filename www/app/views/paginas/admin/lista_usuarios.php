<h1>Listado usuarios</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Login</th>
        <th>email</th>
        <th>rol</th>
        <th>Acciones</th>
    </tr>
    <?php
    foreach($datos['usuarios'] as $c=>$v){
        echo "<tr>";
        echo "<td>".$v->getID()."</td>";
        echo "<td>".$v->getNombre()."</td>";
        echo "<td>".$v->getLogin()."</td>";
        echo "<td>".$v->getEmail()."</td>";
        echo "<td>".$v->getRol()."</td>";
        
        echo "<td>";        
        VistaComponentes::actionIcons([
            (Object)['tooltip'=>'Editar',           'url'=>'/AdminUsuarios/editUsuarioID/'.$v->getID(),            'icono'=>'/img/icon/edit.svg'],
            (Object)['tooltip'=>'Borrar',           'url'=>'/AdminUsuarios/borraUsuarioId/'.$v->getID(),           'icono'=>'/img/icon/trash.svg'],
        ]);
        echo "</td>";

        echo "</tr>";
    }
    ?>
</table>

<?php
if(isset($datos['error'])){
    echo "<p class='msgerror'>".$datos['error']."</p>";
}

?>


<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Añadir usuario',       'url'=>'/AdminUsuarios/addUsuarioNuevo',    'icono'=>'/img/icon/user-plus.svg'],
        (Object)['tooltip'=>'Página principal',     'url'=>'/principal/portada',                'icono'=>'/img/icon/home.svg']        
    ]);
?>

