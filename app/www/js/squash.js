$(document).ready(function(){
	$("#squashResults td.result").hover(function(){
	   var col = $(this).index();
	   $($("table.results col")[col]).addClass("highlight");
	   $(this).closest("tr").addClass("highlight");
	 },function(){
	    var col = $(this).index();
	   $($("table.results col")[col]).removeClass("highlight");
	   $(this).closest("tr").removeClass("highlight");
	 });
});