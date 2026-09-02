{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{if !$message.id_employee}
	{assign var="type" value="customer"}
{else}
	{assign var="type" value="employee"}
{/if}

<div class="message-item{if $initial}-initial-body{/if}">
{if !$initial}
	<div class="message-avatar">
		<div class="avatar-md">
			{if $type == 'customer'}
				<i class="icon-user icon-3x"></i>
			{else}
				{if isset($current_employee->firstname)}<img src="{$message.employee_image}" alt="{$current_employee->firstname|escape:'html':'UTF-8'}" />{/if}
			{/if}
		</div>
	</div>
{/if}
	<div class="message-body">
		{if !$initial}
			<h4 class="message-item-heading">
				<i class="icon-mail-reply text-muted"></i>
					{if $type == 'customer'}
						{$message.customer_name|default|escape:'html':'UTF-8'}
					{else}
						{$message.employee_name|escape:'html':'UTF-8'}
					{/if}
			</h4>
		{/if}
		<span class="message-date">&nbsp;<i class="icon-calendar"></i> - {dateFormat date=$message.date_add full=0} - <i class="icon-time"></i> {$message.date_add|substr:11:5}</span>
		{if isset($message.file_name)} <span class="message-product">&nbsp;<i class="icon-link"></i> <a href="{$message.file_link|escape:'html':'UTF-8'}" target="_blank" rel="noopener noreferrer nofollow">{l s="Attachment" d='Admin.Catalog.Feature'}</a></span>{/if}
		{if isset($message.product_name)} <span class="message-attachment">&nbsp;<i class="icon-book"></i> <a href="{$message.product_link|escape:'html':'UTF-8'}" target="_blank" rel="noopener noreferrer nofollow">{l s="Product" d='Admin.Global'} {$message.product_name|escape:'html':'UTF-8'} </a></span>{/if}
		<p class="message-item-text">{$message.message|escape:'html':'UTF-8'|nl2br}</p>
	</div>
</div>
