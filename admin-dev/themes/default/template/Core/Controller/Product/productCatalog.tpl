<div class="panel">
    ## STATS
</div>

<div id="product_catalog_category_tree_filter" class="panel">
    ## GROS FILTRE
    {$categories}
</div>

<div class="panel col-lg-12">
    <div class="panel-heading">
        ## PRODUITS {$ls_products_filter_category|default:''}
        <span class="badge">{$product_count}</span>
    </div>

    <form name="product_catalog_list" method="post" action="{$post_url}">
        <input type="hidden" name="ls_products_filter_category" value="{$ls_products_filter_category|default:''}" />
        <table class="table product">
            <theader>
                
            </theader>
            {$product_list}
            <tfooter>
                
            </tfooter>
        </table>
        
        <div class="row">
            <div class="pull-left">
                ## ACTIONS GROUPIR
            </div>
            <div class="pull-right">
                {$navigator}
            </div>
        </div>
    </form>
</div>
