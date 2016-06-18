<?php 
include_once 'includes/db_connect.php';
include_once 'includes/logging.php';
session_start();

if (isLoggedIn($mysqli) === false) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Lend-It</title>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
        <script src="http://www.datejs.com/build/date.js" type="text/javascript"></script>
        <script>
            itemsChanged = true;
        </script>
        <script src="js/id_token_auth.js"></script>
        <script src="js/inventory.js"></script>
        <script src="js/checkout.js"></script>
        <script src="js/log.js"></script>
        <script src="js/contacts.js"></script>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script>
            $(document).ready(function () {
                $('ul.tabs').tabs();

                var tab = "<?php echo $_GET["tab"]; ?>";
                console.log(tab);
                if (tab == "inventory") {
                    $('ul.tabs').tabs('select_tab', 'inv');
                } else if (tab == "checkout") {
                    $('ul.tabs').tabs('select_tab', 'check-out');
                } else if (tab == "log") {
                    $('ul.tabs').tabs('select_tab', 'log');
                    if (itemsChanged) getLog();
                } else if (tab == "contacts") {
                    $('ul.tabs').tabs('select_tab', 'contact');
                }
            });

            function setTab(tab) {
                history.pushState(null, null, '?tab=' + tab);
            }
            
            function signOut() {
                window.location.href = "includes/logout.php";
            }
        </script>
    </head>

    <body>
        <div class="navbar-fixed">
            <nav class="teal accent-4">
                <div class="container">
                    <div class="nav-wrapper teal accent-4">
                        <a href="home.php" class="brand-logo center">Lend-It</a>
                        <div class="btn logout" onclick="signOut();" style="background-color:transparent; box-shadow: none; display:inline-block; margin: 0; padding: 0; max-height: 64px;">
                            <p style="display: inline-block; margin: 0; padding: 0;">
                                <i class="medium material-icons" style="display:inline-block;">input</i><span class="hide-on-small-only" style="display:inline-block;line-height:64px;margin-top:-10px; position:relative;top:-10px;padding-left:10px;">Sign Out</span>
                            </p>
                        </div>
                        <!-- <a class="logout" href="#" onclick="signOut();"><i class="medium material-icons">input</i></a> -->
                    </div>
                </div>
                <div class="fixed-top" style="border: 0px; border-bottom: 1px; border-color: grey; border-style: solid">
                    <ul class="tabs">
                        <li class="tab col s3" href="#inventory">
                            <a class="active" href="#inv" onclick='setTab("inventory");'>
                                <i class="medium material-icons">view_list</i>
                            </a>
                        </li>
                        <li class="tab col s3">
                            <a href="#check-out" onclick='setTab("checkout");'>
                                <i class="medium material-icons">done</i>
                            </a>
                        </li>
                        <li class="tab col s3">
                            <a href="#contact" onclick='setTab("contacts");'>
                                <i class="medium material-icons">contact_phone</i>
                            </a>
                        </li>
                        <li class="tab col s3">
                            <a href="#log" onclick='setTab("log");'>
                                <i class="medium material-icons">receipt</i>
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
                            <input id="checkout_contact_id" type="hidden" name="contact_id" value="-1">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input placeholder="Item Name" id="checkout_item_name" type="text" class="validate" name="item_name" maxlength="255" required>
                                    <label for="item_name">Item Name  <span id="item_name_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <!-- 							    <select style="display:block;">
							      <option value="" disabled selected>Existing Contacts</option>
							      <option value="1">Name - Number - Email</option>
							      <option value="2">Name - Number - Email</option>
							      <option value="3">Name - Number - Email</option>
							    </select>
							    <label style="margin-top:-40px;">Returning Borrower?</label> -->
                                    <div id="contact_card" class="card hide" style="width: 75%;">
                                        <div class="card-content">
                                            <span class="card-title">Contact Information</span>
                                            <p><span id="info"></span></p>
                                        </div>
                                        <div class="card-action">
                                            <a id="change_contact" class="blue-text text-darken-2 right-align" href="#change">Change</a>
                                        </div>
                                    </div>
                                    <div id="search_holder">
                                        <input placeholder="Search" id="lendee_search" type="text" class="validate" name="lendee" autocomplete="off" maxlength="255" >
                                        <label for="lendee">Lendee  <span id="contact_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                                        <ul id="search-results" class="search-results hide">
                                            <li id="create_contact" class="result"><span>Create Contact</span></li>
                                            <div id="results">

                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="checkout_date" type="date" class="datepicker validate active" name="date" required>
                                    <label id="checkout_date_label" for="checkout_date" style="">Date to be Returned  <span id="date_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                                    <script>
                                        $('#checkout_date').pickadate({
                                            onSet: function (dateText) {
                                                if (dateText == undefined) $("#checkout_date_label").removeClass("hide");
                                                else $("#checkout_date_label").addClass("hide");
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <span id="image_size_error" class="red-text text-darken-4 hide">Image cannot be more than 2MB<br></span>
                            <span id="image_ext_error" class="red-text text-darken-4 hide">Only .png or .jpg extensions are accepted</span>
                            <div class="file-field input-field">
                                <div class="btn grey">
                                    <span>Item Picture</span>
                                    <input class="active" type="file" id="checkout_image" name="checkout_image">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text">
                                </div>
                            </div>



                            <div class="row">
                                <div class="right-align">
                                    <span id="uploading" class="right-align hide" style="margin: 10px;">Uploading...</span>
                                    <div class="btn right grey darken-1" id="checkout_button" style="float-right">
                                        <span class="right-align">Checkout</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="log" class="col s12">
                <h5 class="center-align">Log</h5>
                <table id="log_table" class="responsive-table centered bordered highlight">
                    <thead>
                        <th data-field="name">Item Name</th>
                        <th data-field="lendee" style="width: 300px">Lendee</th>
                        <th data-field="date_lent">Date Lent</th>
                        <th data-field="date_due">Date Due</th>
                        <th data-field="checked_in">Checked in</th>
                    </thead>
                    <tbody id="log_table_body">

                    </tbody>
                </table>
                <div class="right-align">
                    <a id="previous_log_button" style="margin: 20px;" class="waves-effect waves-light btn"><i class="material-icons left">navigate_before</i>Back</a>
                    <a id="next_log_button" style="margin: 20px;" class="waves-effect waves-light btn"><i class="material-icons right">navigate_next</i>Next</a>
                </div>
            </div>
            <div id="contact" class="col s12">
                <h5 class="center-align">Contact</h5>
                <ul id="contacts" class="collection">

                </ul>
            </div>
            <div id="edit_contact_modal" class="modal">
                <div class="modal-content">
                    <h4>Edit Contact</h4>
                    <form id="edit_contact_form" class="col s12">
                        <input id="edit_contact_id" type="hidden" value="-1">
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Name" id="edit_contact_name" type="text" class="validate" name="name" maxlength="255" required>
                                <label for="name">Name <span id="edit_name_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Phone Number" id="edit_number" type="tel" class="validate" name="phone_number" maxlength="255" onkeypress='return numbersOnly(event)' required>
                                <label for="phone_number">Phone Number <span id="edit_number_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Email" id="edit_email" type="text" class="validate" name="email" maxlength="255" required>
                                <label for="email">Email <span id="edit_email_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btn right grey darken-1" id="submit_edit_contact" style="float:right;">
                                <span>Edit</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="create_contact_modal" class="modal">
                <div class="modal-content">
                    <h4>Create Contact</h4>
                    <form id="create_contact_form" class="col s12">
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Name" id="create_name" type="text" class="validate" name="name" maxlength="255" required>
                                <label for="name">Name <span id="name_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Phone Number" id="create_number" type="tel" class="validate" name="phone_number" maxlength="11" onkeypress='return numbersOnly(event)' required>
                                <label for="phone_number">Phone Number <span id="number_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Email" id="create_email" type="text" class="validate" name="email" maxlength="255" required>
                                <label for="email">Email <span id="email_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btn right grey darken-1" id="submit_create_contact" style="float:right;">
                                <span>Create</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="edit_modal" class="modal">
                <div class="modal-content">
                    <h4>Edit Item</h4>
                    <form id="edit_form" class="col s12">
                        <input id="edit_id" type="hidden" value="-1">
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Item Name" id="edit_item_name" type="text" class="validate" name="item_name" maxlength="255" required>
                                <label for="item_name">Item Name <span id="edit_item_name_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input placeholder="Due Date" id="edit_date" type="date" class="datepicker validate" name="date" required>
                                <label id="edit_date_label" for="date">Date to be Returned <span id="edit_date_error" class="red-text text-darken-4 hide" style="margin-left: 30px;">Required</span></label>
                                <script>
                                    $('.datepicker').pickadate({
                                        onSet: function (dateText) {
                                            if (dateText == undefined) $("#edit_date_label").removeClass("hide");
                                            else $("#edit_date_label").addClass("hide");
                                        }
                                    });

                                    function setDate(date) {
                                        $('#edit_date').pickadate().pickadate('picker').set('select', date);
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btn right grey darken-1" id="submit_edit" style="float:right;">
                                <span>Edit</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </body>

</html>