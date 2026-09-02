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
{foreach from=$js_files key=k item=js_uri}
<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}
{if isset($js_inline) && $js_inline|@count}
<script type="text/javascript">
{foreach from=$js_inline key=k item=inline}
{$inline}
{/foreach}
</script>
{/if}
