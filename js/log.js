/*jshint multistr: true */
var logMarkup = '<tr id="{0}">\
					<td>{1}</td>\
					<td>\
                        <div class="switcher" style="width: 300px; height: 100%; margin: auto; cursor: pointer; cursor: hand;" class="center-align">\
                            <div class="base">\
                                <a id="log_contact_name" class="trigger center-align" href="#show" style="margin: 0px;" onclick="onTriggerClick(this)">{2}</a>\
                            </div>\
                            <div class="other hide" onclick="onTriggerClick(this)">\
                                <span id="log_contact_name" class="center-align" style="margin: 0px;">{2}</span>\
                                <br><span style="margin: 0px; font-size: .9em;" class="center-align"><span id="card_phone" style="margin: 0px;">{7}</span> - <span id="card_email" style="margin: 0px;">{8}</span></span>\
                            </div>\
                        </div>\
                    </td>\
					<td>{3}</td>\
					<td>{4}</td>\
					<td><span class="{5}">{6}</td>\
				</tr>';

var logTurnedInStyle = "green-text darken-3-text";
var logExpiredStyle = "red-text accent-4-text";
var logIndex = 0;
var openedContact = null;

function formatLogDate(date) {
	return date;
}

function addLogItem(item) {
	var checkedInClass = "";
	var checkedInText = "";
	
	if (isExpired(item.date_expired, item.date_checked_in)) {
		checkedInClass = logExpiredStyle;
	} else if (item.checked_in) {
		checkedInClass = logTurnedInStyle;
	} else if (isExpired(item.date_expired, Date.today())) {
        checkedInClass = logExpiredStyle;           
    }
	
	if (!item.checked_in) {
		checkedInText = "Not Checked in";
	} else {
		checkedInText = "Checked in " + formatLogDate(item.date_checked_in); 
	}
	
	var itemHTML = logMarkup.format(item.id, item.item_name, item.contact.name, formatLogDate(item.date_created), formatLogDate(item.date_expired), checkedInClass, checkedInText, formatPhoneNumber(item.contact.phone_number), item.contact.email);
	
	$("#log_table_body").append(itemHTML);
}

function getLog(start) {
	"use strict";
    var index = Math.max(start, 0); // We don't want a negative index.

    $.ajax({
        url: "../includes/inventory/get_log.php",
        type: "POST",
        data: {start: start},
        dataType: "json",
        success: function(data) {
            if (Object.keys(data).length > 0) {
                $("#log_table_body").empty();

                if (data.last && data.last === true) {
                    $("#previous_log_button").addClass("disabled");
                } else {
                    $("#previous_log_button").removeClass("disabled");
                }

                if (data.first && data.first === true) {
                    $("#next_log_button").addClass("disabled");
                } else {
                    $("#next_log_button").removeClass("disabled");
                }

                delete data.first;
                delete data.last;

                for (let entry in data) {
                    if (!data.hasOwnProperty(entry)) continue;
                    addLogItem(data[entry]);
                }

                logIndex = start;
                itemsChanged = false;
            }
        },
        fail: function(data) {
            console.log("Fail!");
            console.log(data);
        }
    });
}

// Will show/hide extra information about each contact on the log page, but only one at a time
function onTriggerClick(o) {
    var switcher = $(o).parents(".switcher");
    
    if (openedContact != null && openedContact.get(0) === switcher.get(0)) {
        switcher.children(".base").removeClass("hide");
        switcher.children(".other").addClass("hide");
        openedContact = null;
        
        console.log("Same");
    } else if (openedContact != null) {
        openedContact.children(".base").removeClass("hide");
        openedContact.children(".other").addClass("hide");
        
        switcher.children(".base").addClass("hide");
        switcher.children(".other").removeClass("hide");
        openedContact = switcher;
    } else {
        switcher.children(".base").addClass("hide");
        switcher.children(".other").removeClass("hide");
        openedContact = switcher;
    }
}

$(document).ready(function() {
	getLog(logIndex);
	
	$("#next_log_button").click(function() {
        if (!$(this).hasClass("disabled")) {
            itemsChanged = true;
            getLog(logIndex + 10);
        }
	});
	
	$("#previous_log_button").click(function() {
        if (!$(this).hasClass("disabled")) {
            itemsChanged = true;
            getLog(logIndex - 10);
        }
	});
});