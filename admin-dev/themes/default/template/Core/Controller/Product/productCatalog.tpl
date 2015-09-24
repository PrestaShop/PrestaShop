<div class="panel">
    ## STATS
</div>

<div id="product_catalog_category_tree_filter" class="panel">
    ## GROS FILTRE
    <input type="button" name="product_catalog_category_tree_filter_reset" value="Unselect" onclick="productCategoryFilterReset($('div#product_catalog_category_tree_filter'))" />
    {$categories}
</div>

<div class="panel col-lg-12">
    <div class="panel-heading">
        ## PRODUITS {if $has_filter}## Filtrés {/if}
        <span class="badge">{if $has_filter}{$product_count_filtered} / {/if}{$product_count}</span>
    </div>

    <form name="product_catalog_list" id="product_catalog_list" method="post" action="{$post_url}">
        <input type="hidden" name="ls_products_filter_category" value="{$ls_products_filter_category|default:''}" />
        <table class="table product">
            <theader>
                <tr class="column-headers">
                    <td>&nbsp;</td>
                    <td>
                        ID
                    </td>
                    <td>
                        Image
                    </td>
                    <td>
                        Nom
                    </td>
                    <td>
                        Ref
                    </td>
                    <td>
                        category
                    </td>
                    <td>
                        prix de base
                    </td>
                    <td>
                        prix final
                    </td>
                    <td>
                        quantité
                    </td>
                    <td>
                        état
                    </td>
                    <td>&nbsp;</td>
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
                            <option value="1" {if isset($ls_products_filter_column_active) && $ls_products_filter_column_active === '1'}selected="selected"{/if}>##Oui</option>
                            <option value="0" {if isset($ls_products_filter_column_active) && $ls_products_filter_column_active === '0'}selected="selected"{/if}>##Non</option>
                        </select>
                    </td>
                    <td>
                        <input type="submit" name="products_filter_submit" value="##Rechercher" />
                        <input type="button" name="products_filter_reset" onclick="productColumnFilterReset($(this).closest('tr.column-filters'))" value="##Réinitialiser" />
                    </td>
                </tr>
            </theader>
            {$product_list}
            <tfooter>
                
            </tfooter>
        </table>
        
        <div class="row">
            <div class="pull-left">
                ## ACTIONS GROUPIR
                <input type="button" name="bulk_action_1" onclick="testBulkAction1($('form#product_catalog_list'))" value="##Test bulk 1" />
            </div>
            <div class="pull-right">
                {$navigator}
            </div>
        </div>
    </form>
</div>
