{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{extends file="helpers/list/list_header.tpl"}
{block name="startForm"}
{$action = $action|replace:'id_shop':'shop_id'}
	<form method="post" action="{$action|escape:'html':'UTF-8'}" class="form-horizontal clearfix" id="form-{$list_id}">
{/block}
