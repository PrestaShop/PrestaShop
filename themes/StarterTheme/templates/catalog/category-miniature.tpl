<ul>
  <li>
    <a href="{$category.url}">
      <img src="{$category.image.medium.url}" alt="{$category.image.legend}">
    </a>
  </li>
  <li>
    <b class="h2"><a href="{$category.url}">{$category.name}</a></b>
  </li>
  <li class="category-description">
     {$category.description nofilter}
  </li>
</ul>
