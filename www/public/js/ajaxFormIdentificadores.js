//Oculta los campos de texto para dejar los campos select de identificadores. Sólo si compatibilidad 100% html.
//document.querySelector('label[for="claveTXT"]').style.display='none';
//document.querySelector('#claveTXT').style.display='none';

let selectIdRepo =document.getElementById('ID_Repositorio');
selectIdRepo.addEventListener('change',actualizaCamposRepo);

let selectCamposRepo=document.getElementById('clave');

//Llama inicialmente para recuperar los campos.
xhrCampos = new XMLHttpRequest();
let urlApi=location.protocol+'//'+location.host+'/ApiRepositorios/getRepoClaves/'+selectIdRepo.value ;
console.log('Llamando a:'+urlApi);
xhrCampos.open('GET', urlApi); 
xhrCampos.addEventListener("load", cargaCamposRepo);
xhrCampos.send();

let camposRepo =[];
function cargaCamposRepo(e){
    camposRepo= JSON.parse(e.currentTarget.responseText);
    console.log(camposRepo);
    
    //Borra los campos anteriores
    while(selectCamposRepo.options.length>0){
        selectCamposRepo.remove(selectCamposRepo.options.length-1);
    }

    //Añade los campos nuevos
    camposRepo.forEach(e => {
        let campo = document.createElement("option");
        campo.text=e;
        selectCamposRepo.add(campo);
    });

}

function actualizaCamposRepo(e){
    urlApi=location.protocol+'//'+location.host+'/ApiRepositorios/getRepoClaves/'+selectIdRepo.value ;
    xhrCampos.open('GET', urlApi); 
    xhrCampos.send();
}