var itemMarkup = '<li id="{4}" class="collection-item avatar">\
						<div class="left-align" style="float: left;">\
							<i class="material-icons circle">folder</i>\
							<span id="item_name" class="title">{0}</span>\
							<p>Lent to: <span id="lendee">{1}</span>\
								<br><span id="extra" class="{3}">{2}</span>\
							</p>\
						</div>\
						<div class="right-align">\
							<a id="checkin_button" class="waves-effect waves-light btn {5}" title="Checkin" onclick="checkinItem(this);"><i class="medium material-icons">done</i></a>\
							<a id="edit_button" class="waves-effect waves-light btn {5}" title="Edit" onclick="editItem(this)"><i class="medium material-icons">mode_edit</i></a>\
							<a class="waves-effect waves-light btn" title="Delete" onclick="deleteItem(this);"><i class="medium material-icons">delete</i></a>\
						</div>\
					</li>';

var turnedInStyle = "green-text darken-3-text";
var expiredStyle = "red-text accent-4-text";

function isExpired(date) {
    return Date.today().compareTo(Date.parse(date)) == 1;
}

function formatDate(date) {
    return Date.parse(date).toString("dddd MMMM dS, yyyy");
}

function addItem(itemName, lendee, date, id, checkedIn) {
	var formattedDate = formatDate(date);
    var spanText = "";
    var spanClass = "";
    var expired = false;
    var hidden = "";
    
	if (checkedIn) {
        spanClass = turnedInStyle;
        spanText = "Turned in!";
        expired = false;
        hidden = "hide";
    } else if (isExpired(date)) {
		spanClass = expiredStyle;
        spanText = 'Date due: <span id="due_date">' + formattedDate + "</span>";
        expired  = true;
	} else {
        spanText = 'Date due: <span id="due_date">' + formattedDate + "</span>";
        expired = false;
    }
    
	var item = itemMarkup.format(itemName, lendee, spanText, spanClass, id, hidden);
    
    if (checkedIn) {
        $("#turnedInItems").prepend(item);
    } else if (expired) {
		$("#expiredItems").prepend(item);
	} else {
		console.log("Prepending!");
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

$(document).ready(function () {
	getItems();
    
	function getItems() {
		$.ajax({
			url: "includes/inventory/get_items.php"
			, type: "POST"
			, dataType: "json"
			, success: function (data) {
				data.forEach(function (entry) {
					addItem(entry.item_name, entry.lendee, entry.date, entry.id, entry.checked_in);
				});
			}
			, fail: function (data) {
				console.log("Fail!");
				console.log(data);
			}
		});
	}

    $("#submit_edit").click(function() {
        var formData = $("#edit_form").serializeArray();
        var id = $("#edit_id").val();
        var itemName = formData[0]["value"];
        var lendee = formData[1]["value"];
        var date = formData[2]["value"];
        
        $.ajax({
            url: "../includes/inventory/edit_item.php",
            type: "POST",
            data: {id: id, item_name: itemName, lendee: lendee, date: date},
            success: function(data) {
                var li = $("#" + id);
                var oldExpired = isExpired(li.find("#due_date").text());
                var newExpired = isExpired(date);
                
                li.find("#item_name").val(itemName);
                li.find("#lendee").val(lendee)
                
                if (oldExpired != newExpired) {
                    if (newExpired) {
                        var extra = li.find("#extra");
                        
                        extra.removeClass();
                        extra.addClass(expiredStyle);
                        li.find("#due_date").text(formatDate(date));
                        li.detach().prependTo("#expiredItems");
                    } else {
                        var extra = li.find("#extra");
                        
                        extra.removeClass();
                        li.find("#due_date").text(formatDate(date));
                        li.detach().prependTo("#items");
                    }
                }
                
                $('#edit_modal').closeModal();
            },
            fail: function(data) {
                console.log("Fail");
                console.log(data);
            }
        });
    });
    
	if (!String.prototype.format) {
		String.prototype.format = function () {
			var args = arguments;
			return this.replace(/{(\d+)}/g, function (match, number) {
				return typeof args[number] != 'undefined' ? args[number] : match;
			});
		};
	}
});