<!--## STATS BANNER TO REINTEGRATE SOON!-->

<div id="product_catalog_category_tree_filter" class="panel">
    {l s="Categories"} &nbsp;
    <input type="button" name="product_catalog_category_tree_filter_reset" value="Unselect"
        onclick="productCategoryFilterReset($('div#product_catalog_category_tree_filter'))"
        class="pull-right btn" />
    {$categories}
</div>

<div class="panel col-lg-12">
    <div class="panel-heading">
        {l s="Products"} {if $has_filter} {l s="(filtered)"} {/if}
        <span class="badge">{if $has_filter}{$product_count_filtered} / {/if}{$product_count}</span>
    </div>

    <form name="product_catalog_list" id="product_catalog_list" method="post" action="{$post_url}" orderingurl="{$ordering_url}" >
        <input type="hidden" name="ls_products_filter_category" value="{$ls_products_filter_category|default:''}" />
        <table class="table product" redirecturl="{$unit_redirect_url}">
            <theader>
                <tr class="column-headers">
                    <th>
                        <input type="checkbox" id="bulk_action_select_all" onclick="$(this).closest('table').find('td.checkbox-column input:checkbox').prop('checked', $(this).prop('checked')); updateBulkMenu();" />
                    </th>
                    <th>
                        {l s="ID"}
                        &nbsp;<span psorderby="id_product" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="id_product" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Image"}
                    </th>
                    <th>
                        {l s="Name"}
                        &nbsp;<span psorderby="name" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="name" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Reference"}
                        &nbsp;<span psorderby="reference" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="reference" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Category"}
                        &nbsp;<span psorderby="name_category" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="name_category" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Base price"}
                        &nbsp;<span psorderby="price" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="price" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Final price"}
                    </th>
                    <th>
                        {l s="Quantity"}
                        &nbsp;<span psorderby="sav_quantity" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="sav_quantity" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        {l s="Status"}
                        &nbsp;<span psorderby="active" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="active" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    {if $has_category_filter}
                        <th>
                            {l s="Position"}
                            &nbsp;<span psorderby="position" psorderway="asc" class="icon-caret-up"></span>
                            &nbsp;<span psorderby="position" psorderway="desc" class="icon-caret-down"></span>
                        </th>
                    {/if}
                    <th>&nbsp;</th>
                </tr>
                <tr class="column-filters">
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" placeholder="{l s="Exact ID matching"}" name="ls_products_filter_column_id_product" value="{$ls_products_filter_column_id_product}" />
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" placeholder="{l s="Partial name allowed"}" name="ls_products_filter_column_name" value="{$ls_products_filter_column_name}" />
                    </td>
                    <td>
                        <input type="text" placeholder="{l s="Partial reference allowed"}" name="ls_products_filter_column_reference" value="{$ls_products_filter_column_reference}" />
                    </td>
                    <td>
                        <input type="text" placeholder="{l s="Partial category name allowed"}" name="ls_products_filter_column_name_category" value="{$ls_products_filter_column_name_category}" />
                    </td>
                    <td>
                        <input type="text" placeholder="{l s="Exact base price matching"}" name="ls_products_filter_column_price" value="{$ls_products_filter_column_price}" />
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" placeholder="{l s="Exact quantity matching"}" name="ls_products_filter_column_sav_quantity" value="{$ls_products_filter_column_sav_quantity}" />
                    </td>
                    <td>
                        <select name="ls_products_filter_column_active">
                            <option value=""></option>
                            <option value="1" {if isset($ls_products_filter_column_active) && $ls_products_filter_column_active === '1'}selected="selected"{/if}>Active</option>
                            <option value="0" {if isset($ls_products_filter_column_active) && $ls_products_filter_column_active === '0'}selected="selected"{/if}>Inactive</option>
                        </select>
                    </td>
                    {if $has_category_filter}
                        <td>
                            {if !$activate_drag_and_drop}
                                <input type="button" class="btn" name="products_filter_position_asc" value="{l s="Sort on this category"}" onclick="productOrderTable('position', 'asc');" />
                            {/if}
                        </td>
                    {/if}
                    <td>
                        <input type="submit" class="btn btn-primary" name="products_filter_submit" value="{l s="Filter"}" />
                        <input type="reset" class="btn btn-warning" name="products_filter_reset" onclick="productColumnFilterReset($(this).closest('tr.column-filters'))" value="{l s="Reset"}" />
                    </td>
                </tr>
            </theader>
            {$product_list}
            <tfooter>
                
            </tfooter>
        </table>
        
        <div class="row">
            <div class="pull-left" bulkurl="{$bulk_url}" redirecturl="{$bulk_redirect_url}">
            
                {include file="../Admin/dropdown_menu.tpl"
                    div_style="btn-group dropup"
                    button_id="product_bulk_menu"
                    disabled=true
                    menu_label={l s="Selection actions"}
                    menu_icon="icon-caret-up"
                    items=[
                        [
                            "onclick" => "bulkProductAction(this, 'activate_all');",
                            "icon" => "icon-power-off",
                            "label" => {l s="Activate selection"}
                        ],
                        [
                            "onclick" => "bulkProductAction(this, 'deactivate_all');",
                            "icon" => "icon-power-off",
                            "label" => {l s="Deactivate selection"}
                        ],
                        ["divider" => true],
                        [
                            "onclick" => "bulkProductAction(this, 'delete_all');",
                            "icon" => "icon-trash",
                            "label" => {l s="Delete selection"}
                        ]
                    ]}
                {include file="../Admin/dropdown_menu.tpl"
                    div_style="btn-group dropup"
                    button_id="product_bulk_menu"
                    menu_label={l s="Bulk actions"}
                    menu_icon="icon-caret-up"
                    items=[
                        [
                            "href" => "javascript:bulkProductEdition(this, 'quantity_edition');",
                            "icon" => "icon-pencil",
                            "label" => {l s="Edit quantities"}
                        ]
                    ]}
                    
                <div style="display: inline; margin-left: 2em;">
                    <span id="bulk_edition_toolbar" style="display: none;">
                        <input type="button" class="btn" onclick="bulkProductAction(this, 'edition');" value="{l s="Save & refresh"}" />
                        <input type="submit" class="btn btn-primary" onclick="return bulkProductAction(this, 'edition_next');" value="{l s="Save & next page"}" />
                        <input type="button" class="btn btn-warning" onclick="bulkProductEdition(this, 'cancel');" value="{l s="Cancel"}" />
                    </span>
                </div>
            </div>
            <div class="pull-right">
                {$navigator}
            </div>
        </div>
    </form>
</div>
