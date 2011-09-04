$(document).ready(function(){
	$("#squashResults td.result").hover(function(){
	   var col = $(this).index();
	   $($("table.results th")[col]).addClass("highlight");
	   $(this).closest("tr").children().first().addClass("highlight");
	   $(this).addClass("highlight");
	   $(this).addClass("highlight");
	 },function(){
	    var col = $(this).index();
	    $($("table.results th")[col]).removeClass("highlight");
	   $(this).closest("tr").children().first().removeClass("highlight");
	   $(this).removeClass("highlight");
	   
	 });
	
	$("#squashResults table.results td.result input").focus(function(){
		$(this).addClass("active");
	});
	
	$("#squashResults table.results td.result input").blur(function(){
		$(this).removeClass("active");
		saveResult($(this).parent().attr("id"), $(this).val(), $("#date").val());
	});
});

function saveResult(playersStr, scoreStr, date)
{
	var playersArr	= playersStr.split('_');
	var scoresArr	= scoreStr.split(':');
	$.ajax({
		  url: ROOT_WWW + '/ajax/squash-edit/save-result',
		  type: 'POST',
		  dataType: 'json',
		  data: {
			  player_one_id: playersArr[1],
			  player_two_id: playersArr[2],
			  score_one:	 scoresArr[0].trim(),
			  score_two:	 scoresArr[1].trim(),
			  date:			 date
		  },
		  success: function(){
			  alert('saved');
		  }
		});
}