//Ayuda comportamiento formulario añadir repositorio
let formRepo = document.getElementById('formRepoInfo');
// formRepo.style.backgroundColor='green';

let radioTipoFichero = document.getElementById('tipo_ficheros');
let radioTipoDocuware = document.getElementById('tipo_docuware');

let fsTiposRepo=document.getElementById('fieldsetTiposRepo');

fsTiposRepo.addEventListener('change',cambioTipoRepo);

cambioTipoRepo(null); //llamada inicial

function cambioTipoRepo(e){
    console.log("Fichero: "+radioTipoFichero.checked);
    console.log("Docuware: "+radioTipoDocuware.checked);
    if(radioTipoFichero.checked){
        //No es necesario campo "Info. Adicional"
        document.querySelector('label[for="extra"]').style.visibility='hidden';
        document.querySelector('label[for="extra"]').textContent='Extra';
        document.getElementById('extra').style.visibility='hidden';
        //La ruta es una Ruta normal
        document.querySelector('label[for="ruta"]').textContent='Ruta';

    }
    if(radioTipoDocuware.checked){
        //El campo "Info. Adicional" contiene el ID del archivador
        document.querySelector('label[for="extra"]').style.visibility='visible';
        document.querySelector('label[for="extra"]').textContent='ID Archivador Docuware:';
        document.getElementById('extra').style.visibility='visible';
        //La ruta es una URL
        document.querySelector('label[for="ruta"]').textContent='Ruta (Url)';
    }
}

