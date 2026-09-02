{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
         {hook h='displayAdminEndContent'}
	</div>
</div>
{if $display_footer}
<div id="footer" class="bootstrap">
	<div class="col-sm-12">
		{hook h="displayBackOfficeFooter"}
	</div>
</div>
{/if}
{if isset($php_errors)}
	{include file="error.tpl"}
{/if}

{if isset($modals)}
<div class="bootstrap">
	{$modals}
</div>
{/if}

</body>
</html>
