$(document).ready(function(){

	// login gizle registration ac
	$("#signup").click(function(){
		$("#first-form").slideUp("slow", function(){
			$("#second-form").slideDown("slow");
		});
	});

	// registration gizle login ac
	$("#signin").click(function(){
		$("#second-form").slideUp("slow", function(){
			$("#first-form").slideDown("slow");
		});
	});

});