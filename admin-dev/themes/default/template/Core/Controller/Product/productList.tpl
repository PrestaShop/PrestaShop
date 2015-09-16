<tbody>
    {foreach key=rowid item=product from=$products}
        <tr>
            <td>
                {$product.id_product}
            </td>
            <td>
                [IMAGE]
            </td>
            <td>
                <a href="{$product.url}">{$product.name}</a>
            </td>
            <td>
                {$product.reference}
            </td>
            <td>
                {$product.price}
            </td>
        </tr>
    {foreachelse}
        <tr><td colspan="5">
            EMPTY CASE !
        </td></tr>
    {/foreach}
</tbody>