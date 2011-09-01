Wyniki squasha
<table>
	
	<h3>
		{if $prevDate}<small>&laquo; <a href="{$ROOT_WWW}/squash/show-date/{$prevDate}">{$prevDate}</a></small>{/if}
		Wyniki z {$date}
		{if $nextDate}<small> <a href="{$ROOT_WWW}/squash/show-date/{$nextDate}">{$nextDate}<a/> &raquo;</small>{/if}
	</h3>
	
	<tr>
		<th>\</th>
		{foreach from=$players item=player key=playerId}
			<th>{$player->shortName}</th>
		{/foreach}
	</tr>
	
	{foreach from=$players item=playerOne key=playerOneId}
		<tr>
			<td>{$playerOne->shortName}</td>
			{foreach from=$players item=playerTwo key=playerTwoId}
				<td>
					{if $playerOneId == $playerTwoId}
						X
					{else}
						{$results.$playerOneId.$playerTwoId->scoreOne} : {$results.$playerOneId.$playerTwoId->scoreTwo}
					{/if}
				</td>
			{/foreach}
		</tr>
	{/foreach}
	
</table>
