<div id="squashRanking" class="squashContent">
	<div class="title">
		<h1>Ranking</h1>
		<span>z dnia 09/11/2011</span>
	</div>
	<ul>
	{foreach from=$players item=player key=key}
		<li class="position_{$key+1}">
			<span class="position">{$key+1}</span>
			<span class="player">{$player->name}</span>
			<span class="ranking">{$player->ranking}</span>
			<div class="cl"></div>
		</li>
	{/foreach}
	</ul>
</div>
