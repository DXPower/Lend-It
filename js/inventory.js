/*jshint multistr: true */
var itemMarkup = '<li id="{4}" class="collection-item avatar" style="display: flex;">\
						<div class="left-align" style="float: left;">\
                            {6}\
							<span id="item_name" class="title">{0}</span>\
							<p>Lent to: <span id="lendee">{1}</span>\
                                {7}\
                                {8}\
								<br><span id="extra" class="{3}">{2}</span>\
							</p>\
						</div>\
						<div class="right-align" style="margin-left: auto;">\
							<a id="checkin_button" class="waves-effect waves-light btn {5}" title="Checkin" onclick="checkinItem(this);"><i class="medium material-icons">done</i></a>\
							<a id="edit_button" class="waves-effect waves-light btn {5}" title="Edit" onclick="editItem(this)"><i class="medium material-icons">mode_edit</i></a>\
							<a class="waves-effect waves-light btn" title="Delete" onclick="deleteItem(this);"><i class="medium material-icons">delete</i></a>\
						</div>\
					</li>';

var turnedInStyle = "green-text darken-3-text";
var expiredStyle = "red-text accent-4-text";

function formatPhoneNumber(number) {
    return number.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
}

function isExpired(date, compareTo) {
    try {
        return Date.parse(compareTo).compareTo(Date.parse(date)) === 1;
    } catch (e) {
        return false;    
    }
}

function formatDate(date) {
    return Date.parse(date).toString("dddd MMMM dS, yyyy");
}

function addItem(data) {
    var itemName = data.item_name, lendee = data.contact.name, date = data.date, phoneNumber = data.contact.phone_number, email = data.contact.email, image = data.image, id = data.id, checkedIn = data.checked_in;
	var formattedDate = formatDate(date);
    var spanText = "";
    var spanClass = "";
    var expired = false;
    var hidden = "";
    var img = '<i class="material-icons circle">folder</i>';
    var number = "";
    var em = "";
    
    // The following if statements make changes to the formatting and text of each list item depending on expiration date/checked in and stuff
    
	if (checkedIn) {
        spanClass = turnedInStyle;
        spanText = "Turned in!";
        expired = false;
        hidden = "hide";
    } else if (isExpired(date, Date.today())) {
		spanClass = expiredStyle;
        spanText = 'Date due: <span id="due_date">' + formattedDate + "</span>";
        expired  = true;
	} else {
        spanText = 'Date due: <span id="due_date">' + formattedDate + "</span>";
        expired = false;
    }
    
    if (phoneNumber) {
        number = '<br><span id="number">Phone Number: ' + formatPhoneNumber(phoneNumber) + '</span>';
    }
    
    if (email) {
        em = '<br><span id="e-mail">Email: ' + email + '</span>';
    }
    
    if (image) {
        img = '<img class="circle" width="50px" height="50px" src="img/uploads/' + image + '"/>';
    }
    
	var item = itemMarkup.format(itemName, lendee, spanText, spanClass, id, hidden, img, number, em);
    
    if (checkedIn) {
        $("#turnedInItems").prepend(item);
    } else if (expired) {
		$("#expiredItems").prepend(item);
	} else {
		$("#items").prepend(item);
	}
}

function deleteItem(item) {
    var li = $(item).closest("li");
    
    $.ajax({
        url: "../includes/inventory/delete_item.php",
        type: "POST",
        data: {id: li.attr("id")},
        success: function(data) {
            li.fadeOut();
            itemsChanged = true;
            getLog(0);
        }, fail: function(data) {
            console.log("Fail!");
            console.log(data);
        }
    });
}

function checkinItem(item) {
    var li = $(item).closest("li");
    var span = li.find("#extra");
    
    $.ajax({
        url: "../includes/inventory/checkin_item.php",
        type: "POST",
        data: {id: li.attr("id")},
        success: function(data) {
            $.when(li.fadeOut(250)).done(function() { // When the fade out is complete, move it to the other list and fade it in.
                li.detach().prependTo($("#turnedInItems"));
                span.text("Turned in!");
                span.removeClass();
                span.addClass(turnedInStyle);
                li.find("#checkin_button").addClass("hide");
                li.find("#edit_button").addClass("hide");
                li.fadeIn(250);
                
                itemsChanged = true;
                getLog(0);
            });
        },
        fail: function(data) {
            console.log("Failure!");
            console.log(data);
        }
    });
}

function editItem(item) {
    var li = $(item).closest("li");
    
    $("#edit_id").val(li.attr("id"));
    $("#edit_item_name").val(li.find("#item_name").text());
    $("#edit_lendee").val(li.find("#lendee").text());
    setDate(Date.parse(li.find("#due_date").text()));
    $('#edit_modal').openModal();
}

function getItems() {
    $.ajax({
        url: "includes/inventory/get_items.php"
        , type: "POST"
        , dataType: "json"
        , success: function (data) {
            $("#turnedInItems").empty();
            $("#expiredItems").empty()
            $("#items").empty();

            data.forEach(function (entry) {
                addItem(entry);
            });
        }
        , fail: function (data) {
            console.log("Fail!");
            console.log(data);
        }
    });
}

function validateEditItem() {
    var valid = true;
    
    if ($("#edit_item_name").val() == "") {
        $("#edit_item_name_error").removeClass("hide");
        valid = false;
    } else {
        $("#edit_item_name_error").addClass("hide");
    }
    
    if ($("#edit_date").val == "") {
        $("#edit_date_error").removeClass("hide");
        valid = false;
    } else {
        $("#edit_date_error").removeClass("hide");
    }
    
    return valid;
}

$(document).ready(function () {
	getItems();

    $("#submit_edit").click(function() {
        if (!validateEditItem()) return;
        
        var formData = $("#edit_form").serializeArray();
        var id = $("#edit_id").val();
        var itemName = formData[0]["value"];
        var date = formData[1]["value"];
        
        $.ajax({
            url: "../includes/inventory/edit_item.php",
            type: "POST",
            data: {id: id, item_name: itemName, date: date},
            success: function(data) {
                var li = $("#inv #" + id);
                var oldExpired = isExpired(li.find("#due_date").text(), Date.today());
                var newExpired = isExpired(date, Date.today());
                var extra = li.find("#extra");
                
                li.find("#item_name").val(itemName);
                
                // Moves to either the expired items or normal items depeneding on the changed expiration date
                if (oldExpired != newExpired) { 
                    if (newExpired) {
                        extra.removeClass();
                        extra.addClass(expiredStyle);
                        li.find("#due_date").text(formatDate(date));
                        li.detach().prependTo("#expiredItems");
                    } else {
                        extra.removeClass();
                        li.find("#due_date").text(formatDate(date));
                        li.detach().prependTo("#items");
                    }
                }
                
                $('#edit_modal').closeModal();
                getLog(0);
            },
            fail: function(data) {
                console.log("Fail");
                console.log(data);
            }
        });
    });
    
    // If the String.format function doesn't exist (used heavily to put information into the HTML snippets for the inventory, log, and contacts)
	if (!String.prototype.format) {
		String.prototype.format = function () {
			var args = arguments;
			return this.replace(/{(\d+)}/g, function (match, number) {
				return typeof args[number] != 'undefined' ? args[number] : match;
			});
		};
	}
});