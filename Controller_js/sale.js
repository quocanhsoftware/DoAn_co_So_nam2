document.addEventListener('DOMContentLoaded',function(){
var modes=document.querySelectorAll('.sale-modes .mode');
modes.forEach(function(btn){btn.addEventListener('click',function(){modes.forEach(function(b){b.classList.remove('active')});btn.classList.add('active')})});
var pay=document.querySelector('.pay-btn');
if(pay){pay.addEventListener('click',function(){alert('Thanh toán')})}
var addTab=document.querySelector('.tab-plus');
if(addTab){addTab.addEventListener('click',function(){alert('Thêm hóa đơn')})}
});