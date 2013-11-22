<li class="help-context-{$label|escape:'html':'UTF-8'}">
    <a id="desc-{$label|escape:'html':'UTF-8'}-help"
       class="toolbar_btn"
       href="#"
       onclick="showHelp('{$help_base_url|escape:'html':'UTF-8'}',
                         '{$label|escape:'html':'UTF-8'}',
                         '{$iso_lang|escape:'html':'UTF-8'}',
                         '{$version|escape:'html':'UTF-8'}',
                         '{$doc_version|escape:'html':'UTF-8'}',
                         '{$country|escape:'html':'UTF-8'}');"
        title="{$tooltip|escape:'html':'UTF-8'}">
        <span class="{$button_class|escape:'html':'UTF-8'}"></span>
        <div>{l s='Help'}</div>
    </a>
</li>
