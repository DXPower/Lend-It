<?php
include_once 'includes/db_connect.php';
include_once 'includes/logging.php';
session_start();

if (isLoggedIn($mysqli) === true) {
    header('Location: home.php');
}
?>

	<!DOCTYPE html>
	<html>

	<head>
		<title>Lend-It</title>
		<script src="https://apis.google.com/js/platform.js" async defer></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="js/sha512.js"></script>
        <script src="js/logging.js"></script>
		<script src="js/user_cookie.js"></script>
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
		<link rel="stylesheet" href="css/main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                    <div class="row hide" id="login_error">
                        <div class="input-field col s12">
                            <span class="red-text text-darken-4" style="margin-down: 30px;">Incorrect email/password</span>
                        </div>
                    </div>
					<div class="row">
						<div class="input-field col s12">
							<input id="login_email" placeholder="Email" id="email" type="email" class="validate" autocomplete="on">
							<label for="email">Email</label>
						</div>
					</div>
					<div class="row">
						<div class="input-field col s12">
							<input id="login_password" placeholder="Password" id="password" type="password" class="validate" autocomplete="on">
							<label for="password">Password</label>
						</div>
					</div>
					<div class="row">
						<div class="col s8">
							<p class="left"><a class="modal-trigger" href="#signup">New? Create an Account.</a></p>
						</div>
						<div class="col s4">
							<div id="login" class="btn right grey darken-1">
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
								<input id="register_email" placeholder="Email" id="email" type="email" class="validate" autocomplete="off">
								<label for="email">Email <span id="register_email_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Invalid email address</span></label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input id="register_password" placeholder="Password" id="password" type="password" class="validate" autocomplete="off">
								<label for="password">Password <span id="register_password_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Password is too short (min. 8 characters)</span></label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input id="register_password2" placeholder="Confirm Password" id="password2" type="password" class="validate" autocomplete="off">
								<label for="password2">Confirm Password <span id="register_match_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Passwords do not match</span></label>
								<label for="password2">Confirm Password <span id="register_match_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Passwords do not match</span></label>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<a id="register_submit" class="modal-action modal-close waves-effect waves-green btn-flat">Register</a>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script>
			$(document).ready(function () {
				$('.modal-trigger').leanModal();
			});
		</script>
	</body>

	</html>