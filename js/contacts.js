var contactMarkup = '<li id={0} class="collection-item contact_li" style="height: 90px;">\
                        <div class="left-align" style="float: left;">\
                            <span id="contact_name" class="title" style="font-size: 1em; margin: 0px;">{1}</span>\
                            <br><span id="contact_phone">{2}</span>\
                            <br><span id="contact_email">{3}</span>\
                        </div>\
                        <div class="right-align">\
                            <a id="edit_button" class="waves-effect waves-light btn {5}" title="Edit" onclick="editContact(this)"><i class="medium material-icons">mode_edit</i></a>\
                        </div>\
                    </li>';

var contactEditing = null;

function addContact(contact) {
    var contactHTML = contactMarkup.format(contact.id, contact.name, formatPhoneNumber(contact.phone_number), contact.email);
    
    $("#contacts").append(contactHTML);
}

function editContact(o) {
    contactEditing = $(o).parents(".contact_li");
    
    $("#edit_contact_id").val(contactEditing.attr("id"));
    $("#edit_contact_name").val(contactEditing.find("#contact_name").text());
    $("#edit_number").val(contactEditing.find("#contact_phone").text().replace(/\D/g, ""));
    $("#edit_email").val(contactEditing.find("#contact_email").text());
    
    $("#edit_contact_modal").openModal();
}

function getContacts() {
    $.ajax({
        url: "includes/contacts/get_contacts.php",
        type: "POST",
        dataType: "json",
        success: function(data) {
            data.forEach(function(contact) {
                addContact(contact); 
            });
        },
        fail: function(data) {
            console.log("Fail!");
            console.log(data);
        }
    });
}

function validateEditContact() {
    var valid = true;
    
    if($("#edit_contact_name").val() == "") {
        $("#edit_name_error").removeClass("hide");
        valid = false;
    } else {
        $("#edit_name_error").addClass("hide");
    }
    
    if ($("#edit_number").val() == "") {
        $("#edit_number_error").removeClass("hide");
        valid = false;
    } else {
        $("#edit_number_error").addClass("hide");
    }
    
    if ($("#edit_email").val() == "") {
        $("#edit_email_error").removeClass("hide");
        valid = false;
    } else {
        $("#edit_email_error").addClass("hide");
    }
    
    return valid;
}

$(document).ready(function() {
    getContacts();
    
    $("#submit_edit_contact").click(function() {
        if (!validateEditContact()) return;
        
        var name = $("#edit_contact_name").val();
        var number = $("#edit_number").val();
        var email = $("#edit_email").val();
        
        $.ajax({
            url: "includes/contacts/edit_contact.php",
            type: "POST",
            data: {id: $("#edit_contact_id").val(), name: name, phone_number: number, email: email},
            success: function(data) {
                contactEditing.find("#contact_name").text(name);
                contactEditing.find("#contact_phone").text(number.replace(/\D/g, ""));
                contactEditing.find("#contact_email").text(email);
                
                itemsChanged = true;
                getLog(0);
                getItems();
                
                $("#edit_contact_modal").closeModal();
            },
            fail: function(data) {
                console.log("Fail!");
                console.log(data);
            }
        });
    });
});