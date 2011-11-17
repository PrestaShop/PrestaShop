<table style="width: 100%">
<tr>
	<td style="width: 50%">
        {if $logo_path}
            <img src="{$logo_path}" />
        {/if}
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%">{$shop_name|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 11pt; color: #9E9F9E">{$date|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 11pt; color: #9E9F9E">{$title|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

