<html>
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>New Flite app!!</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{$ROOT_WWW}/css/debug.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$ROOT_WWW}/css/style.css" />
	<script type="text/javascript" src="{$ROOT_WWW}/js/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/debug.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/squash.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/jquery.jeditable.js"></script>
	{literal}
	<script>
		var ROOT_WWW = '{$ROOT_WWW}';
	</script>
	{/literal}
</head> 
<body>
{if $DEBUG}
	{$DEBUG_HTML}
{/if}

{if $currentUser}
	<div id="bar">
		{$currentUser->name} <a href="./main/logout">logout</a>
	</div>
{/if}

<div id="content">
	{$content}
</div>
</body>
</html>


 

