<div id="squashResults">
		<h3>
			{if $prevDate}<small>&laquo; <a href="{$ROOT_WWW}/squash/show-date/{$prevDate}">{$prevDate}</a></small>{/if}
			Wyniki z {$date}
			{if $nextDate}<small> <a href="{$ROOT_WWW}/squash/show-date/{$nextDate}">{$nextDate}<a/> &raquo;</small>{/if}
		</h3>
		
	<table class="results">
		<colgroup>
			<col>
			{foreach from=$players item=player key=playerId}
				<col>
			{/foreach}
		</colgroup>
		
		<tr>
			<th>\</th>
			{foreach from=$players item=player key=playerId}
				<th class="playerName">{$player->shortName}</th>
			{/foreach}
		</tr>
		
		{foreach from=$players item=playerOne key=playerOneId}
			<tr>
				<td class="playerName">{$playerOne->shortName}</td>
				{foreach from=$players item=playerTwo key=playerTwoId}
					<td class="result">
						{if $playerOneId == $playerTwoId}
							<span class="ex">X</span>
						{else}
							<div class=score>
								<span class="score">{$results.$playerOneId.$playerTwoId->scoreOne}</span>
								:
								<span class="score">{$results.$playerOneId.$playerTwoId->scoreTwo}</span>
							</div>
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
		
	</table>
</div>
