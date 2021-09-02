<div id="leftpannel">
  <ol id="tabs">
    <?php foreach (self::getSteps() as $step): ?>
      <?php if (self::getSteps()->current()->getName() == $step->getName()): ?>
        <li class="selected"><?php echo $step; ?></li>
      <?php elseif ($this->isStepFinished($step->getName())): ?>
        <li class="finished"><a href="index.php?step=<?php echo $step->getName(); ?>"><?php echo $step; ?></a></li>
      <?php elseif ($step->getName() == $this->getLastStep()): ?>
        <li class="configuring"><a href="index.php?step=<?php echo $step->getName(); ?>"><?php echo $step; ?></a></li>
      <?php else: ?>
        <li><?php echo $step; ?></li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>

  <?php echo $this->getHook('menu-footer'); ?>
</div>
