function is_integer(number) {
     var reInteger = /^\d+$/
     return reInteger.test(number)
}

function incrementar(campo)
{
	if (is_integer(campo.value))
	{
		campo.value=parseInt(campo.value)+1;
	}
	else
	{
		campo.value=1;
	}
}
