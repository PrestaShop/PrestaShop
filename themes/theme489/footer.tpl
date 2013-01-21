{if !$content_only}
</div>

{if $page_name !== 'index'}
<aside>
	<div id="right_column" class="column">{$HOOK_RIGHT_COLUMN}</div>
</aside>
{/if}
</div>
</div>

<footer>
	<div id="footer" class="clearfix">
		<div>{$HOOK_FOOTER}{if $page_name == 'index'}More <a rel="nofollow" href="http://www.templatemonster.com/category/handbag-prestashop-themes/" target="_blank">Handbag PrestaShop Themes at TemplateMonster.com</a>{/if}</div>
	</div>
</footer>

</div>
</div>
{/if}
</body>
</html>