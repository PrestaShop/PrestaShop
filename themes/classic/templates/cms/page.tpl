{extends file='page.tpl'}

{block name='page_title'}
  {$cms.meta_title}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-cms page-cms-{$cms.id}">
    {$cms.content nofilter}
    
    {hook h='displayCMSDisputeInformation'}
    
    {hook h='displayCMSPrintButton'}
  </section>
{/block}
