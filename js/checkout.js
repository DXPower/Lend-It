$(document).ready(function () {
	$("#checkout_button").click(function () {
		var formData = $("#checkout_form").serializeArray();
		
		if (formData.item_name == "") { // To finish later
			
		}
		
		$.ajax({
			url: "includes/inventory/add_item.php",
			type: "POST",
			data: $("#checkout_form").serialize(),
			dataType: "json",
			success: function(data) {
				console.log(data);
				addItem(data.item_name, data.lendee, data.date);
				
				$('ul.tabs').tabs('select_tab', 'inv');
			},
			fail: function(data) {
				console.log("Fail");
				console.log(data);
			}
		});
	});
});