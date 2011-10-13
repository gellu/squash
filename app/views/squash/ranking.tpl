<div id="squashRanking" class="squashContent">
	<div class="title">
		<h1>Ranking</h1>
		<span>z dnia {$date}</span>
	</div>
	<div class="menu">
		<ul>
			<li><a href="{$ROOT_WWW}/squash/build-ranking">Przelicz ranking</a></li>
			<li><a href="{$ROOT_WWW}/squash/ranking-stats">Wykres rankingu</a></li>
		</ul>
	</div>
	<ul style="clear:both">
	{foreach from=$players item=player key=key}
		<li class="position_{$key+1}">
			<span class="position">{$key+1}</span>
			<span class="player"><a href="{$ROOT_WWW}/squash/ranking-stats/{$player->id}">{$player->name}</a></span>
			<span class="ranking">{$player->ranking}</span>
			<div class="cl"></div>
		</li>
	{/foreach}
	</ul>
	
	<div class="links"><a href="{$ROOT_WWW}/squash">Zobacz Wyniki</a></div>
</div>
