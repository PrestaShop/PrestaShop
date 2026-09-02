{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

<div class="leadin">{block name="leadin"}{/block}</div>

{block name="override_tpl"}{/block}

{hook h='displayAdminView'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}View{/capture}
	{hook h=$hookName}
{elseif isset($controller_name)}
    {capture name=hookName assign=hookName}display{$controller_name|ucfirst}View{/capture}
    {hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}View{/capture}
	{hook h=$hookName}
{/if}
