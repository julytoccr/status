$(window).load(function(){
	$('#flash_message').each(function(i, e){
		setTimeout(
			function(){
				$(e).slideUp();
			},
			5000
		);
	});
});
