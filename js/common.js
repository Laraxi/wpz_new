/*菜单加个显示在线用户的按钮 （始）*/
var myMenu;
window.onload = function() {
    myMenu = new SDMenu("my_menu");
    myMenu.init();
/*右侧正文部分内容自适应屏幕-横向滚动条 （始）*/
function th(){
var h =document.body.offsetHeight;
var odiv1024 = document.getElementById('div1024');
odiv1024.style.height=h-250+"px";
}
th()
window.onresize = function(){
th();
}
}; 
/*右侧正文部分内容自适应屏幕-横向滚动条 （止）*/
$(document).ready(function(e) {
   $(".Switch").click(function(){
     $(".left").toggle();
   });
});
!function(){
laydate.skin('molv');
laydate({elem: '#Calendar'});
laydate.skin('molv');
laydate({elem: '#Calendar2'});
}();
$(function(){
   $(".select_button_caidan").click(function(){
      $("#my_yonhu").hide()
         $("#my_menu").show()
   })
   $(".select_button_yonhu").click(function(){
      $("#my_yonhu").show()
         $("#my_menu").hide()
   })
})
/*菜单加个显示在线用户的按钮 （止）*/

//邮箱验证
function isEmail(str){
	var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
	
	return reg.test(str);
}

//手机验证
function IsTel(Tel){ 
	var re=new RegExp(/^((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)$/);
	var retu=Tel.match(re);
	if(retu){
		return true;
	}else{
		return false;
	}
} 

function getId(id) {
	return document.getElementById(id) ? document.getElementById(id) : false;
}

//select选中(text)
function selectOptions(id,value) {
	var objSelect = getId(id);
	for(var i=0; i<objSelect.options.length; i++) {
	   if(objSelect.options[i].text == value) {
		   objSelect.options[i].selected = true;
		   break;
	  }
	}
}

//select选中(value)
function selectOptions1(id,value) {
	var objSelect = getId(id);
	for(var i=0; i<objSelect.options.length; i++) {
	   if(objSelect.options[i].value == value) {
		   objSelect.options[i].selected = true;
		   break;
	  }
	}
}

//json选中select
function selectJson(json){
	for (var key in json) {
		if (getId(key) && getId(key).type == 'select-one'){
			selectOptions(key,json[key]);
			if (key=="department") {
				if(json[key]) setPosition(json[key]);
			}
		}
	}
}

//获取url参数
function getparm(key) {
	var parm = (location.href.split(key+'='))[1];
	parm = parm ? (parm.split('&'))[0] : "";
	return parm ? parm : "";
}

function checkCancel(obj) {
  var ids = document.getElementsByName("id[]");
  for (var i=0; i<ids.length; i++) {
	  ids.item(i).checked = obj.checked;
  }
}
function exportExcel(url) {
	var values = "";
	var ids = document.getElementsByName("id[]");
	for (var i=0; i<ids.length; i++) {
		if (ids.item(i).checked == true) {
			values = values+ids.item(i).value+",";
		}
	}
	values = values.substr(0,(values.length-1));
	if (values=="") {
		alert("请选择要导出的记录！");
	} else {
		window.location = url+"&ids="+values;
	}
}

//表单验证函数
function formCheck(form){
	var formArr = form.elements;
	for (var i=0; i<formArr.length; i++) {
		var items = formArr[i];
		if (items.getAttribute("data-required") && items.value=="") {
			alert(items.getAttribute("data-required"));
			items.focus();
			return false;
		}
	}
		
	return true;
}

function submitForm(formName,actionCode){
	var form = document.forms[formName];
	if (formCheck(form)) {
		var does = getId("id") && getId("id").value!="" ? "update" : "add";
		form.action="?action="+actionCode+"&do="+does;
		form.submit();
	}
}

function resetForm(formName){
	document.forms[formName].reset();
}

function clearValue(obj,type){
	if (obj.value == obj.defaultValue && !type) {
		obj.value = "";
	}
	if (obj.value == "" && type==1) {
		obj.value = obj.defaultValue
	}
}

