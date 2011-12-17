<table class="table" width="100%;" cellspacing="0" cellpadding="0" id="documents_table">
	<thead>
	<tr>
		<th style="width:20%">{l s='Date'}</th>
		<th style="width:25%">{l s='Document'}</th>
		<th style="width:20%">{l s='Number'}</th>
		<th>{l s='Amount'}</th>
		<th style="width:42px"></th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$order->getDocuments() item=document}
	{*if TYPE DOCUMENT = INVOICE *}
	<tr class="invoice_line" id="invoice_{$document->id}">
	{*/if*}
		<td class="document_date">{dateFormat date=$document->date_add}</td>
		<td class="document_type">Invoice</td>
		<td class="document_number"><a href="pdf.php?pdf&id_order_invoice={$document->id}">#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang)}{'%06d'|sprintf:$document->number}</a></td>
		<td class="document_amount">
		{*if TYPE DOCUMENT = INVOICE *}
			{displayPrice price=$document->total_paid_tax_incl currency=$currency->id}&nbsp;
			{if $document->getRestPaid()}
				<span style="color:red;font-weight:bold;">({displayPrice price=$document->getRestPaid() currency=$currency->id} {l s='not paid'})</span>
			{/if}
		{*/if*}
		</td>
		<td class="right document_action">
		{*if TYPE DOCUMENT = INVOICE *}
			{if $document->getRestPaid()}
				<a href="#" class="js-set-payment" data-amount="{$document->getRestPaid()}" data-id-invoice="{$document->id}" title="{l s='Set payment form'}"><img src="../img/admin/money_add.png" alt="{l s='Set payment form'}" /></a>
			{/if}
			<a href="#" onclick="$('#invoiceNote{$document->id}').show(); return false;" title="{if $document->note eq ''}{l s='Add note'}{else}{l s='Edit note'}{/if}"><img src="../img/admin/note.png" alt="{if $document->note eq ''}{l s='Add note'}{else}{l s='Edit note'}{/if}"{if $document->note eq ''} class="js-disabled-action"{/if} /></a>
		{*/if*}
		</td>
	</tr>
	{*if TYPE DOCUMENT = INVOICE *}
	<tr id="invoiceNote{$document->id}" style="display:none" class="current-edit">
		<td colspan="5">
			<form action="{$currentIndex}&viewOrder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}" method="post">
				<p>
					<label for="editNote{$document->id}" class="t">{l s='Note'}</label>
					<input type="hidden" name="id_order_invoice" value="{$document->id}" />
					<textarea name="note" rows="10" cols="10" id="editNote{$document->id}" class="edit-note">{$document->note|escape:'htmlall':'UTF-8'}</textarea>
				</p>
				<p class="right">
					<input type="submit" name="submitEditNote" value="{l s='Save'}" class="button" />
					<input type="button" name="cancelNote" id="cancelNote" value="{l s='Cancel'}" onclick="$('#invoiceNote{$document->id}').hide();" class="button" />
				</p>
			</form>
		</td>
	</tr>
	{*/if*}
		{foreachelse}
	<tr>
		<td colspan="5" class="center">
			<h3>{l s='No document is available'}</h3>
			<p><a class="button" href="{$currentIndex}&viewOrder&submitGenerateInvoice&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">{l s='Generate invoice'}</a></p>
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>