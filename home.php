<?php 
include_once 'includes/db_connect.php';
include_once 'includes/logging.php';
session_start();
?>

	<!DOCTYPE html>
	<html>

	<head>
		<title>Lend-It</title>
		<script src="https://apis.google.com/js/platform.js" async defer></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
		<script src="http://www.datejs.com/build/date.js" type="text/javascript"></script>
		<script src="js/id_token_auth.js"></script>
		<script src="js/inventory.js"></script>
		<script src="js/checkout.js"></script>
		<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="css/main.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="google-signin-client_id" content="743404233548-3nabjs8t7ah209r11fhm6ha2u82ui7kg.apps.googleusercontent.com">
		<script>
			gapi.load('auth2', function () {
				gapi.auth2.init();
			});

			function onSignIn(googleUser) {
				var id_token = googleUser.getAuthResponse().id_token;
				authenticateToken(id_token);
				
				window.location.href = "http://inventory.nerdchill.com/home.php";
			}
			
			function signOut() {
				var auth2 = gapi.auth2.getAuthInstance();
				auth2.signOut().then(function () {
					console.log('User signed out.');
					window.location.href = "includes/logout.php";
				});
				
			//	window.location.href = "includes/logout.php";
			}
		</script>
	</head>

	<body>
		<div class="navbar-fixed">
			<nav class="teal accent-4">
				<div class="container">
				<div class="nav-wrapper teal accent-4">
					<a href="index.php" class="brand-logo center">Lend-It</a>
					<a class="logout" href="#" onclick="signOut();"><i class="medium material-icons">input</i></a>
				</div>
				</div>
				<div class="fixed-top" style="border: 0px; border-bottom: 1px; border-color: grey; border-style: solid">
					<ul class="tabs">
						<li class="tab col s3">
							<a class="active" href="#inv">
								<i class="medium material-icons">view_list</i>
							</a>
						</li>
						<li class="tab col s3">
							<a href="#check-out">
								<i class="medium material-icons">done</i>
							</a>
						</li>
						<li class="tab col s3">
							<a href="#calendar">
								<i class="medium material-icons">perm_contact_calendar</i>
							</a>
						</li>
						<li class="tab col s3">
							<a href="#contact">
								<i class="medium material-icons">contact_phone</i>
							</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<div class="container push-down-top">
			<div id="inv" class="col s12">
				<h5 class="center-align">Inventory</h5>
				<ul id="expiredItems" class="collection">
					
				</ul>
				<ul id="items" class="collection">
					
				</ul>
                <ul id="turnedInItems" class="collection">
					
				</ul>
			</div>
			<div id="check-out" class="col s12">
				<h5 class="center-align">Check Out</h5>
				<div class="container push-down-top">
					<div class="row" style="margin-top: -25px;">
						<form id="checkout_form" class="col s12">
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Item Name" id="item_name" type="text" class="validate" name="item_name" required>
									<label for="item_name">Item Name</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Lendee" id="lendee" type="text" class="validate" name="lendee" required>
									<label for="lendee">Lendee</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Due Date" id="date" type="date" class="datepicker validate" name="date" required>
									<label for="date">Date to be Returned</label>
								</div>
							</div>
							<div class="row">
								<div class="btn right grey darken-1 right-align" style="float-right">
									<span id="checkout_button" class="right-align">Checkout</span>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div id="calendar" class="col s12">
					<h5 class="center-align">Calendar</h5>
				</div>
				<div id="contact" class="col s12">
					<h5 class="center-align">Contact</h5>
					<ul class="collection">
						<li class="collection-item avatar">
							<i class="material-icons circle">folder</i>
							<span class="title">Title</span>
							<p>First Line
								<br> Second Line
							</p>
						</li>
						<li class="collection-item avatar">
							<i class="material-icons circle">folder</i>
							<span class="title">Title</span>
							<p>First Line
								<br> Second Line
							</p>
						</li>
						<li class="collection-item avatar">
							<i class="material-icons circle">folder</i>
							<span class="title">Title</span>
							<p>First Line
								<br> Second Line
							</p>
						</li>
						<li class="collection-item avatar">
							<i class="material-icons circle">folder</i>
							<span class="title">Title</span>
							<p>First Line
								<br> Second Line
							</p>
						</li>
					</ul>
				</div>
			</div>

            <div id="edit_modal" class="modal">
				<div class="modal-content">
					<h4>Edit Item</h4>
					<form id="edit_form" class="col s12">
						<input id="edit_id" type="hidden" value="-1">
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Item Name" id="edit_item_name" type="text" class="validate" name="item_name" required>
									<label for="item_name">Item Name</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Lendee" id="edit_lendee" type="text" class="validate" name="lendee" required>
									<label for="lendee">Lendee</label>
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<input placeholder="Due Date" id="edit_date" type="date" class="datepicker validate" name="date" required>
									<label for="date">Date to be Returned</label>
									<script>
										$('.datepicker').pickadate({
											selectMonths: true, // Creates a dropdown to control month
											selectYears: 15, // Creates a dropdown of 15 years to control year
										});
                                        
                                        function setDate(date) {
                                            $('#edit_date').pickadate().pickadate('picker').set('select', date);
                                        }
									</script>
								</div>
							</div>
							<div class="row">
								<div class="btn right grey darken-1" style="float:right;">
									<span id="submit_edit">Edit</span>
								</div>
							</div>
						</form>
				</div>
				<div class="modal-footer">
				</div>
			</div>
			<script>
				$(document).ready(function () {
					$('ul.tabs').tabs();
				});
			</script>
	</body>

	</html>