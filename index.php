<?php
include_once 'includes/logging.php';
session_start();

if (isLoggedIn()) {
	//header("Location: http://www.inventory.nerdchill.com/home.php");
}
?>

	<!DOCTYPE html>
	<html>

	<head>
		<title>Lend-It</title>
		<script src="https://apis.google.com/js/platform.js" async defer></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="js/user_cookie.js"></script>
		<script src="js/id_token_auth.js"></script>
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
		<link rel="stylesheet" href="css/main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="google-signin-client_id" content="743404233548-3nabjs8t7ah209r11fhm6ha2u82ui7kg.apps.googleusercontent.com">
		<script>
			function onSignIn(googleUser) {
				var id_token = googleUser.getAuthResponse().id_token;
				authenticateToken(id_token);

			//	window.location.href = "http://inventory.nerdchill.com/home.php";
			}

			function signOut() {
				var auth2 = gapi.auth2.getAuthInstance();
				auth2.signOut().then(function () {
					console.log('User signed out.');
				});
			}
		</script>
	</head>

	<body>
		<nav>
			<div class="nav-wrapper teal accent-4">
				<a href="index.php" class="brand-logo center">Lend-It</a>
			</div>
		</nav>
		<div class="container push-down-top">
			<h4>Login</h4>
			<div class="row">
				<form class="col s12">
					<div class="row">
						<div class="g-signin2" data-onsuccess="onSignIn"></div>


						<div class="input-field col s12">
							<input placeholder="Email" id="email" type="email" class="validate">
							<label for="email">Email</label>
						</div>
					</div>
					<div class="row">
						<div class="input-field col s12">
							<input placeholder="Password" id="password" type="password" class="validate">
							<label for="password">Password</label>
						</div>
					</div>
					<div class="row">
						<div class="col s8">
							<p class="left"><a class="modal-trigger" href="#signup">New? Create an Account.</a></p>
						</div>
						<div class="col s4">
							<div class="btn right grey darken-1">
								<span>Login</span>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div id="signup" class="modal">
				<div class="modal-content">
					<h4>Sign Up</h4>
					<form class="col s12">
						<div class="row">
							<div class="input-field col s12">
								<input placeholder="Email" id="email" type="email" class="validate">
								<label for="email">Email</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input placeholder="Password" id="password" type="password" class="validate">
								<label for="password">Password</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input placeholder="Confirm Password" id="password2" type="password" class="validate">
								<label for="password2">Confirm Password</label>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Create</a>
				</div>
			</div>
			<a href="#" onclick="signOut();">Sign out</a>
			<script>
			</script>
		</div>




		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script>
			$(document).ready(function () {
				$('.modal-trigger').leanModal();
			});
		</script>
	</body>

	</html>