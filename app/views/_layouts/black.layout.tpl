<html>
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>Głosuj! To Twój obywatelski obowiązek!!</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{$ROOT_WWW}/css/debug.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$ROOT_WWW}/css/photo_vote.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$ROOT_WWW}/css/jquery-ui-1.8.5.custom.css" />
	
	<script type="text/javascript" src="{$ROOT_WWW}/js/jquery-1.4.2.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/debug.js"></script>
	<script type="text/javascript" src="{$ROOT_WWW}/js/photo_vote.js"></script>
	
	
	<script>
		var ROOT_WWW = '{$ROOT_WWW}';
	</script>
	{literal}
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-19404122-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	{/literal}
</head> 
<body>
{if $DEBUG}
	{$DEBUG_HTML}
{/if}

{if $current}
	<div id="bar">
		{$current.name} <a href="./main/logout">logout</a>
	</div>
{/if}
<div id="contact">
	@:<a href="mailto:ktraczykowski[at]gmail.com">ktraczykowski[at]gmail.com</a>
</div>

<div id="content">
	{$content}
</div>
</body>

</html>


 

