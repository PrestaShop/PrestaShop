<section  id    = "{$identifier}"
          class = "{[
                      'checkout-step'   => true,
                      '-current'        => $step_is_current,
                      '-reachable'      => $step_is_reachable,
                      '-complete'       => $step_is_complete,
                      'js-current-step' => $step_is_current
                  ]|classnames}"
>
  <h1 class="step-title h3">
    <i class="material-icons done">&#xE876;</i>
    <span class="step-number">{$position}</span>
    {$title}
    <span class="step-edit text-muted"><i class="material-icons edit">mode_edit</i> edit</span>
  </h1>

  <div class="content">
    {block name='step_content'}DUMMY STEP CONTENT{/block}
  </div>
</section>
