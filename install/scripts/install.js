$(function() {
	$("#dbform").bind('submit', function(ev) {
		ev.preventDefault();
		$('#continue').attr('disabled', 'disabled');
		$('#continue').attr('value', 'Please Wait');
		
		$.post('install.php', $(this).serialize(), function(data) {
			$('#continue').attr('disabled', false);
			$('#continue').attr('value', 'Continue');
			$("#upper_content").html(data);
		});
	});
});