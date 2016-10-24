{extends file='page.tpl'}

    {block name='page_content_container'}
      <section id="content" class="page-home">
        {block name='page_content_top'}{/block}
        {block name='page_content'}
          {$HOOK_HOME nofilter}
        {/block}
      </section>
    {/block}
