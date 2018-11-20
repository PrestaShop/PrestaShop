	</div><!-- div id="sheet_step" -->
</div><!-- div id="sheets" -->

<div id="buttons">
	<?php if (!$this->isLastStep()): ?>
		<?php if ($this->next_button): ?>
			<input id="btNext" class="button little" type="submit" name="submitNext" value="<?php echo $this->translator->trans('Next', array(), 'Install'); ?>" />
		<?php else: ?>
			<input id="btNext" class="button little disabled" type="submit" name="submitNext" value="<?php echo $this->translator->trans('Next', array(), 'Install'); ?>" disabled="disabled" />
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!$this->isFirstStep() && $this->previous_button): ?>
		<input id="btBack" class="button little" type="submit" name="submitPrevious" value="<?php echo $this->translator->trans('Back', array(), 'Install') ?>" />
	<?php endif; ?>
</div>
</form>
<div id="phone_help">
	<?php echo $this->translator->trans('If you need some assistance, you can <a href="%help%" onclick="return !window.open(this.href);">get tailored help</a> from our support team. <a href="%doc%" onclick="return !window.open(this.href);">The official documentation</a> is also here to guide you.', array('%help%' => $this->getTailoredHelp(), '%doc%' => $this->getDocumentationLink()), 'Install'); ?>
</div>
</div><!-- div id="container" -->

<ul id="footer">
	<li><a href="<?php echo $this->getForumLink() ?>" title="<?php echo $this->translator->trans('Official forum', array(), 'Install'); ?>" target="_blank"><?php echo $this->translator->trans('Official forum', array(), 'Install'); ?></a> | </li>
	<li><a href="http://www.prestashop.com" title="PrestaShop.com" target="_blank">PrestaShop.com</a> | </li>
	<li><a href="<?php echo $this->getSupportLink() ?>" title="<?php echo $this->translator->trans('Support', array(), 'Install'); ?>" target="_blank"><?php echo $this->translator->trans('Support', array(), 'Install'); ?></a> | </li>
	<li><a href="<?php echo $this->getDocumentationLink() ?>" title="<?php echo $this->translator->trans('Documentation', array(), 'Install'); ?>" target="_blank"><?php echo $this->translator->trans('Documentation', array(), 'Install'); ?></a> | </li>
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
		psuser_assistance.setStep('install_<?php echo addslashes($this->step) ?>', {'error': errors + ' || {"version": "' + ps_version + '"}'});
		if (errors.length)
			$('#iframe_help').attr('src', $('#iframe_help').attr('src') + '&errors=' + encodeURI(errors.join(', ')));
	}
</script>
</body>
</html>
