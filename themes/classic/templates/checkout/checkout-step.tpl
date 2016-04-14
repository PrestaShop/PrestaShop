<section  id    = "{$identifier}"
          class = "{[
                      'checkout-step' => true,
                      '-js-current'   => $step_is_current,
                      '-reachable'    => $step_is_reachable,
                      '-complete'     => $step_is_complete
                  ]|classnames}"
>
  <h1 class="step-title">
    <i class="material-icons done">&#xE876;</i>
    <span class="step-number">{$position}</span>
    {$title}
    <span class="step-edit"><i class="material-icons edit">mode_edit</i> edit</span>
  </h1>

  <div class="content">
    {block name='step_content'}DUMMY STEP CONTENT{/block}
  </div>
</section>
