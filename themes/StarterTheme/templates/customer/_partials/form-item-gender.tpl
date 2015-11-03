<label>
  <span>{l s='Social title'}</span>
</label>

{* StarterTheme: rename gender into social_title (classes, database, variables) *}

{foreach from=$genders item=gender}
  <label>
    <input type="radio" name="id_gender" id="id_gender{$gender.id}" value="{$gender.id}"
      {if $smarty.post.id_gender == $gender.id}checked="checked"{/if} />
    {$gender.name}
  </label>
{/foreach}
