<h2>{l s='Information'}</h2>
<fieldset>
	<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='Help'}</legend>
	<p>{l s='This information must be indicated when you report a bug on our bug tracker or if you report a problem on our forum.'}</p>
</fieldset>
<br />
<fieldset>
	<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='Information about your configuration'}</legend>
	<h3>{l s='Server information'}</h3>
	<p>
		<b>{l s='Prestashop Version'}:</b> {$version.ps}
	</p>

	{if count($uname)}
	<p>
		<b>{l s='Server information'}:</b> {$uname}
	</p>
	{/if}

	<p>
		<b>{l s='Server software Version'}:</b> {$version.server}
	</p>
	<p>
		<b>{l s='PHP Version'}:</b> {$version.php}
	</p>
	<p>
		<b>{l s='MySQL Version'}:</b> {$version.mysql}
	</p>
	{if $apache_instaweb}
	<p style="color:red;font-weight:700">{l s='PageSpeed module for Apache installed (mod_instaweb)'}</p>
	{/if}

	<hr />
	<h3>{l s='Store information'}</h3>
	<p>
		<b>{l s='URL of your website'}:</b> {$shop.url}
	</p>
	<p>
		<b>{l s='Theme name used'}:</b> {$shop.theme}
	</p>
	<hr />
	<h3>{l s='Mail information'}</h3>
	<p>
		<b>{l s='Mail method'}:</b>

{if $mail}
	{l s='You use PHP mail() function.'}</p>
{else}
	{l s='You use your own SMTP parameters'}</p>
	<p>
		<b>{l s='SMTP server'}:</b> {$smtp.server}
	</p>
	<p>
		<b>{l s='SMTP user'}:</b>
		{if $smtp.user neq ''}
			{l s='Defined'}
		{else}
			<span style="color:red;">{l s='Not defined'}</span>
		{/if}
	</p>
	<p>
		<b>{l s='SMTP password'}:</b>
		{if $smtp.password neq ''}
			{l s='Defined'}
		{else}
			<span style="color:red;">{l s='Not defined'}</span>
		{/if}
	</p>
	<p>
		<b>{l s='Encryption'}:</b> {$smtp.encryption}
	</p>
	<p>
		<b>{l s='Port'}:</b> {$smtp.port}
	</p>
{/if}
	<hr />
	<h3>{l s='Your information'}</h3>
	<p>
		<b>{l s='Information from you'}:</b> {$user_agent}
	</p>
</fieldset>
<br />
<fieldset id="checkConfiguration">
	<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='Check your configuration'}</legend>
	<p>
		<b>{l s='Required parameters'}:</b>
		{if !$failRequired}
				<span style="color:green;font-weight:bold;">OK</span>
			</p>
		{else}
			<span style="color:red">{l s='Please consult the following error(s)'}</span>
		</p>
		<ul>
			{foreach from=$testsRequired item='value' key='key'}
				{if $value eq 'fail'}
					<li>{$testsErrors[$key]}</li>
				{/if}
			{/foreach}
		</ul>
		{/if}

		<p>
			<b>{l s='Optional parameters'}:</b>
		{if !$failOptional}
			<span style="color:green;font-weight:bold;">OK</span>
		</p>
		{else}
			<span style="color:red">{l s='Please consult the following error(s)'}</span>
		</p>
		<ul>
			{foreach from=$testsOptional item='value' key='key'}
				{if $value eq 'fail'}
					<li>{$testsErrors[$key]}</li>
				{/if}
			{/foreach}
		</ul>
		{/if}

</fieldset>
