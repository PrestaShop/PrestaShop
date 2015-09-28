<!--## STATS BANNER TO REINTEGRATE SOON!-->

<div id="product_catalog_category_tree_filter" class="panel">
    CATEGORIES &nbsp;
    <input type="button" name="product_catalog_category_tree_filter_reset" value="Unselect"
        onclick="productCategoryFilterReset($('div#product_catalog_category_tree_filter'))"
        class="pull-right btn" />
    {$categories}
</div>

<div class="panel col-lg-12">
    <div class="panel-heading">
        PRODUCTS {if $has_filter} Filtered {/if}
        <span class="badge">{if $has_filter}{$product_count_filtered} / {/if}{$product_count}</span>
    </div>

    <form name="product_catalog_list" id="product_catalog_list" method="post" action="{$post_url}" orderingurl="{$ordering_url}" >
        <input type="hidden" name="ls_products_filter_category" value="{$ls_products_filter_category|default:''}" />
        <table class="table product">
            <theader>
                <tr class="column-headers">
                    <th>
                        <input type="checkbox" id="bulk_action_select_all" onclick="$(this).closest('table').find('td.checkbox-column input:checkbox').prop('checked', $(this).prop('checked')); updateBulkMenu();" />
                    </th>
                    <th>
                        ID
                        &nbsp;<span psorderby="id_product" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="id_product" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Image
                    </th>
                    <th>
                        Name
                        &nbsp;<span psorderby="name" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="name" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Reference
                        &nbsp;<span psorderby="reference" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="reference" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Category
                        &nbsp;<span psorderby="name_category" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="name_category" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Base price
                        &nbsp;<span psorderby="price" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="price" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Final price
                    </th>
                    <th>
                        Quantity
                        &nbsp;<span psorderby="sav_quantity" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="sav_quantity" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    <th>
                        Status
                        &nbsp;<span psorderby="active" psorderway="asc" class="icon-caret-up"></span>
                        &nbsp;<span psorderby="active" psorderway="desc" class="icon-caret-down"></span>
                    </th>
                    {if $has_category_filter}
                        <th>
                            Position
                            &nbsp;<span psorderby="position" psorderway="asc" class="icon-caret-up"></span>
                            &nbsp;<span psorderby="position" psorderway="desc" class="icon-caret-down"></span>
                        </th>
                    {/if}
                    <th>&nbsp;</th>
                </tr>
                <tr class="column-filters">
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" name="ls_products_filter_column_id_product" value="{$ls_products_filter_column_id_product}" />
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" name="ls_products_filter_column_name" value="{$ls_products_filter_column_name}" />
                    </td>
                    <td>
                        <input type="text" name="ls_products_filter_column_reference" value="{$ls_products_filter_column_reference}" />
                    </td>
                    <td>
                        <input type="text" name="ls_products_filter_column_name_category" value="{$ls_products_filter_column_name_category}" />
                    </td>
                    <td>
                        <input type="text" name="ls_products_filter_column_price" value="{$ls_products_filter_column_price}" />
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" name="ls_products_filter_column_sav_quantity" value="{$ls_products_filter_column_sav_quantity}" />
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
                            TODO: tri ASC direct en bouton
                        </td>
                    {/if}
                    <td>
                        <input type="submit" class="btn btn-primary" name="products_filter_submit" value="Filter" />
                        <input type="reset" class="btn btn-warning" name="products_filter_reset" onclick="productColumnFilterReset($(this).closest('tr.column-filters'))" value="Reset" />
                    </td>
                </tr>
            </theader>
            {$product_list}
            <tfooter>
                
            </tfooter>
        </table>
        
        <div class="row">
            <div class="pull-left" bulkurl="{$bulk_url}" redirecturl="{$bulk_redirect_url}">
                <div class="btn-group dropup">
                    <button id="product_bulk_menu" class="btn btn-default dropdown-toggle" disabled="disabled" data-toggle="dropdown">
                        Bulk actions
                        <i class="icon-caret-up"></i>&nbsp;
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onclick="bulkProductAction(this, 'activate_all');">
                                <i class="icon-power-off"></i>
                                Activate selection
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="bulkProductAction(this, 'deactivate_all');">
                                <i class="icon-power-off"></i>
                                Deactivate selection
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" onclick="bulkProductAction(this, 'delete_all');">
                                <i class="icon-trash"></i>
                                Delete selection
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:bulkProductEdition(this, 'quantity_edition');">
                                <i class="icon-pencil"></i>
                                Edit quantities
                            </a>
                        </li>
                    </ul>
                </div>

                <div style="display: inline; margin-left: 2em;">
                    <span id="bulk_edition_toolbar" style="display: none;">
                        <input type="button" class="btn" onclick="bulkProductAction(this, 'quantity_edition');" value="Save & refresh" />
                        <input type="submit" class="btn btn-primary" onclick="bulkProductAction(this, 'quantity_edition_next');" value="Save & next page" />
                        <input type="button" class="btn btn-warning" onclick="bulkProductEdition(this, 'cancel');" value="Cancel" />
                    </span>
                </div>
            </div>
            <div class="pull-right">
                {$navigator}
            </div>
        </div>
    </form>
</div>
