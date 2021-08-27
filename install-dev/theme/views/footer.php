<ul id="footer">
	<li><a href="https://www.prestashop.com" title="PrestaShop.com" target="_blank">PrestaShop.com</a> | </li>
	<li><a href="https://www.prestashop.com/<?php echo $this->language->getLanguageIso() ?>/contact-us" title="<?php echo $this->translator->trans('Contact us', array(), 'Install'); ?>" target="_blank"><?php echo $this->translator->trans('Contact us', array(), 'Install'); ?></a> | </li>
	<li>&copy; 2007-<?php echo date('Y'); ?></li>
</ul>

<script type="text/javascript">
	if (typeof psuser_assistance != 'undefined')
	{
		var errors = new Array();
		$.each($('li.fail'), function(i, item){
			errors.push($(this).text().trim());
		});
		psuser_assistance.setStep('install_<?php echo addslashes(self::$steps->current()->getName()) ?>', {'error': errors + ' || {"version": "' + ps_version + '"}'});
		if (errors.length)
			$('#iframe_help').attr('src', $('#iframe_help').attr('src') + '&errors=' + encodeURI(errors.join(', ')));
	}
</script>
