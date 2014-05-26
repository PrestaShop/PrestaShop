/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Retrocompatibility with 1.4
if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
	baseUri = baseDir;

//JS Object : update the cart by ajax actions
var ajaxCart = {
    //last known object data
    jsonData: null,
    
    /*
        @brief add a product into the current shopping cart
        @param data is an array containing information about the product to add. 
                     Eg : data = {
                        idProduct:1,
                        idCombination:23,
                        quantity:2,
                        op:'down',
                        whishlist:[token,id_quantity]
                     }
        @param successCb callback function
        @param errorCb callback function
    */
	add : function(data, successCb, errorCb){
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseUri + '?rand=' + new Date().getTime(),
            headers: { "cache-control": "no-cache" },
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&add=1&ajax=true&qty=' + ((data.quantity && data.quantity != null) ? data.quantity : '1') + '&id_product=' + data.idProduct + '&token=' + static_token + ( (parseInt(data.idCombination) && data.idCombination != null) ? '&ipa=' + parseInt(data.idCombination): '') + '&op=' + ((data.op && data.op != null) ? data.op : 'up'),
			success: function(jsonData, textStatus, jqXHR)
			{
                //update object data
                ajaxCart.jsonData = jsonData;
                
                // add appliance to whishlist module
                if (data.whishlist && !jsonData.errors)
                    WishlistAddProductCart(data.whishlist[0], data.idProduct, data.idCombination, data.whishlist[1]);
             
                if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
	},
    
    /*
        @brief remove a product from the current shopping cart
        @param data is an array containing information about the product to remove. 
                     Eg : data = {
                        idProduct:1,
                        idCombination:23,
                        customizationId:2,
                        idAddressDelivery:4
                     }
        @param successCb callback function
        @param errorCb callback function
    */
	remove : function(data, successCb, errorCb){
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseUri + '?rand=' + new Date().getTime(),
            headers: { "cache-control": "no-cache" },
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&delete=1&id_product=' + data.idProduct + '&ipa=' + ((data.idCombination != null && parseInt(data.idCombination)) ? data.idCombination : '') + ((data.customizationId && data.customizationId != null) ? '&id_customization=' + data.customizationId : '') + '&id_address_delivery=' + data.idAddressDelivery + '&token=' + static_token + '&ajax=true',
			success: function(jsonData, textStatus, jqXHR)	
            {
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
	},
	
	/*
        @brief refresh the cart informations
        @param successCb callback function
        @param errorCb callback function
    */
	refresh : function(successCb, errorCb){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'controller=cart&ajax=true&token=' + static_token,
			success: function(jsonData, textStatus, jqXHR)
			{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
	},
    
    /*
        @brief remove a voucher from the current shopping cart
        @param idVoucher the id of the voucher to remove
        @param successCb callback function
        @param errorCb callback function
    */
    removeVoucher : function(idVoucher, successCb, errorCb) {
        //send the ajax request to the server
		$.ajax({
			type: 'GET',
			headers: { "cache-control": "no-cache" },
			url: uriOPC + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "html",
			data: 'deleteDiscount=' + idVoucher,
			success: function(jsonData, textStatus, jqXHR)	{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
    },
    
    /*
        @brief add a voucher to the current shopping cart
        @param voucherCode the code of the voucher to add
        @param successCb callback function
        @param errorCb callback function
    */
    addVoucher : function(voucherCode, successCb, errorCb) {
        //send the ajax request to the server
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: uriOPC + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "html",
			data: 'submitAddDiscount=true&discount_name=' + voucherCode,
			success: function(jsonData, textStatus, jqXHR)	{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
    },
    
    /*
        @brief allow or disallow separated package
        @param allow true or false
        @param successCb callback function
        @param errorCb callback function
    */
    allowSeparatedPackage : function(allow, successCb, errorCb){        
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'controller=cart&ajax=true&allowSeperatedPackage=true&value=' + (allow ? '1' : '0') + '&token='+static_token + '&allow_refresh=1',
			success: function(jsonData, textStatus, jqXHR)
			{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
    },
    
    /*
        @brief expand the cart display
        @param successCb callback function
        @param errorCb callback function
    */
    expand : function(){
        $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'ajax_blockcart_display=expand',
			success: function(jsonData, textStatus, jqXHR)
			{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
	},
    
    /*
        @brief collapse the cart display
        @param successCb callback function
        @param errorCb callback function
    */
	collapse : function(){
        $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'ajax_blockcart_display=collapse',
			success: function(jsonData, textStatus, jqXHR)
			{
                //update object data
                ajaxCart.jsonData = jsonData;
                
			    if (successCb)
				    successCb.call(this, jsonData, textStatus, jqXHR);
			},
            error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				if (errorCb)
				    errorCb.call(this, XMLHttpRequest, textStatus, errorThrown);
			}
		});
	},
};