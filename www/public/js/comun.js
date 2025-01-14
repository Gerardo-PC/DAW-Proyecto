// ================   Gestión de tooltips, posicionados a la derecha del cursor ====================
let tooltips = document.querySelectorAll(".tooltip");

tooltips.forEach((e,i,a)=>{
    //console.log(e);
    e.setAttribute('style','z-index:1');
    e.style.position='absolute';
    e.style.transform='translate(1rem, 1rem)';
});

window.onmousemove = function(m){
    // Debug: console.log('M:' + m.clientX+','+m.clientY + '\t '+ window.scrollY);
    for(var i=0;i<tooltips.length;i++){
        tooltips[i].style.top=m.clientY+window.scrollY+'px';
        tooltips[i].style.left=m.clientX+window.scrollX+'px';
    }
}


//Gestión de footer que se esconde
const pie = document.querySelector('footer');

pie.addEventListener('mouseover',(e)=>{
    pie.classList.add('escondido');
})

