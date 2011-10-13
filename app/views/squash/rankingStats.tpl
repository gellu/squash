<div id="squashRankingStats" class="squashContent">
	<div class="title">
		<h1>Ranking - statystyki</h1>
	</div>
	<div id="flotPlaceholder" style="width:800px; height:400px"/>
	
	<div class="links"><a href="{$ROOT_WWW}/squash">Zobacz Wyniki</a></div>
</div>


<script type="text/javascript">
var rankingStates = [];

{foreach from="$plotData" item="userPlotData" key="userId"}
	var userPlotData = [];
	{foreach from="$userPlotData" item="userRanking" key="validFor"}
		userPlotData.push([{$validFor}*1000, {$userRanking}]);
	{/foreach}

	{literal} var row = {data : userPlotData, label: "{/literal}{$players.$userId->name}{literal}"}
	rankingStates.push(row);
	{/literal}
{/foreach}
{literal}

$(function () {
	
    var plot = $.plot($("#flotPlaceholder"),
    		rankingStates, {
               series: {
                   lines: { show: true },
                  
               },
               grid: { hoverable: true, clickable: true },
               xaxis: { mode: "time", timeformat: "%d/%m/%y", max: (new Date()).getTime()+1000*24*3600*14},
               
             });
});
 </script>
 {/literal}