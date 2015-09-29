<tbody>
    {foreach key=rowid item=product from=$products}
        <tr>
            <td class="checkbox-column">
                <input type="checkbox" name="bulk_action_selected_products[]" value="{$product.id_product}" />
            </td>
            <td>
                {$product.id_product}
            </td>
            <td>
                [im#{$product.id_image}]
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
                    {if $activate_drag_and_drop}
                        +MOVE!&nbsp;
                    {/if}
                    {$product.position}
                </td>
            {/if}
            <td>
                <div class="btn-group-action">
                    {include file="../Admin/dropdown_menu.tpl"
                        button_id="product_list_id_"|cat:$product.id_product|cat:"_menu"
                        default_item=[
                            "href" => $product.url,
                            "icon" => "icon-pencil",
                            "label" => "Modify"
                        ]
                        items=[
                            [
                                "href" => "#todo",
                                "icon" => "icon-eye",
                                "label" => "Preview"
                            ],
                            [
                                "href" => "#todo",
                                "icon" => "icon-copy",
                                "label" => "Duplicate"
                            ],
                            [
                                "href" => "#todo",
                                "icon" => "icon-trash",
                                "label" => "Delete"
                            ]
                        ]}
                </div>
            </td>
        </tr>
    {foreachelse}
        <tr><td colspan="11">
            EMPTY CASE !
        </td></tr>
    {/foreach}
</tbody>