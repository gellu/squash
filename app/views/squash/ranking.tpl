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
		{assign var=player_id value=$player->id}
		<li class="position_{$key+1}">
			<span class="position">{$key+1}</span>
			<span class="player"><a href="{$ROOT_WWW}/squash/ranking-stats/{$player->id}">{$player->name}</a></span>
			<span class="ranking">{$player->ranking}</span>
			<span class="lastRanking">
				{if !isset($last_ranking[$player_id])}
					--
				{elseif $last_ranking[$player_id]->ranking > $player->ranking}
					<span class="minus"> -{$last_ranking[$player_id]->ranking-$player->ranking}</span>
				{elseif $last_ranking[$player_id]->ranking < $player->ranking}
					<span class="plus"> +{$player->ranking-$last_ranking[$player_id]->ranking}</span>
				{else}
					--
				{/if}
			</span>
			<div class="cl"></div>
		</li>
	{/foreach}
	</ul>
	
	<div class="links"><a href="{$ROOT_WWW}/squash">Zobacz Wyniki</a></div>
</div>
