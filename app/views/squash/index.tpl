{assign var=today value=$smarty.now|date_format:"%Y-%m-%d"}

<div id="squashResults" class="squashContent">
	<input type="hidden" id="date" name="date" value="{$date}">
	<div class="date">
		<span class="prev">{if $prevDate}&laquo; <a href="{$ROOT_WWW}/squash/show-date/{$prevDate}">{$prevDate}</a>{/if}&nbsp;</span>
		Wyniki z {$date}
		<span class="next">&nbsp;
			{if $nextDate}
				<a href="{$ROOT_WWW}/squash/show-date/{$nextDate}">{$nextDate}<a/> &raquo;
			{elseif $date != $today}
				<a href="{$ROOT_WWW}/squash/show-date/{$today}">Dodaj nowe</a>  &raquo
			{/if}
		</span>
	</div>
	<div class="today">
		{if $date == $today}
			(dzisiaj)
		{/if}
	</div>
	<p class="editInfo">Kliknij wynik, aby go edytować</p>	
	<div class="cl"></div>
	<table class="results">
		<colgroup>
			<col>
			{foreach from=$players item=player key=playerId}
				<col>
			{/foreach}
		</colgroup>
		<tr>
			<th><img class="ajaxLoader" src="{$ROOT_WWW}/img/ajax-loader.gif" border="0" />
				<img class="okIcon" src="{$ROOT_WWW}/img/ok_icon.gif">
			</th>
			{foreach from=$players item=player key=playerId}
				<th class="playerName top">{$player->shortName}</th>
			{/foreach}
		</tr>
		
		{foreach from=$players item=playerOne key=playerOneId}
			<tr>
				<td class="playerName left">{$playerOne->shortName}</td>
				{foreach from=$players item=playerTwo key=playerTwoId}
					<td class="result" id="result_{$playerOne->id}_{$playerTwo->id}">
						{if $playerOneId == $playerTwoId}
							<span class="ex">X</span>
						{elseif !$results.$playerOneId.$playerTwoId->scoreOne && $results.$playerOneId.$playerTwoId->scoreOne != 0}
							<input type="text" value="" />
						{else}
							<input type="text" value="{$results.$playerOneId.$playerTwoId->scoreOne}:{$results.$playerOneId.$playerTwoId->scoreTwo}">
							
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
		
	</table>
</div>
