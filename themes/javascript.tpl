{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if isset($js_def) && is_array($js_def) && $js_def|@count}
<script type="text/javascript">
{foreach from=$js_def key=k item=def}
var {$k} = {$def|json_encode nofilter};
{/foreach}
</script>
{/if}
{if isset($js_files) && $js_files|@count}
{foreach from=$js_files key=k item=js_file}
  {assign var="js_uri" value=$js_file}
  {assign var="js_params" value=[]}
  {if is_array($js_file)}
    {assign var="js_uri" value=$js_file.uri}
    {assign var="js_params" value=$js_file.params}
  {/if}
  <script type="{if isset($js_params.type)}{$js_params.type}{else}text/javascript{/if}" src="{$js_uri|escape:'html':'UTF-8'}"{if isset($js_params.defer) && $js_params.defer} defer{/if}{if isset($js_params.async) && $js_params.async} async{/if}{if isset($js_params.attributes) && $js_params.attributes} {$js_params.attributes}{/if}></script>
{/foreach}
{/if}
{if isset($js_inline) && $js_inline|@count}
<script type="text/javascript">
{foreach from=$js_inline key=k item=inline}
{$inline}
{/foreach}
</script>
{/if}
