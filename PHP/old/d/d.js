function getOffset(a){var b=0;var c=0;while(a&&!isNaN(a.offsetLeft)&&!isNaN(a.offsetTop)){b+=a.offsetLeft-a.scrollLeft;c+=a.offsetTop-a.scrollTop;a=a.offsetParent}return{top:c,left:b}}var persistvar='0';var div=document.getElementById('projectsdisplaybg');var bg=document.getElementById('projectsdisplaydiv');var phd=document.getElementById('projecthoverdiv');var pt=document.getElementById('pt');var triggerout=document.getElementById('triggerout');var leftedge=getOffset(phd).left;div.style.left=leftedge+'px';bg.style.left=leftedge+'px';pt.style.left=leftedge+'px';triggerout.style.left=(leftedge-16)+'px';window.onresize=function(){leftedge=getOffset(phd).left;div.style.left=leftedge+'px';div.style.backgroundColor='#030007';bg.style.left=leftedge+'px';pt.style.left=leftedge+'px';triggerout.style.left=(leftedge-16)+'px'};function persistin(){div.style.backgroundColor='black';persistvar='1';div.style.opacity='.8';bg.style.opacity='1';div.style.border='1px solid white';pt.style.zIndex='0';triggerout.style.display='block'}function persistout(){persistvar='0';div.style.opacity='.5';bg.style.opacity='.8';div.style.border='1px dotted white';div.style.backgroundColor='#030007';pt.style.zIndex='180';triggerout.style.display='none'}function show(){div.style.opacity='.5';bg.style.opacity='.8';div.style.backgroundColor='#030007';div.style.border='1px dotted white';div.style.display='block';pt.style.display='block';triggerout.style.display='block'}function hide(){if(persistvar=='1'){void(0)}else{div.style.display='none';pt.style.display='none';triggerout.style.display='none'}}function forcehide(){div.style.backgroundColor='#030007';div.style.display='none';pt.style.display='none';triggerout.style.display='none';persistvar='0';div.style.opacity='.5';bg.style.opacity='.8';div.style.border='1px dotted white';pt.style.zIndex='180'}