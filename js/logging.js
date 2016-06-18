function login(email, password) {
    $("#login_error").addClass("hide");
    
    if (!validateEmail(email) || !validatePassword(password)) {
        $("#login_error").removeClass("hide");
        return;
    }
    
    var hashedPassword = hex_sha512(password);
    
    $.ajax({
        url: "includes/login.php",
        type: "POST",
        data: {email: email, password: hashedPassword},
        success: function(data) {
            if (data == true) {
                $("#login_error").addClass("hide");
                window.location.href = "home.php";
            } else {
                $("#login_error").removeClass("hide");
            }
        },
        fail: function(data) {
            console.log("Log in failed!");
            console.log(data);
        }
    });
}

function validateEmail(email) {
    if (email.length > 254) return;
    
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validatePassword(password) {
    if (password.length >= 8 && password.length <= 128) {
        return true;
    } else {
        return false;
    }
}

$(document).ready(function() {
    $("#register_submit").click(function() {
        var email = $("#register_email").val();
        var password = $("#register_password").val();
        var password2 = $("#register_password2").val();
        
        if (password != password2 || !validateEmail(email) || !validatePassword(password)) return;
        
        var hashedPassword = hex_sha512(password);
        
        $.ajax({
            url: "includes/register.php",
            type: "POST",
            data: {email: email, password: hashedPassword},
            success: function(data) {
                login(email, password);
            },
            fail: function(data) {
                console.log("Fail!");
                console.log(data);
            }
        });
    });
    
    $("#register_password").on('input', function() {
        if (!validatePassword($("#register_password").val())) {
            $("#register_password_error").removeClass("hide");
        } else {
            $("#register_password_error").addClass("hide");
        }
    });
    
    $("#register_password2").on('input', function() {
        if ($("#register_password").val() != $("#register_password2").val()) {
            $("#register_match_error").removeClass("hide");
        } else {
            $("#register_match_error").addClass("hide");
        }
    });
    
    $("#register_email").on('input', function() {
        if (!validateEmail($("#register_email").val())) {
            $("#register_email_error").removeClass("hide");
        } else {
            $("#register_email_error").addClass("hide");
        }
    });
    
    $("#login").click(function() {
        login($("#login_email").val(), password = $("#login_password").val());
    });
});