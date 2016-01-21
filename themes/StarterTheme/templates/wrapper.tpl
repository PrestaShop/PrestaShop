{extends file=$layout}

{*
  This file is used to embed the output of some module
  front controllers inside the standard layout.
  If this does not make sense to you, leave this file untouched :)
  In particular, this page requires no specific design or styling.
*}

{block name='content'}
  {$content nofilter}
{/block}
