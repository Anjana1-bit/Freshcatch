document.querySelector('.nav-toggle')?.addEventListener('click',()=>document.querySelector('nav').classList.toggle('open'));
document.querySelectorAll('[data-confirm]').forEach(b=>b.addEventListener('click',e=>{if(!confirm(b.dataset.confirm))e.preventDefault()}));
document.querySelectorAll('[data-preview]').forEach(input=>input.addEventListener('change',()=>{const file=input.files[0],preview=input.parentElement.parentElement.querySelector('.preview');if(file&&preview){preview.src=URL.createObjectURL(file);preview.hidden=false}}));
const role=document.querySelector('#role-select'),seller=document.querySelector('.seller-fields');function sellerFields(){if(seller)seller.style.display=role.value==='seller'?'contents':'none'}role?.addEventListener('change',sellerFields);sellerFields();
