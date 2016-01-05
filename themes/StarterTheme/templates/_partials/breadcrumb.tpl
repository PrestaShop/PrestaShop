<nav>
  <ol vocab="http://schema.org/" typeof="BreadcrumbList">
    {foreach from=$breadcrumb item=path name=breadcrumb}
      <li property="itemListElement" typeof="ListItem">
        <a property="item" typeof="WebPage" href="{$path.url}">
          <span property="name">{$path.title}</span>
        </a>
        <meta property="position" content="{$smarty.foreach.breadcrumb.iteration}">
      </li>
    {/foreach}
  </ol>
</nav>
