<tbody>
    {foreach key=rowid item=product from=$products}
        <tr>
            <td>
                <input type="checkbox" name="bulk_action_selected_products[]" value="{$product.id_product}" />
            </td>
            <td>
                {$product.id_product}
            </td>
            <td>
                [image id#{$product.id_image}]
            </td>
            <td>
                <a href="{$product.url}">{$product.name}</a>
            </td>
            <td>
                {$product.reference}
            </td>
            <td>
                {$product.name_category}
            </td>
            <td>
                {$product.price}
            </td>
            <td>
                {$product.price_final}
            </td>
            <td>
                {$product.sav_quantity}
            </td>
            <td>
                {$product.active}
            </td>
            {if isset($product.position)}
                <td>
                    {$product.position}
                </td>
            {/if}
            <td>
                [bouton d'action]<br/>
                <a href="{$product.url}">Go to details</a><br/>
                <a href="#todo">Preview</a><br/>
                <a href="#todo">Duplicate</a><br/>
                <a href="#todo">Delete</a>
            </td>
        </tr>
    {foreachelse}
        <tr><td colspan="11">
            EMPTY CASE !
        </td></tr>
    {/foreach}
</tbody>