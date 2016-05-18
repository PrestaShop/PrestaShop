<div class="linklist">
  {foreach $linkBlocks as $linkBlock}
    <div class="col-md-2">
      <h3 class="h3">{$linkBlock.title}</h3>
      <ul>
        {foreach $linkBlock.links as $link}
          <li>
            <a id="{$link.id}"
                class="{$link.class}"
                href="{$link.url}"
                title="{$link.description}">
              {$link.title}
            </a>
          </li>
        {/foreach}
      </ul>
    </div>
  {/foreach}
</div>
