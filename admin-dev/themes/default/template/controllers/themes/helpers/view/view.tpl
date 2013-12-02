<div class="alert alert-success">
    {l s='The theme %1s has been successfully installed.' sprintf=$themeName}
</div>

{if isset($imgError['error'])}
    <div class="alert alert-warning">
        {l s='Warning: Copy/Paste your errors if you want to manually set the image type (in the "Images" page under the "Preferences" menu):'}
        <ul>
            {foreach $imgError['error'] as $error}
                <li>
                    {l s='Name image type:'} <strong>{$error['name']}</strong> {l s='Width:'} {$error['width']}
                    px {l s='Height:'} {$error['height']}px
                </li>
            {/foreach}
        </ul>

    </div>
{/if}
{if isset($imgError['ok'])}
    <div class="alert alert-success">
        {l s='Images have been correctly updated in database:'}
        <ul>
            {foreach $imgError['error'] as $error}
                <li>
                    {l s='Name image type:'} <strong>{$error['name']}</strong> {l s='Width:'} {$error['width']}
                    px {l s='Height:'} {$error['height']}px
                </li>
            {/foreach}
        </ul>

    </div>
{/if}

<a href="{$back_link}"><button class="btn btn-default">{l s='Finish'}</button></a>
