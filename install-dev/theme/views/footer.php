<ul id="footer">
  <?php if (is_array($this->getConfig('footer.links'))): ?>
    <?php foreach($this->getConfig('footer.links') as $link => $label): ?>
      <li>
        <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer">
          <?php echo $label; ?>
        </a>
        |
      </li>
    <?php endforeach ?>
  <?php endif; ?>

  <li>&copy; 2007-<?php echo date('Y'); ?></li>
</ul>

<script type="text/javascript">
  if (typeof psuser_assistance != 'undefined')
  {
	var errors = new Array();

	$.each($('li.fail'), function(i, item){
	  errors.push($(this).text().trim());
	});

	psuser_assistance.setStep('install_<?php echo addslashes(self::$steps->current()->getName()); ?>', {'error': errors + ' || {"version": "' + ps_version + '"}'});

	if (errors.length) {
	  $('#iframe_help').attr('src', $('#iframe_help').attr('src') + '&errors=' + encodeURI(errors.join(', ')));
    }
  }
</script>
