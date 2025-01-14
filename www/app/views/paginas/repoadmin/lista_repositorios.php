<h1>Listado repositorios</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Ruta</th>
        <th>tipo</th>
        <th>admin</th>
        <th>ACCIONES</th>
    </tr>

    <?php
    foreach($datos['repositorios'] as $r){
            echo "<tr>";
            echo "<td>".$r->getID()."</td>";
            echo "<td>".$r->getNombre()."</td>";
            echo "<td>".$r->getRuta()."</td>";
            echo "<td>".$r->gettipo()->value."</td>";
            echo "<td>".$r->getIdAdmin()."</td>";

            echo "<td>";
            VistaComponentes::actionIcons([
                (Object)['tooltip'=>'Editar',           'url'=>'/AdminRepositorios/editRepoId/'.$r->getID(),            'icono'=>'/img/icon/edit.svg'],
                (Object)['tooltip'=>'Borrar',           'url'=>'/AdminRepositorios/borraRepoId/'.$r->getID(),           'icono'=>'/img/icon/trash.svg'],
                (Object)['tooltip'=>'Comprobar',        'url'=>'/AdminRepositorios/testRepoId/'.$r->getID(),            'icono'=>'/img/icon/eye.svg'],
                (Object)['tooltip'=>'Clonar',           'url'=>'/AdminRepositorios/clonRepoId/'.$r->getID(),            'icono'=>'/img/icon/squares.svg'],
                (Object)['tooltip'=>'Usuarios',         'url'=>'/AdminUsuarios/listaUsuarios/'.$r->getID(),             'icono'=>'/img/icon/users-group.svg'],
                (Object)['tooltip'=>'Importar usuarios','url'=>'/ImportUsuariosRepo/selectImportOptions/'.$r->getID(),  'icono'=>'/img/icon/file-type-csv.svg'],

            ]);
            echo "</td>";

            echo "</tr>";
        }
    ?>
</table>

<?php
    VistaComponentes::menuBar([
        (Object)['tooltip'=>'Añadir repositorio',
                'url'=>'/AdminRepositorios/addRepoNuevo/',
                'icono'=>'/img/icon/table-plus.svg'],
        (Object)['tooltip'=>'Página principal',
                'url'=>'/principal/portada',
                'icono'=>'/img/icon/home.svg']         
    ]);
?>