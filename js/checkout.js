var resultMarkup = '<li id="{0}" class="result" onclick="clickResult({0});">\
                        <span>{1} - {2} - {3}</span>\
                    </li>';
var lendeeFocused = false;

function setContact(id, name, number, email) {
    $("#contact_card #info").text(name + " - " + number + " - " + email)
    $("#contact_card").removeClass("hide");
    $("#checkout_contact_id").val(id);
    $("#search-results").addClass("hide");
    $("#lendee_search").val("");
    $("#results").empty();
    $("#search_holder").addClass("hide");
}

function clickContact(id, info) {
    $("#contact_card #info").text(info)
    $("#contact_card").removeClass("hide");
    $("#checkout_contact_id").val(id);
    $("#search-results").addClass("hide");
    $("#lendee_search").val("");
    $("#results").empty();
    $("#search_holder").addClass("hide");
}

function listContact(contact) {
    var result = resultMarkup.format(contact.id, contact.name, formatPhoneNumber(contact.phone_number), contact.email);
    
    $("#results").append(result);
}

function clickResult(id) {
    clickContact(id, $("#" + id + " span").text());
}

function validateCheckout() {
    var valid = true;
    
    if ($("#checkout_item_name").val() == "") {
        $("#item_name_error").removeClass("hide");
        valid = false;
    } else {
        $("#item_name_error").addClass("hide");
    }

    if ($("#checkout_contact_id").val() <= -1) {
        $("#contact_error").removeClass("hide");
        valid = false;
    } else {
        $("#contact_error").addClass("hide");
    }
    
    if ($("#checkout_date").val() == "") {
        $("#date_error").removeClass("hide");
        valid = false;
    } else {
        $("#date_error").addClass("hide");
    }
    
    if (!validateImage) valid = false;
    
    return valid;
}

function validateContact() {
    var valid = true;
    
    if($("#create_name").val() == "") {
        $("#name_error").removeClass("hide");
        valid = false;
    } else {
        $("#name_error").addClass("hide");
    }
    
    if ($("#create_number").val() == "") {
        $("#number_error").removeClass("hide");
        valid = false;
    } else {
        $("#number_error").addClass("hide");
    }
    
    if ($("#create_email").val() == "") {
        $("#email_error").removeClass("hide");
        valid = false;
    } else {
        $("#email_error").addClass("hide");
    }
    
    return valid;
}

function validateImage() {
    var valid = true;
    
    if ($("#checkout_image")[0].files.length > 0) {
        // Image cannot be more than 2MB
        if ($("#checkout_image")[0].files[0].size > 2097152) {
            $("#image_size_error").removeClass("hide");
            valid = false;
        } else {
            $("#image_size_error").addClass("hide");
        }

        // Only png and jpg are accepted 
        var ext = $('#checkout_image').val().split('.').pop().toLowerCase();

        if ($.inArray(ext, ["png", "jpg", "jpeg"]) == -1) {
            $("#image_ext_error").removeClass("hide");
            valid = false;
        } else {
            $("#image_ext_error").addClass("hide");
        }
    }
    
    return valid;
}

function numbersOnly(event) {
    // Backspace, Delete, Left, Right, 0-9
    return event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39 || (event.charCode >= 48 && event.charCode <= 57);
}

$(document).ready(function () {
    $('#checkout_image').bind('change', function() {
        validateImage();
    });

	$("#checkout_button").click(function () {
        if (!validateCheckout()) return;
        
        var formData = new FormData(document.getElementById("checkout_form"));
        
        $("#uploading").removeClass();
        
		$.ajax({
			url: "includes/inventory/add_item.php",
			type: "POST",
			data: formData,
            contentType: false,
            processData: false,
			dataType: "json",
			success: function(data) {
                $('ul.tabs').tabs('select_tab', 'inv');
                $("#checkout_form")[0].reset();
                $("#checkout_date_label").removeClass("hide");
                $("#uploading").addClass("hide");
                
				addItem(data);
				getLog(0);
			},
			fail: function(data) {
				console.log("Fail");
				console.log(data);
				
				$("#uploading").addClass("red-text accent-4-text");
			}
		});
	});
    
    $("#create_contact").click(function() { 
        $("#create_contact_form")[0].reset();
        $("#create_contact_modal").openModal();
        console.log("Open");
    });
    
    $("#submit_create_contact").click(function() {
        if (!validateContact()) return;
        
        var name = $("#create_name").val();
        var number = $("#create_number").val();
        var email = $("#create_email").val();
        
        $.ajax({
            url: "includes/contacts/create_contact.php",
            type: "POST",
            data: {name: name, phone_number: number, email: email},
            success: function(data) {
                $("#create_contact_modal").closeModal();
                $("#create_contact_form")[0].reset();
                setContact(data, name, number, email);   
                getContacts();
            }, 
            fail: function(data) {
                console.log("Fail!");
                console.log(data);
            }
        });
    });
    
    $("#lendee_search").on('input', function() {
        var search = $("#lendee_search").val();
        
        if (search != "") {
            $("#search-results").removeClass("hide");
            $.ajax({
                url: "includes/contacts/search_contacts.php",
                type: "POST",
                data: {search: search},
                dataType: "json",
                success: function(data) {
                    $("#results").empty();
                    
                    data.forEach(function(contact) {
                        listContact(contact);     
                    });
                },
                fail: function(data) {
                    console.log("Fail!");
                    console.log(data);
                } 
            });
        } else {
            $("#results").empty();
        }
    });
    
    $("#lendee_search").focus(function() {
        $("#search-results").removeClass("hide");
        lendeeFocused = true;
    });
    
    $(document).click(function(e) {
        if (lendeeFocused && $(":focus").attr("id") != "lendee_search") {
            if ($(e.target).attr("#id") == "create_contact") {
                $("#create_contact_modal").openModal();
            } else {
                lendeeFocused = false;
                $("#search-results").addClass("hide");
            }
        } 
    });
    
    $("#change_contact").click(function() {
        $("#contact_card").addClass("hide");
        $("#checkout_contact_id").val(-1);
        $("#search-results").removeClass("hide");
        $("#search_holder").removeClass("hide");
        $("#lendee_search").focus();
    });
});