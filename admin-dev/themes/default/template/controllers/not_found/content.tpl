{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{if isset($controller) && !empty($controller) && $controller != 'adminnotfound'}
	<h1>{l s='The controller %s is missing or invalid.' sprintf=[$controller]}</h1>
{/if}
<a class="btn btn-default" href="javascript:window.history.back();">
	<i class="icon-arrow-left"></i>
	{l s='Back to the previous page'}
</a>
<a class="btn btn-default" href="index.php">
	<i class="icon-dashboard"></i>
	{l s='Go to the dashboard'}
</a>
