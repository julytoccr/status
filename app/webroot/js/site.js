$(function(){
	$('a.bloqueslink').click(function(){
		var link = $(this);

		$.post({
			url: link.attr('href'), 
			data: $("#BloqueProblemasForm").closest("form").serialize(),
			success: function(data){
				$("#aux").html(data);
			}
		});

		return false;
	});
});
