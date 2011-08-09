<?php 

require_once dirname(__FILE__).'/../../config/config.inc.php';
include_once(dirname(__FILE__).'/FavoriteProduct.php');

if(FavoriteProduct::isCustomerFavoriteProduct(2,2))
	echo 'yes';
else
	echo 'non';
?>