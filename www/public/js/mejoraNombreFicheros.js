let nombresDoc = document.querySelectorAll('.nombreDocumento');

nombresDoc.forEach(e=>{
    e.addEventListener('mouseover',muestraTextoNombreDoc);
    e.addEventListener('mouseout',ocultaTextoNombreDoc);
});

function muestraTextoNombreDoc(e){
    //console.log('MUESTRA: '+e.target.innerText);
    e.target.style.overflow='visible';
    e.target.style.whiteSpace='wrap';
    e.target.parentElement.querySelector('.infoDocumento').style.visibility ='hidden';
}

function ocultaTextoNombreDoc(e){
    //console.log('OCULTA: '+e.target.innerText);
    e.target.style.overflow='hidden';
    e.target.style.whiteSpace='nowrap';
    e.target.parentElement.querySelector('.infoDocumento').style.visibility ='visible';
}




