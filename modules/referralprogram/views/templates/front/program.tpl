{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8084 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
// <![CDATA[
	ThickboxI18nClose = "{l s='Close' mod='referralprogram'}";
	ThickboxI18nOrEscKey = "{l s='or Esc key' mod='referralprogram'}";
	tb_pathToImage = "{$img_ps_dir}loadingAnimation.gif";
	//]]>
</script>

{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='referralprogram'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Referral Program' mod='referralprogram'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Referral program' mod='referralprogram'}</h2>

{if $error}
	<p class="error">
		{if $error == 'conditions not valided'}
			{l s='You need to agree to the conditions of the referral program!' mod='referralprogram'}
		{elseif $error == 'email invalid'}
			{l s='At least one e-mail address is invalid!' mod='referralprogram'}
		{elseif $error == 'name invalid'}
			{l s='At least one first name or last name is invalid!' mod='referralprogram'}
		{elseif $error == 'email exists'}
			{l s='Someone with this e-mail address has already been sponsored!' mod='referralprogram'}: {foreach from=$mails_exists item=mail}{$mail} {/foreach}
		{elseif $error == 'no revive checked'}
			{l s='Please mark at least one checkbox' mod='referralprogram'}
		{elseif $error == 'cannot add friends'}
			{l s='Cannot add friends to database' mod='referralprogram'}
		{/if}
	</p>
{/if}

{if $invitation_sent}
	<p class="success">
	{if $nbInvitation > 1}
		{l s='E-mails have been sent to your friends!' mod='referralprogram'}
	{else}
		{l s='An e-mail has been sent to your friend!' mod='referralprogram'}
	{/if}
	</p>
{/if}

{if $revive_sent}
	<p class="success">
	{if $nbRevive > 1}
		{l s='Reminder e-mails have been sent to your friends!' mod='referralprogram'}
	{else}
		{l s='A reminder e-mail has been sent to your friend!' mod='referralprogram'}
	{/if}
	</p>
{/if}

<ul class="idTabs clearfix">
	<li><a href="#idTab1" {if $activeTab eq 'sponsor'}class="selected"{/if}>{l s='Sponsor my friends' mod='referralprogram'}</a></li>
	<li><a href="#idTab2" {if $activeTab eq 'pending'}class="selected"{/if}>{l s='Pending friends' mod='referralprogram'}</a></li>
	<li><a href="#idTab3" {if $activeTab eq 'subscribed'}class="selected"{/if}>{l s='Friends I sponsored' mod='referralprogram'}</a></li>
</ul>
<div class="sheets">

	<div id="idTab1">
		<p class="bold">
			<strong>{l s='Get a discount of %d for you and your friends by recommending this Website.' sprintf=$discount mod='referralprogram'}</strong>
		</p>
		{if $canSendInvitations}
			<p>
				{l s='It\'s quick and it\'s easy. Just fill in the first name, last name, and e-mail address(es) of your friend(s) in the fields below.' mod='referralprogram'}
				{if $orderQuantity > 1}
					{l s='When one of them makes at least %d orders' sprintf=$orderQuantity mod='referralprogram'}
				{else}
					{l s='When one of them makes at least %d order' sprintf=$orderQuantity mod='referralprogram'}
				{/if},
				{l s='he or she will receive a %1$d voucher and you will receive your own voucher worth %2$d.' sprintf=[$discount,$discount] mod='referralprogram'}
			</p>
			<form method="post" action="{$link->getModuleLink('referralprogram', 'program', [], true)}" class="std">
				<table class="std">
				<thead>
					<tr>
						<th class="first_item">&nbsp;</th>
						<th class="item">{l s='Last name' mod='referralprogram'}</th>
						<th class="item">{l s='First name' mod='referralprogram'}</th>
						<th class="last_item">{l s='E-mail' mod='referralprogram'}</th>
					</tr>
				</thead>
				<tbody>
					{section name=friends start=0 loop=$nbFriends step=1}
					<tr class="{if $smarty.section.friends.index % 2}item{else}alternate_item{/if}">
						<td class="align_right">{$smarty.section.friends.iteration}</td>
						<td><input type="text" class="text" name="friendsLastName[{$smarty.section.friends.index}]" size="14" value="{if isset($smarty.post.friendsLastName[$smarty.section.friends.index])}{$smarty.post.friendsLastName[$smarty.section.friends.index]|escape:'htmlall':'UTF-8'}{/if}" /></td>
						<td><input type="text" class="text" name="friendsFirstName[{$smarty.section.friends.index}]" size="14" value="{if isset($smarty.post.friendsFirstName[$smarty.section.friends.index])}{$smarty.post.friendsFirstName[$smarty.section.friends.index]|escape:'htmlall':'UTF-8'}{/if}" /></td>
						<td><input type="text" class="text" name="friendsEmail[{$smarty.section.friends.index}]" size="20" value="{if isset($smarty.post.friendsEmail[$smarty.section.friends.index])}{$smarty.post.friendsEmail[$smarty.section.friends.index]|escape:'htmlall':'UTF-8'}{/if}" /></td>
					</tr>
					{/section}
				</tbody>
				</table>
				<p class="bold">
					<strong>{l s='Important: Your friends\' e-mail addresses will only be used in the referral program. They will never be used for other purposes.' mod='referralprogram'}</strong>
				</p>
				<p class="checkbox">
					<input type="checkbox" name="conditionsValided" id="conditionsValided" value="1" {if isset($smarty.post.conditionsValided) AND $smarty.post.conditionsValided eq 1}checked="checked"{/if} />
					<label for="conditionsValided">{l s='I agree to the terms of service and adhere to them unconditionally.' mod='referralprogram'}</label>
					<a href="{$link->getModuleLink('referralprogram', 'rules', ['height' => '500', 'width' => '400'], true)}" class="thickbox" title="{l s='Conditions of the referral program' mod='referralprogram'}">{l s='Read conditions.' mod='referralprogram'}</a>
				</p>
				<p class="see_email">
					{l s='Preview' mod='referralprogram'} 
					{assign var="file" value="{$lang_iso}/referralprogram-invitation.html"}
					<a href="{$link->getModuleLink('referralprogram', 'email', ['height' => '500', 'width' => '600', 'mail' => {$file}], true)}" class="thickbox" title="{l s='Invitation e-mail' mod='referralprogram'}">{l s='the default e-mail' mod='referralprogram'}</a> {l s='that will be sent to your friend(s).' mod='referralprogram'}
				</p>
				<p class="submit">
					<input type="submit" id="submitSponsorFriends" name="submitSponsorFriends" class="button_large" value="{l s='Validate' mod='referralprogram'}" />
				</p>
			</form>
		{else}
			<p class="warning">
				{l s='To become a sponsor, you need to have completed at least' mod='referralprogram'} {$orderQuantity} {if $orderQuantity > 1}{l s='orders' mod='referralprogram'}{else}{l s='order' mod='referralprogram'}{/if}.
			</p>
		{/if}
	</div>

	<div id="idTab2">
	{if $pendingFriends AND $pendingFriends|@count > 0}
		<p>
			{l s='These friends have not yet placed an order on this Website since you sponsored them, but you can try again! To do so, mark the checkboxes of the friend(s) you want to remind, then click on the button "Remind my friend(s)"' mod='referralprogram'}
		</p>
		<form method="post" action="{$link->getModuleLink('referralprogram', 'program', [], true)}" class="std">
			<table class="std">
			<thead>
				<tr>
					<th class="first_item">&nbsp;</th>
					<th class="item">{l s='Last name' mod='referralprogram'}</th>
					<th class="item">{l s='First name' mod='referralprogram'}</th>
					<th class="item">{l s='E-mail' mod='referralprogram'}</th>
					<th class="last_item"><b>{l s='Last invitation' mod='referralprogram'}</b></th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$pendingFriends item=pendingFriend name=myLoop}
				<tr>
					<td>
						<input type="checkbox" name="friendChecked[{$pendingFriend.id_referralprogram}]" id="friendChecked[{$pendingFriend.id_referralprogram}]" value="1" />
					</td>
					<td>
						<label for="friendChecked[{$pendingFriend.id_referralprogram}]">{$pendingFriend.lastname|substr:0:22}</label>
					</td>
					<td>{$pendingFriend.firstname|substr:0:22}</td>
					<td>{$pendingFriend.email}</td>
					<td>{dateFormat date=$pendingFriend.date_upd full=1}</td>
				</tr>
			{/foreach}
			</tbody>
			</table>
			<p class="submit">
				<input type="submit" value="{l s='Remind my friend(s)' mod='referralprogram'}" name="revive" id="revive" class="button_large" />
			</p>
		</form>
		{else}
			<p class="warning">{l s='You have not sponsored any friends.' mod='referralprogram'}</p>
		{/if}
	</div>

	<div id="idTab3">
	{if $subscribeFriends AND $subscribeFriends|@count > 0}
		<p>
			{l s='Here are sponsored friends who have accepted your invitation:' mod='referralprogram'}
		</p>
		<table class="std">
		<thead>
			<tr>
				<th class="first_item">&nbsp;</th>
				<th class="item">{l s='Last name' mod='referralprogram'}</th>
				<th class="item">{l s='First name' mod='referralprogram'}</th>
				<th class="item">{l s='E-mail' mod='referralprogram'}</th>
				<th class="last_item">{l s='Inscription date' mod='referralprogram'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$subscribeFriends item=subscribeFriend name=myLoop}
			<tr>
				<td>{$smarty.foreach.myLoop.iteration}.</td>
				<td>{$subscribeFriend.lastname|substr:0:22}</td>
				<td>{$subscribeFriend.firstname|substr:0:22}</td>
				<td>{$subscribeFriend.email}</td>
				<td>{dateFormat date=$subscribeFriend.date_upd full=1}</td>
			</tr>
			{/foreach}
		</tbody>
		</table>
	{else}
		<p class="warning">
			{l s='No sponsored friends have accepted your invitation yet.' mod='referralprogram'}
		</p>
	{/if}
	</div>
</div>

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /> {l s='Back to Your Account' mod='referralprogram'}</a></li>
	<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /> {l s='Home' mod='referralprogram'}</a></li>
</ul>
