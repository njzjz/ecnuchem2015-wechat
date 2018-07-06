$(function(){
	$("#form").submit(function(e) {
		ajax($("#url").val(),"#main",1);
		$("#more").hide();
		return false;
	});
	ajax($("#url").val(),"#main",$("#mode").val());
});