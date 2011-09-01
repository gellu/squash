var limit_reached = 0;
var def_photo_size = 650;
var def_button_size = 100;
var already_voted = 0;
var voted_minus = 0;
var voted_from_checkpoint = 0;
var checkpoint = 20;

$(document).ready(function(){
	if ($('#next_photo').length == 0) 
	{
		limit_reached = 1;
	}
	
	//dynamiczna zmiana rozmiaru fotki
	if ($('#photo_to_vote').length != 0)
	{
		resizePhoto();
		$(window).resize(function() {
			resizePhoto();
		});
	}
	//welcome dialog
	if ($('#welcome_dialog').length != 0) 
	{	
		
		$("#welcome_dialog").dialog({title: '', height: 250, width: 500});
		$("#welcome_button").button();
		$("#welcome_button").click(function(){
			saveVoterName();
		});
		
		$("#welcome_name").keyup(function(event){
			if (event.keyCode == 13)
			{
				saveVoterName();
			}
		});
	}
	
	//checkpoint info
	if ($('#checkpoint_button').length != 0)
	{
		$("#checkpoint_button").button();
		$("#checkpoint_button").click(function() {
			$("#checkpoint_info").css('display', 'none');
			$("#photo_to_vote").fadeIn('fast');
			$("div.vote_buttons").css('display', 'block');
		});
	}
	
	//ocenianie
	$('a.vote_button').click(function(){
		var vote;
		if ($(this).attr('class').indexOf('plus_vote') != -1) {
			vote = 1;
		} else {
			vote = -1;
		}
		$(this).fadeTo('fast', '0.3');
		
		if ($('#multiple_voting').length != 0){
			ajaxForMultipleVoting(vote);
		} else {
			var photo_id = $(this).parent().attr('photo_id');
			ajaxForResultsVote(photo_id, vote);
		}
		
		
		$(this).fadeTo('fast', '1');
	});
		
		
	if ($('#voter_photos').length != 0)
	{
		$("#voter_photos img").mouseenter(function() {
			$(this).css('border-color', '#666');
			var photo_id = $(this).parent().parent().attr('id').substring(6);
			$("#votes_info_"+photo_id).fadeTo("fast",1);
			
		});
		$("#voter_photos img").mouseleave(function() {
			$(this).css('border-color', '#444');
			var photo_id = $(this).parent().parent().attr('id').substring(6);
			$("#votes_info_"+photo_id).fadeTo("fast",0.5);
		});
	}
	
	if ($('#proceed_to_vote_button').length != 0)
	{
		$("#proceed_to_vote_button").button();
		$("#proceed_to_vote_button").click(function() {
			window.location = $(this).find('a').first().attr('href');
		});
	}
});


function ajaxForMultipleVoting(vote)
{
	if (parseInt(vote) == -1) {
		voted_minus++;
	}
		
	$.ajax({
		data: {
				photo_id		: $("#photo_to_vote").attr('photo_id'),
				next_photo_id	: $("#next_photo").attr('photo_id'),
				vote			: vote,
				voted_minus 	: voted_minus,
				voted_cnt		: already_voted
				},
		type: "POST",
		cache: false,
		dataType: 'json',
		url: ROOT_WWW + '/ajax/photo/addVote',
		beforeSend: function(data) {
					$("#photo_to_vote").fadeOut('fast');
		},
		success: function(data) {
					if (limit_reached)
					{
						$("#vote_photo").remove();
						$("div.vote_buttons").remove();
						
						$("#counter").remove();
						$("p.no_photos").css('display', 'block');
					}
					else
					{
						//podmiana fotki do oceny
						$("#photo_to_vote").delay(200).queue(function(){
							$(this).attr('src', $("#next_photo").attr('src'));
							$(this).attr('photo_id', $("#next_photo").attr('photo_id'));
							$(this).dequeue();
							
							//zaladowanie next_photo
							if (data.resp.id = parseInt(data.resp.id )) {
								$("#next_photo").attr('photo_id', data.resp.id);
								
								var next_photo_id = ""+data.resp.id;
								var padCnt = 4 - next_photo_id.length;
								while (next_photo_id.length < 4) {
									next_photo_id = "0"+next_photo_id;
								}
								$("#next_photo").attr('src', ROOT_WWW + '/img/photos/'+next_photo_id+'.jpg');
								
							} else {
								limit_reached = 1;
							}
						});
							
						//aktualizacja licznika
						$("#voted").text(data.resp.voted_cnt);
						
						//obsluga checkpointow
						already_voted += 1;
						voted_from_checkpoint += 1;
						if (voted_from_checkpoint == checkpoint)
						{
							$("#photo_to_vote").css('display', 'none');
							$("#next_photo").css('display', 'none');
							$("div.vote_buttons").css('display', 'none');
							checkpoint *= 2;
							voted_from_checkpoint = 0;
							$("#checkpoint_cnt").text(already_voted);
							$("#checkpoint_info").css('display', 'block');
						}
						else
						{
							$("#photo_to_vote").fadeIn('slow');
							
						}
					}
					
		}
	});
}

function ajaxForResultsVote(photo_id, vote)
{
	$.ajax({
		data: {
				photo_id: photo_id,
				vote	: vote
				},
		type: "POST",
		cache: false,
		dataType: 'json',
		url: ROOT_WWW + '/ajax/photo/addVote',
		success: function(data) {
				new_plus_cnt = plus_cnt = parseInt($('#plus_cnt_'+photo_id).text());
				if (vote == 1) {
					$('#plus_cnt_'+photo_id).text(plus_cnt + 1);
					new_plus_cnt += 1;
				}

				plus_percent = parseFloat($('#plus_percent_'+photo_id).text());
				new_percent = (new_plus_cnt / (((plus_cnt*100) / plus_percent) + 1)) * 100;
				new_percent = Math.round(new_percent*100)/100;
				$('#plus_percent_'+photo_id).text(new_percent);
				
				$('#user_vote_'+photo_id).html('Twój głos: <img src="'+ ROOT_WWW+'/img/' + ((vote == 1) ? 'plus': 'minus')+'_min.png" />');
					
		}
	});
}

function resizePhoto()
{
	var max_size = $(window).height()-200;
	if (max_size < def_photo_size || $("#photo_to_vote").height() < max_size) {
		$("#photo_to_vote").css('height', Math.min(max_size, def_photo_size));
		$("#vote_photo").css('height', $(window).height()-180);
	}
}

function saveVoterName()
{
	$.ajax({
		data: {
				name	: $("#welcome_name").val(),
				referer : document.referrer
				},
		type: "POST",
		cache: false,
		dataType: 'json',
		url: ROOT_WWW + '/ajax/photo/addVoter',
		success: function(data) {
				$("#welcome_dialog").dialog("close");
		}
	});
}