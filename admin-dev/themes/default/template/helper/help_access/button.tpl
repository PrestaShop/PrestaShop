<li class="help-context-{$label|escape:'htmlall':'UTF-8'}" style="display:none">
    <a id="desc-{$label|escape:'htmlall':'UTF-8'}-help"
       class="toolbar_btn"
       href="#"
       onclick="showHelp('{$help_base_url|escape:'htmlall':'UTF-8'}',
                         '{$label|escape:'htmlall':'UTF-8'}',
                         '{$iso_lang|escape:'htmlall':'UTF-8'}',
                         '{$version|escape:'htmlall':'UTF-8'}',
                         '{$doc_version|escape:'htmlall':'UTF-8'}',
                         '{$country|escape:'htmlall':'UTF-8'}');"
        title="{$tooltip|escape:'htmlall':'UTF-8'}">
        <span class="{$button_class|escape:'htmlall':'UTF-8'}"></span>
        <div>{l s='Help'}</div>
    </a>
</li>
