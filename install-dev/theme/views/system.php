<?php $this->displayTemplate('header') ?>

<h2><?php echo $this->translator->trans('We are currently checking PrestaShop compatibility with your system environment', array(), 'Install') ?></h2>

<p><?php echo $this->translator->trans('If you have any questions, please visit our <a href="%doc%" target="_blank">documentation</a> and <a href="%forum%" target="_blank">community forum</a>.', array('%doc%' => $this->getDocumentationLink(), '%forum%' => $this->getForumLink(), 'Install')); ?></p>

<?php if ($this->tests['required']['success']): ?>
	<h3 class="okBlock"><?php echo $this->translator->trans('PrestaShop compatibility with your system environment has been verified!', array(), 'Install'); ?></h3>
<?php else: ?>
	<h3 class="errorBlock"><?php echo $this->translator->trans('Oops! Please correct the item(s) below, and then click "Refresh information" to test the compatibility of your new system.', array(), 'Install'); ?></h3>
<?php endif; ?>
<!-- Display tests results -->
<?php foreach ($this->tests_render as $type => $categories): ?>
	<ul id="<?php echo $type ?>">
	<?php foreach ($categories as $category): ?>
		<li class="title <?php if ($category['success'] == 1): ?>ok<?php endif;?>"><?php echo $category['title'] ?></li>
		<?php $i = 0; foreach ($category['checks'] as $id => $lang): ?>
			<li class="required <?php if ($i == 0): ?>first<?php endif;?> <?php echo isset($this->tests[$type]['checks'][$id]) ? $this->tests[$type]['checks'][$id] : 'fail' ?>">
				<?php echo $lang ?>
			</li>
		<?php $i++; endforeach; ?>
	<?php endforeach; ?>
	</ul>
<?php endforeach; ?>

<p><input class="button" value="<?php echo $this->translator->trans('Refresh', array(), 'Install'); ?> " type="submit" id="req_bt_refresh" /></p>

<?php $this->displayTemplate('footer') ?>
