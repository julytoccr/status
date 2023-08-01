



function cambiar_lenguaje_excel(cont_ses_lenguajes, pagina){
	
	var lenguaje=document.getElementById('LenguajeId').options[document.getElementById('LenguajeId').selectedIndex].value;
	
	
	var url='/lenguajes/alta_excel_lenguajes/'+cont_ses_lenguajes+'/'+lenguaje+'/?page='+pagina;
	
	location.href=url;
}




function cambiar_lenguaje_excel_nuevos_terminos(cont_ses_terminos,pagina){
	
	var lenguaje=document.getElementById('LenguajeId').options[document.getElementById('LenguajeId').selectedIndex].value;
	
	
	var url='/textos/alta_excel_nuevos_terminos/'+cont_ses_terminos+'/'+lenguaje+'/?page='+pagina;
	
	location.href=url;
}




function cambiar_lenguaje_buscador(cont_ses_buscador, busqueda, tipobusqueda, pagina){
	
	var lenguaje=document.getElementById('LenguajeId').options[document.getElementById('LenguajeId').selectedIndex].value;
	
	
	var url='/textos/buscador_post/'+cont_ses_buscador+'/'+busqueda+'/'+tipobusqueda+'/'+lenguaje+'/?page='+pagina;
	
	location.href=url;
}
