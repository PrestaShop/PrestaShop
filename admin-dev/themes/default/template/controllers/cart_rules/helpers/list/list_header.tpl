{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{extends file="helpers/list/list_header.tpl"}
{block name='override_header'}
{if $submit_form_ajax}
	<script type="text/javascript">
		$('#voucher', window.parent.document).val('{$new_cart_rule->code|escape:'html':'UTF-8'}');
		parent.add_cart_rule({$new_cart_rule->id|intval});
		parent.$.fancybox.close();
	</script>
{/if}
{/block}
