function authenticateToken(token) {
	console.log("authenticateToken");
	$.ajax({
		url: "../includes/id_token_auth.php",
		type: "POST",
		data: {id_token: token},
		success: function(data) {
			console.log("Success!");
			console.log(data);
			window.location.href = "../home.php";
		}
	}).fail(function(data) {
			console.log("Fail!");
			console.log(data); 
	});
}