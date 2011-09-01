$(document).ready(function(){

	$("#add_task_form").submit(function () {
		console.log("submit");
		if ($("#add_task_input").val().trim() == '')
			return false;
		$.ajax({
			data: {
					list_id: $("#list_id").attr('class'),
					content: $("#add_task_input").val(),
					},
			type: "POST",
			cache: false,
			dataType: 'json',
			url: ROOT_WWW + '/ajax/task/add',
			success: function(data) {
				var tasksHTML = $('#tasks_table').html();
				//console.log(tasksHTML);
			    if (data.stat == "ok")
			    {
			    	var newTaskHTML = getNewTaskHTML(data);				
			    	$('#tasks_table').append(newTaskHTML);
			    	$('#task_'+ data.resp.id + '_li').fadeIn('slow');
			    	$("#add_task_input").val('');
			    	
			    	setEditable();
			    }
			  }
		});
		
		return false;
	});
	
	jQuery('#add_task_input').placeholder();
	
	$(document).ready(function() {
		setEditable();
	 });
	
	function setEditable()
	{
		$('input.edit').unbind();
		$('input:checkbox').unbind();
		$('form.task_form').unbind();
		
		
		$('input.edit').focus(function(){
			$(this).css('background-color', '#fff');
			
		});
		$('input.edit').blur(function(){
			$(this).css('background-color', '#eee');
			var form_id = $(this).parent().attr('id');
			var task_id = form_id.substring(5,form_id.indexOf('_form'));
			$("#task_"+task_id+"_form").submit();
		});
		$('input.edit').keydown(function(event){
			var form_id = $(this).parent().attr('id');
			var task_id = form_id.substring(5,form_id.indexOf('_form'));
			var li = $(this).parent().parent();
			if (event.keyCode == '13') {
				//console.log(li.html());
				$.ajax({
					data: {
							list_id: $("#list_id").attr('class'),
							after_id: task_id,
							content: '',
							},
					type: "POST",
					cache: false,
					dataType: 'json',
					url: ROOT_WWW + '/ajax/task/add',
					success: function(data) {
					    if (data.stat == "ok")
					    {
					    	li.after(getNewTaskHTML(data));
					    	$('#task_'+ task_id + '_name').blur();
					    	
					    	$('#task_'+ data.resp.id + '_li').fadeIn('slow');
					    	setEditable();
					    	console.log($("#task_"+ data.resp.id + "_name").focus());
					    }
					  }
				});
				
			    event.preventDefault();
		    }
			if (event.keyCode == 8 && $(this).val() == '')
			{
				$.ajax({
					data: {
							list_id: $("#list_id").attr('class'),
							task_id: task_id,
							},
					type: "POST",
					cache: false,
					dataType: 'json',
					url: ROOT_WWW + '/ajax/task/delete',
					success: function(data) {
					    if (data.stat == "ok")
					    {
					    	$('#task_'+ task_id + '_li').toggle('fast');	
					    }
					  }
				});
				
			    event.preventDefault();
			}	
			
		});
		$('input:checkbox').click(function(){
			var form_id = $(this).parent().attr('id');
			var task_id = form_id.substring(5,form_id.indexOf('_form'));
			var before_li = $(this).parent().parent().prev();
			if (before_li.attr('id'))
				var before_id = before_li.attr('id').substring(5, before_li.attr('id').indexOf('_li'));;
			
			if ($(this).attr('checked'))
			{
				$('#task_'+task_id+'_name').css('color', '#ccc');
				$('#task_'+task_id+'_name').css('text-decoration', 'line-through');
				$('#task_'+task_id+'_date').css('color', '#ccc');
				$('#task_'+task_id+'_date').css('text-decoration', 'line-through');
				
				
				var task_name = $('#task_'+task_id+'_name').val();
				$.ajax({
					data: {
							done: 1,
							list_id: $("#list_id").attr('class'),
							task_id : task_id,
							},
					type: "POST",
					cache: false,
					dataType: 'json',
					url: ROOT_WWW + '/ajax/task/done',
					success: function(data) {
								$('#task_'+task_id+'_li').toggle('slow');
								$("#message").css('background-color', '#fff');
								$("#message").html('Task <i><strong>'+ task_name+'</strong></i> marked as done. <a href="' + ROOT_WWW + '/task/undo/'+task_id+'" id="#undo_'+task_id+'_after_'+before_id+'" class="undo ">Undo</a>');
								setUndoneAble();
					}
				});
				
			}
			
		});
		
	     $('form.task_form').submit(function(){
	    	var form_id = $(this).attr('id');
	    	var task_id = form_id.substring(5,form_id.indexOf('_form'));
	    	console.log('edit submit');

	    	$.ajax({
				data: {
						task_id : task_id,
	    				content: $("#task_"+task_id+"_name").val(),
	    				due_to: $("#task_"+task_id+"_date").val(),
						},
				type: "POST",
				cache: false,
				dataType: 'json',
				url: ROOT_WWW + '/ajax/task/edit',
				success: function(data) {
							
							$("#task_"+task_id+"_name").val(data.resp.name);
				  }
			});
	    	return false;
	     });
	}	
	
	function setUndoneAble()
	{
		$("a.undo").unbind();
		$("a.undo").click(function(){
			var a_id = $(this).attr('id');
			var after_id = a_id.substring(a_id.indexOf('_after')+7);
	    	var task_id = a_id.substring(6, a_id.indexOf('_after'));
	    	
	    	//console.log($(this));
	    	console.log('undo ' + task_id);

	    	$.ajax({
				data: {
	    				done : 0,
	    				list_id: $("#list_id").attr('class'),
	    				after_id: after_id,
						task_id : task_id,
						},
				type: "POST",
				cache: false,
				dataType: 'json',
				url: ROOT_WWW + '/ajax/task/done',
				success: function(data) {
							//TODO: animacja
							$("#message").css('background-color', '#eee');
							$("#message").html('');
							$('#task_'+task_id+'_done').removeAttr('checked');
							$('#task_'+task_id+'_name').css('color', '#000');
							$('#task_'+task_id+'_name').css('text-decoration', 'none');
							$('#task_'+task_id+'_date').css('color', '#000');
							$('#task_'+task_id+'_date').css('text-decoration', 'none');

							$('#task_'+task_id+'_li').fadeIn();
				}
			});
	    	
	    	return false;
		});
	};
	
	function getNewTaskHTML(data)
	{
		if (data.resp.due_to == null)
    	{
    		var due_to = ' ';
    		var name_class = ''; 
    		var date_class = '';
    	}
    	else
    	{
    		var due_to = data.resp.due_to;
    		var name_class = 'float_left';
    		var date_class = 'edit';
    	}
    	var newTaskHTML = '<li style="display:none" class="task" id="task_' + data.resp.id +'_li">'+
    						'<form class="task_form" id="task_' + data.resp.id +'_form" name="task_' + data.resp.id +'_form">'+
    						'<input id="task_' + data.resp.id +'_done" class="float_left" type="checkbox" value="" />'+
    						'<input id="task_' + data.resp.id +'_name" class="edit '+ name_class +'" type="text" value=" ' + data.resp.name + '" />'+
    						'<input id="task_' + data.resp.id +'_date" class="edit" type="text" value="' + due_to  + '" />'+
    						'<div class="cl" /></form></li>';
    	
    	return newTaskHTML;
    	
	}
		
	
});