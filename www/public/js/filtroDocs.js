//Comportamiento botón filtros
let botonFiltro= document.getElementById('showfilter');
let barra=document.getElementById('barraFiltros');
botonFiltro.onclick = (e)=>{
    if(barra.style.position=='' || barra.style.position=='relative'){        
        botonFiltro.classList.add('btnFiltroIcon');
        botonFiltro.classList.remove('btnFiltroGrande');
        botonFiltro.src='/img/icon/filter-code.svg';
        
        barra.style.position='absolute';
        barra.style.zIndex='1';        
        barra.style.height='3rem';
        barra.style.width='3rem';
        barra.style.borderRadius='1.5rem';        
        barra.style.overflow='hidden';      
    }else{
        botonFiltro.classList.add('btnFiltroGrande');
        botonFiltro.classList.remove('btnFiltroIcon');
        botonFiltro.src='/img/icon/filter-cancel.svg';

        barra.style.position='relative';        
        barra.style.zIndex='0';
        barra.style.removeProperty('height');
        barra.style.removeProperty('width');
        barra.style.removeProperty('border-radius');
        barra.style.removeProperty('overflow');
    }
};

//Cambia de estado el filtro al pulsar.
function interruptorFiltro(e){
    let boton = e.currentTarget;
    //cambia el estado del filtro
    arrFiltros[boton.innerText]=!arrFiltros[boton.innerText];
    //actualiza la representación del filtro
    if(!arrFiltros[boton.innerText]){
        boton.style.textDecoration='line-through';
        boton.style.backgroundColor='var(--mandy)';
    }else{
        boton.style.textDecoration='none';
        boton.style.removeProperty('background-color');
    }
    console.log("Actualizado:" + boton.innerText + ":" + arrFiltros[boton.innerText]);
    //actualiza todos los ficheros en función del filtro.
    actualizaFicherosFiltro();
}

const regexEtiquetas = /\[.*?\]/g;
function actualizaFicherosFiltro(){
    //console.log('Actualizando filtro ficheros.');
    let fileItems = document.querySelectorAll('.fileItem');
    fileItems.forEach((e)=>{
        let infoFichero = e.getElementsByClassName('infoDocumento')[0].innerText;
        //console.log('Actualizando:'+ infoFichero);
        let etiquetasFichero=infoFichero.matchAll(regexEtiquetas);
        let ficheroVisible=true;
        etiquetasFichero.forEach(e => {            
            //console.log(e);
            if(!arrFiltros[e]){
                ficheroVisible=false;//si algún filtro del array de filtros está desactivado el fichero dejará de ser visible
            }
        });

        let nombreFichero = e.getElementsByClassName('nombreDocumento')[0].innerText.toUpperCase();
        if(filtroNombreDoc.value.length>0){
            let filtro = filtroNombreDoc.value.toUpperCase();
            if(!nombreFichero.includes(filtro)){
                ficheroVisible=false;
            }
        }

        if(ficheroVisible){
            // e.style.backgroundColor='green';
            e.style.display='grid';
        }else{
            //e.style.backgroundColor='red';
            e.style.display='none';
        }
    });
}

let arrFiltros=[];
let botonesFiltros=document.querySelectorAll('.filtroDoc');
botonesFiltros.forEach((b)=>{
    arrFiltros[b.innerText]=true; //activo por defecto
    b.onclick=interruptorFiltro;
});


//Comportamiento filtro ficheros
const filtroNombreDoc = document.getElementById('filtroNombreDoc');

filtroNombreDoc.addEventListener('input',actualizaFiltroNombreDoc);

function actualizaFiltroNombreDoc(e){
    //let filtro = this.value;
    //console.log('Filtrando por texto: '+filtro);
    if(filtroNombreDoc.value.length>0){
        filtroNombreDoc.classList.add('filtroNombreDocActivo');
    }else{
        filtroNombreDoc.classList.remove('filtroNombreDocActivo');
    }
    actualizaFicherosFiltro();
}
