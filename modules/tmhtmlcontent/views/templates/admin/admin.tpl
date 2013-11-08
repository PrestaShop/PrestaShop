<div id="htmlcontent">
    <h2>{$htmlcontent.info.name} (v.{$htmlcontent.info.version})</h2>
    
    {if $error}
        {include file="{$htmlcontent.admin_tpl_path}messages.tpl" id="main" text=$error class='error'}
    {/if}
    {if $confirmation}
        {include file="{$htmlcontent.admin_tpl_path}messages.tpl" id="main" text=$confirmation class='conf'}
    {/if}
    
    <!-- New -->
    {include file="{$htmlcontent.admin_tpl_path}new.tpl"}
    <!-- Slides -->
    {include file="{$htmlcontent.admin_tpl_path}items.tpl"}

</div>