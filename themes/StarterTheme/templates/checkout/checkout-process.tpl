{foreach from=$steps item="step" key="index"}
  {render identifier  =  $step.identifier
          position    =  ($index + 1)
          ui          =  $step.ui
  }
{/foreach}
