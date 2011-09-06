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
	
	$("#squashResults table.results td.result input")
	.focus(function(){
		$(this).addClass("active");
	})
	.mask("9:9")
	.keyup(function(event){
		if (event.keyCode == 13) {
			$(this).blur();
		}
		var playersArr	= $(this).parent().attr("id").split('_');
		var scoresArr	= $(this).val().split(':');
		//console.log($(this).parent().attr("id"));
		//console.log("#result_"+playersArr[2]+"_"+playersArr[1]+" input");
		$("#result_"+playersArr[2]+"_"+playersArr[1]+" input").val(scoresArr[1].trim()+":"+scoresArr[0].trim()).addClass('activeSecond');
	})
	.blur(function(){
		$(this).removeClass("active");
		saveResult($(this).parent().attr("id"), $(this).val(), $("#date").val());
	});
});

function saveResult(playersStr, scoreStr, date)
{
	var playersArr	= playersStr.split('_');
	var scoresArr	= scoreStr.split(':');
	if (!scoresArr[0] || !scoresArr[1]) {
		return;
	}
	
	var player_one_id = playersArr[1];
	var player_two_id = playersArr[2];
	var score_one	= scoresArr[0].trim();
	var score_two	= scoresArr[1].trim();
	
	$.ajax({
		  url: ROOT_WWW + '/ajax/squash-edit/save-result',
		  type: 'POST',
		  dataType: 'json',
		  data: {
			  player_one_id: player_one_id,
			  player_two_id: player_two_id,
			  score_one:	 score_one,
			  score_two:	 score_two,
			  date:			 date
		  },
		  beforeSend: function(){
			  $("img.ajaxLoader").show();  
		  },
		  success: function(){
			  $("img.ajaxLoader").hide();
			  $("img.okIcon").fadeIn('slow').delay(600).fadeOut('slow');
			  $("#result_"+player_two_id+"_"+player_one_id+" input").removeClass('activeSecond')
			  //$("img.okIcon").fadeOut('slow');
			  
		  }
		});
}