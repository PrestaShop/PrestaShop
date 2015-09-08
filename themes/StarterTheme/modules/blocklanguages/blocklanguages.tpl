<ul class="language-selector">
  {foreach from=$languages item=language}
    <li><a href="{$link->getLanguageLink($language.id_lang)}">{$language.name}</a></li>
  {/foreach}
</ul>
