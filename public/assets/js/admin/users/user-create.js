document.addEventListener('DOMContentLoaded', () => {
 const form=document.getElementById('formNewUser'), role=document.getElementById('rol_id');
 const leagueBox=document.getElementById('boxLiga'), teamBox=document.getElementById('boxEquipo');
 const league=document.getElementById('liga_id'), team=document.getElementById('equipo_id');
 const key=()=>role?.selectedOptions[0]?.dataset.roleKey||'';
 const sync=()=>{const k=key();leagueBox?.classList.toggle('d-none',!['LIGA','EQUIPO'].includes(k));teamBox?.classList.toggle('d-none',k!=='EQUIPO');if(!['LIGA','EQUIPO'].includes(k)&&league)league.value='';if(k!=='EQUIPO'&&team)team.value='';};
 role?.addEventListener('change',sync);sync();
 if(league&&team){
  fetch('index.php?page=ajax&action=ligas').then(r=>r.json()).then(items=>items.forEach(x=>{const o=document.createElement('option');o.value=x.id;o.textContent=x.nombre;league.appendChild(o);}));
  league.addEventListener('change',()=>{team.innerHTML='<option value="">Selecciona un equipo</option>';if(!league.value)return;fetch('index.php?page=ajax&action=equipos&liga_id='+encodeURIComponent(league.value)).then(r=>r.json()).then(items=>items.forEach(x=>{const o=document.createElement('option');o.value=x.id;o.textContent=x.nombre;team.appendChild(o);}));});
 }
 form?.addEventListener('submit',e=>{e.preventDefault();const send=()=>fetch(form.action,{method:'POST',body:new FormData(form)}).then(r=>r.json()).then(result=>{if(!result.success)throw new Error(result.mensaje||'No fue posible crear el usuario.');if(window.Swal)Swal.fire({icon:'success',title:'Usuario creado',text:result.mensaje}).then(()=>location.href='index.php?page=users');else{alert(result.mensaje);location.href='index.php?page=users';}}).catch(error=>{if(window.Swal)Swal.fire({icon:'error',title:'No se pudo guardar',text:error.message});else alert(error.message);});if(window.Swal)Swal.fire({title:'Crear cuenta',text:'¿Deseas guardar este usuario?',icon:'question',showCancelButton:true,confirmButtonText:'Crear usuario',cancelButtonText:'Cancelar'}).then(x=>{if(x.isConfirmed)send();});else if(confirm('¿Deseas crear este usuario?'))send();});
});