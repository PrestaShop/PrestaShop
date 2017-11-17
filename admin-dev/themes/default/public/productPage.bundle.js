/******/!function(modules){/******/
/******/
// The require function
/******/
function __webpack_require__(moduleId){/******/
/******/
// Check if module is in cache
/******/
if(installedModules[moduleId])/******/
return installedModules[moduleId].exports;/******/
// Create a new module (and put it into the cache)
/******/
var module=installedModules[moduleId]={/******/
i:moduleId,/******/
l:!1,/******/
exports:{}};/******/
/******/
// Return the exports of the module
/******/
/******/
/******/
// Execute the module function
/******/
/******/
/******/
// Flag the module as loaded
/******/
return modules[moduleId].call(module.exports,module,module.exports,__webpack_require__),module.l=!0,module.exports}// webpackBootstrap
/******/
// The module cache
/******/
var installedModules={};/******/
/******/
// Load entry module and return exports
/******/
/******/
/******/
/******/
// expose the modules object (__webpack_modules__)
/******/
__webpack_require__.m=modules,/******/
/******/
// expose the module cache
/******/
__webpack_require__.c=installedModules,/******/
/******/
// define getter function for harmony exports
/******/
__webpack_require__.d=function(exports,name,getter){/******/
__webpack_require__.o(exports,name)||/******/
Object.defineProperty(exports,name,{/******/
configurable:!1,/******/
enumerable:!0,/******/
get:getter})},/******/
/******/
// getDefaultExport function for compatibility with non-harmony modules
/******/
__webpack_require__.n=function(module){/******/
var getter=module&&module.__esModule?/******/
function(){return module.default}:/******/
function(){return module};/******/
/******/
return __webpack_require__.d(getter,"a",getter),getter},/******/
/******/
// Object.prototype.hasOwnProperty.call
/******/
__webpack_require__.o=function(object,property){return Object.prototype.hasOwnProperty.call(object,property)},/******/
/******/
// __webpack_public_path__
/******/
__webpack_require__.p="",__webpack_require__(__webpack_require__.s=3)}([/* 0 */
,/* 1 */
,/* 2 */
,/* 3 */
/***/
function(module,exports,__webpack_require__){__webpack_require__(4),__webpack_require__(5),__webpack_require__(6),__webpack_require__(7),__webpack_require__(8),__webpack_require__(9),__webpack_require__(10),__webpack_require__(11),module.exports=__webpack_require__(12)},/* 4 */
/***/
function(module,exports){/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
$(document).ready(function(){form.init(),nav.init(),featuresCollection.init(),displayFormCategory.init(),formCategory.init(),stock.init(),supplier.init(),specificPrices.init(),warehouseCombinations.init(),customFieldCollection.init(),virtualProduct.init(),attachmentProduct.init(),imagesProduct.init(),priceCalculation.init(),displayFieldsManager.refresh(),displayFieldsManager.init(virtualProduct),seo.init(),tags.init(),rightSidebar.init(),recommendedModules.init(),/** Type product fields display management */
$("#form_step1_type_product").change(function(){displayFieldsManager.refresh()}),
// Validate price fields on input change
$(".money-type input[type='text']").change(function(){var inputValue=priceCalculation.normalizePrice($(this).val()),parsedValue=truncateDecimals(inputValue,6);$(this).val(parsedValue)}),/** Attach date picker */
$(".datepicker").datetimepicker({locale:full_language_code,format:"YYYY-MM-DD"}),/** tooltips should be hidden when we move to another tab */
$("#form-nav").on("click",".nav-item",function(){$('[data-toggle="tooltip"]').tooltip("hide"),$('[data-toggle="popover"]').popover("hide")})});/**
 * Manage show or hide fields
 */
var displayFieldsManager=function(){var managedVirtualProduct,typeProduct=$("#form_step1_type_product"),showVariationsSelector=$("#show_variations_selector"),combinationsBlock=$("#combinations");return{init:function(virtualProduct){managedVirtualProduct=virtualProduct,/** Type product fields display management */
$("#form_step1_type_product").change(function(){displayFieldsManager.refresh()}),$("#form .form-input-title input").on("focus",function(){$(this).select()}),this.initVisibilityRule(),/** Tax rule dropdown shortcut */
$("a#tax_rule_shortcut_opener").on("click",function(){
// lazy instantiated
var duplicate=$("#form_step2_id_tax_rules_group_shortcut");if(0==duplicate.length){var origin=$("select#form_step2_id_tax_rules_group");duplicate=origin.clone(!1).attr("id","form_step2_id_tax_rules_group_shortcut"),origin.on("change",function(){duplicate.val(origin.val())}),duplicate.on("change",function(){origin.val(duplicate.val()).change()}),duplicate.appendTo($("#tax_rule_shortcut"))}return duplicate.parent().parent().show(),!1})},/**
     * When a product is available for order, its price should be visible,
     * whereas products unavailable for order can have their prices visible or hidden.
     */
initVisibilityRule:function(){var applyVisibilityRule=function(){var $availableForOrder=$(".js-available-for-order input"),$showPrice=$(".js-show-price input"),$showPriceColumn=$(".js-show-price");$availableForOrder.prop("checked")?($showPrice.prop("checked",!0),$showPriceColumn.addClass("hide")):$showPriceColumn.removeClass("hide")};$(".js-available-for-order .checkbox").on("click",applyVisibilityRule),applyVisibilityRule()},refresh:function(){this.checkAccessVariations(),$("#virtual_product").hide(),$('#form-nav a[href="#step3"]').text(translate_javascripts.Quantities),/** product type switch */
"1"===typeProduct.val()?($("#pack_stock_type, #js_form_step1_inputPackItems").show(),$('#form-nav a[href="#step4"]').show(),showVariationsSelector.hide(),showVariationsSelector.find('input[value="0"]').attr("checked",!0)):($("#virtual_product, #pack_stock_type, #js_form_step1_inputPackItems").hide(),$('#form-nav a[href="#step4"]').show(),"2"===typeProduct.val()?(showVariationsSelector.hide(),$("#virtual_product").show(),$('#form-nav a[href="#step4"]').hide(),showVariationsSelector.find('input[value="0"]').attr("checked",!0),$('#form-nav a[href="#step3"]').text(translate_javascripts["Virtual product"])):(showVariationsSelector.show(),$('#form-nav a[href="#step3"]').text(translate_javascripts.Quantities)));"2"!==typeProduct.val()&&void 0!==managedVirtualProduct&&managedVirtualProduct.destroy(),/** check quantity / combinations display */
"1"===showVariationsSelector.find("input:checked").val()||$("#accordion_combinations tr:not(#loading-attribute)").length>0?(combinationsBlock.show(),$("#specific-price-combination-selector").removeClass("hide").show(),$('#form-nav a[href="#step3"]').text(translate_javascripts.Combinations),$("#product_qty_0_shortcut_div, #quantities").hide()):(combinationsBlock.hide(),$("#specific-price-combination-selector").hide(),$("#product_qty_0_shortcut_div, #quantities").show()),/** Tooltip for product type combinations */
$('input[name="show_variations"][value="1"]:checked').length>=1?$("#product_type_combinations_shortcut").show():$("#product_type_combinations_shortcut").hide()},getProductType:function(){switch(typeProduct.val()){case"0":return"standard";case"1":return"pack";case"2":return"virtual";default:return"standard"}},/**
     * Product pack or virtual can't have variations
     * Warn e-merchant.
     * @param errorMessage
     */
checkAccessVariations:function(){if(("1"===showVariationsSelector.find("input:checked").val()||$("#accordion_combinations tr:not(#loading-attribute)").length>0)&&("1"===typeProduct.val()||"2"===typeProduct.val())){var errorMessage="You can't create "+this.getProductType()+" product with variations. Are you sure to disable variations ? they will all be deleted.";modalConfirmation.create(translate_javascripts[errorMessage],null,{onCancel:function(){typeProduct.val(0).change(),/* else the radio bouton is not display even if checked attribute is true */
$('#show_variations_selector input[value="1"]').click()},onContinue:function(){$.ajax({type:"GET",url:$("#accordion_combinations").attr("data-action-delete-all").replace(/delete-all\/\d+/,"delete-all/"+$("#form_id_product").val()),success:function(){$("#accordion_combinations .combination").remove(),displayFieldsManager.refresh()},error:function(response){showErrorMessage(jQuery.parseJSON(response.responseText).message)}})}}).show()}}}}(),displayFormCategory=function(){var parentElem=$("#add-categories");return{init:function(){/** Click event on the add button */
parentElem.find("a.open").on("click",function(e){e.preventDefault(),parentElem.find("#add-categories-content").removeClass("hide"),$(this).hide()})}}}(),formCategory=function(){var elem=$("#form_step1_new_category");return{init:function(){var that=this;/** remove all categories from selector, except pre defined */
$("#add-categories button.save").click(function(){!/** Send category form and it to nested categories */
function(form){$.ajax({type:"POST",url:elem.attr("data-action"),data:{"form[category][name]":$("#form_step1_new_category_name").val(),"form[category][id_parent]":$("#form_step1_new_category_id_parent").val(),"form[_token]":$("#form #form__token").val()},beforeSend:function(){$("button.submit",elem).attr("disabled","disabled"),$("ul.text-danger",elem).remove(),$("*.has-danger",elem).removeClass("has-danger"),$("*.has-danger").removeClass("has-danger")},success:function(response){
//inject new category into category tree
var html='<li><div class="checkbox js-checkbox"><label><input type="checkbox" name="form[step1][categories][tree][]" checked value="'+response.category.id+'">'+response.category.name[1]+'</label><div class="radio pull-right"><input type="radio" value="'+response.category.id+'" name="ignore" class="default-category"></div></div></li>',parentElement=$("#form_step1_categories input[value="+response.category.id_parent+"]").parent().parent();0===parentElement.next("ul").length?(html="<ul>"+html+"</ul>",parentElement.append(html)):parentElement.next("ul").append(html),
//inject new category in parent category selector
$("#form_step1_new_category_id_parent").append('<option value="'+response.category.id+'">'+response.category.name[1]+"</option>");
// create label
var tag={name:response.category.name[1],id:response.category.id,breadcrumb:""};productCategoriesTags.createTag(tag),
//hide the form
form.hideBlock()},error:function(response){$.each(jQuery.parseJSON(response.responseText),function(key,errors){var html='<ul class="list-unstyled text-danger">';$.each(errors,function(key,error){html+="<li>"+error+"</li>"}),html+="</ul>",$("#form_step1_new_"+key).parent().append(html),$("#form_step1_new_"+key).parent().addClass("has-danger")})},complete:function(){$("#form_step1_new_category button.submit").removeAttr("disabled")}})}(that)}),$('#add-categories button[type="reset"]').click(function(){that.hideBlock()})},hideBlock:function(){$("#form_step1_new_category_name").val(""),$("#add-category-button").show(),$("#add-categories-content").addClass("hide")}}}(),featuresCollection=function(){var collectionHolder=$(".feature-collection");return{init:function(){/** Click event on the add button */
$("#features .add").on("click",function(e){e.preventDefault(),/** Add a feature */
function(){var newForm=collectionHolder.attr("data-prototype").replace(/__name__/g,collectionHolder.children(".row").length);collectionHolder.append(newForm),prestaShopUiKit.initSelects()}(),$("#features-content").removeClass("hide")}),/** Click event on the remove button */
$(document).on("click",".feature-collection .delete",function(e){e.preventDefault();var _this=$(this);modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){_this.closest(".product-feature").remove()}}).show()}),/** On feature selector event change, refresh possible values list */
$(document).on("change",".feature-collection select.feature-selector",function(event){var that=event.currentTarget,$selector=$($(that).parents(".row")[0]).find(".feature-value-selector");""!==$(this).val()&&$.ajax({url:$(this).attr("data-action").replace(/\/\d+(?=\?.*)/,"/"+$(this).val()),success:function(response){$selector.prop("disabled",0===response.length),$selector.empty(),$.each(response,function(index,elt){
// the placeholder shouldn't be posted.
"0"==elt.id&&(elt.id=""),$selector.append($("<option></option>").attr("value",elt.id).text(elt.value))})}})});$("#features-content").on("change",'.row select, .row input[type="text"]',function(event){var that=event.currentTarget,$row=$($(that).parents(".row")[0]),$definedValueSelector=$row.find(".feature-value-selector"),$customValueSelector=$row.find("input[type=text]");
// if feature has changed we need to reset values
$(that).hasClass("feature-selector")&&($customValueSelector.val(""),$definedValueSelector.val(""))})}}}(),supplier=function(){var supplierInputManage=function(input){var supplierDefaultInput=$('#form_step6_suppliers input[name="form[step6][default_supplier]"][value='+$(input).val()+"]");$(input).is(":checked")?supplierDefaultInput.prop("disabled",!1).show():supplierDefaultInput.prop("disabled",!0).hide()};return{init:function(){$('#form_step6_suppliers input[name="form[step6][suppliers][]"]').change(function(){supplierInputManage($(this)),supplierCombinations.refresh()}),
//default display
$('#form_step6_suppliers input[name="form[step6][suppliers][]"]').map(function(){supplierInputManage($(this))})}}}(),supplierCombinations=function(){var id_product=$("#form_id_product").val(),collectionHolder=$("#supplier_combination_collection");return{refresh:function(){var suppliers=$('#form_step6_suppliers input[name="form[step6][suppliers][]"]:checked').map(function(){return $(this).val()}).get(),url=collectionHolder.attr("data-url").replace(/refresh-product-supplier-combination-form\/\d+\/\d+/,"refresh-product-supplier-combination-form/"+id_product+(suppliers.length>0?"/"+suppliers.join("-"):""));$.ajax({url:url,success:function(response){collectionHolder.empty().append(response)}})}}}(),stock={init:function(){/** Update qty_0 and shortcut qty_0 field on change */
$("#form_step1_qty_0_shortcut, #form_step3_qty_0").keyup(function(){"form_step1_qty_0_shortcut"===$(this).attr("id")?$("#form_step3_qty_0").val($(this).val()):$("#form_step1_qty_0_shortcut").val($(this).val())}),/** if GSA : Show depends_on_stock choice only if advanced_stock_management checked */
$("#form_step3_advanced_stock_management").on("change",function(e){e.target.checked?$("#depends_on_stock_div").show():$("#depends_on_stock_div").hide(),warehouseCombinations.refresh()}),/** if GSA activation change on 'depend on stock', update quantities fields */
$("#form_step3_depends_on_stock_0, #form_step3_depends_on_stock_1, #form_step3_advanced_stock_management").on("change",function(e){displayFieldsManager.refresh(),warehouseCombinations.refresh()}),displayFieldsManager.refresh()}},nav={init:function(){/** Manage tabls hash routes */
var hash=document.location.hash,formNav=$("#form-nav");hash&&formNav.find("a[href='"+hash.replace("tab-","")+"']").tab("show"),formNav.find("a").on("shown.bs.tab",function(e){e.target.hash&&(!/** on tab switch */
function(currentTab){"#step2"===currentTab&&/** each switch to price tab, reload combinations into specific price form */
specificPrices.refreshCombinationsList()}(e.target.hash),window.location.hash=e.target.hash.replace("#","#tab-"))})}},specificPrices=function(){/** Get all specific prices */
function getAll(){var url=elem.attr("data").replace(/list\/\d+/,"list/"+id_product);$.ajax({type:"GET",url:url,success:function(specific_prices){var tbody=elem.find("tbody");tbody.find("tr").remove(),specific_prices.length>0?elem.removeClass("hide"):elem.addClass("hide"),$.each(specific_prices,function(key,specific_price){var row="<tr><td>"+specific_price.rule_name+"</td><td>"+specific_price.attributes_name+"</td><td>"+specific_price.currency+"</td><td>"+specific_price.country+"</td><td>"+specific_price.group+"</td><td>"+specific_price.customer+"</td><td>"+specific_price.fixed_price+"</td><td>"+specific_price.impact+"</td><td>"+specific_price.period+"</td><td>"+specific_price.from_quantity+"</td><td>"+(specific_price.can_delete?'<a href="'+$("#js-specific-price-list").attr("data-action-delete").replace(/delete\/\d+/,"delete/"+specific_price.id_specific_price)+'" class="js-delete delete btn tooltip-link delete pl-0 pr-0"><i class="material-icons">delete</i></a>':"")+"</td></tr>";tbody.append(row)})}})}/**
   * Because all "forms" are encapsulated in a global form, we just can't use reset button
   * Reset all subform inputs values
   */
function resetForm(){$("#specific_price_form").find("input").each(function(){$(this).val(initSpecificPriceForm[$(this).attr("id")])}),$("#specific_price_form").find("select").each(function(){$(this).val(initSpecificPriceForm[$(this).attr("id")]).change()}),$("#specific_price_form").find("input:checkbox").each(function(){$(this).prop("checked",!0)})}var id_product=$("#form_id_product").val(),elem=$("#js-specific-price-list"),leaveInitialPrice=$("#form_step2_specific_price_leave_bprice"),productPriceField=$("#form_step2_specific_price_sp_price"),discountTypeField=$("#form_step2_specific_price_sp_reduction_type"),initSpecificPriceForm=($("#form_step2_specific_price_sp_reduction_tax"),new Object);return{init:function(){this.getAll(),$("#specific-price .add").click(function(){$(this).hide()}),$("#specific_price_form .js-cancel").click(function(){resetForm(),$("#specific-price > a").click(),$("#specific-price .add").click().show(),productPriceField.prop("disabled",!0)}),$("#specific_price_form .js-save").click(function(){!/**
   * Add a specific price
   * @param {object} elem - The clicked link
   */
function(elem){$.ajax({type:"POST",url:$("#specific_price_form").attr("data-action"),data:$("#specific_price_form input, #specific_price_form select, #form_id_product").serialize(),beforeSend:function(){elem.attr("disabled","disabled")},success:function(){showSuccessMessage(translate_javascripts["Form update success"]),$("#specific_price_form .js-cancel").click(),getAll()},complete:function(){elem.removeAttr("disabled")},error:function(errors){showErrorMessage(errors.responseJSON)}})}($(this))}),$(document).on("click","#js-specific-price-list .js-delete",function(e){e.preventDefault(),/**
   * Remove a specific price
   * @param {object} elem - The clicked link
   */
function(elem){modalConfirmation.create(translate_javascripts["This will delete the specific price. Do you wish to proceed?"],null,{onContinue:function(){$.ajax({type:"GET",url:elem.attr("href"),beforeSend:function(){elem.attr("disabled","disabled")},success:function(response){getAll(),resetForm(),showSuccessMessage(response)},error:function(response){showErrorMessage(response.responseJSON)},complete:function(){elem.removeAttr("disabled")}})}}).show()}($(this))}),$("#form_step2_specific_price_sp_reduction_type").change(function(){"percentage"===$(this).val()?$("#form_step2_specific_price_sp_reduction_tax").hide():$("#form_step2_specific_price_sp_reduction_tax").show()}),this.refreshCombinationsList(),/* enable price field only when needed */
leaveInitialPrice.on("click",function(){productPriceField.prop("disabled",$(this).is(":checked")).val("")}),/* enable tax type field only when reduction by amount is selected */
discountTypeField.on("change",function(){var uglySelect2Selector=$("#select2-form_step2_specific_price_sp_reduction_tax-container").parent().parent();"amount"===$(this).val()?uglySelect2Selector.show():uglySelect2Selector.hide()}),this.getInitSpecificPriceForm()},getAll:function(){getAll()},refreshCombinationsList:function(){!/** refresh combinations list selector for specific price form */
function(){var elem=$("#form_step2_specific_price_sp_id_product_attribute"),url=elem.attr("data-action").replace(/product-combinations\/\d+/,"product-combinations/"+id_product);$.ajax({type:"GET",url:url,success:function(combinations){/** remove all options except first one */
elem.find("option:gt(0)").remove(),$.each(combinations,function(key,combination){elem.append('<option value="'+combination.id+'">'+combination.name+"</option>")})}})}()},getInitSpecificPriceForm:function(){$("#specific_price_form").find("select,input").each(function(){initSpecificPriceForm[$(this).attr("id")]=$(this).val()}),$("#specific_price_form").find("input:checkbox").each(function(){initSpecificPriceForm[$(this).attr("id")]=$(this).prop("checked")})}}}(),warehouseCombinations=function(){var id_product=$("#form_id_product").val(),collectionHolder=$("#warehouse_combination_collection");return{init:function(){
// toggle all button action
$(document).on("click",'div[id^="warehouse_combination_"] button.check_all_warehouse',function(){var checkboxes=$(this).closest('div[id^="warehouse_combination_"]').find('input[type="checkbox"][id$="_activated"]');checkboxes.prop("checked",0===checkboxes.filter(":checked").size())}),
// location disablation depending on 'stored' checkbox
$(document).on("change",'div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]',function(){var checked=$(this).prop("checked"),location=$(this).closest("div.form-group").find('input[id^="form_step4_warehouse_combination_"][id$="_location"]');location.prop("disabled",!checked),checked||location.val("")}),this.locationDisabler()},locationDisabler:function(){$('div[id^="warehouse_combination_"] input[id^="form_step4_warehouse_combination_"][id$="_activated"]',collectionHolder).each(function(){var checked=$(this).prop("checked");$(this).closest("div.form-group").find('input[id^="form_step4_warehouse_combination_"][id$="_location"]').prop("disabled",!checked)})},refresh:function(){if($("input#form_step3_advanced_stock_management:checked").size()>0){var url=collectionHolder.attr("data-url").replace(/\/\d+(?=\?.*)/,"/"+id_product);$.ajax({url:url,success:function(response){collectionHolder.empty().append(response),collectionHolder.show(),warehouseCombinations.locationDisabler()}})}else collectionHolder.hide()}}}(),form=function(){function send(redirect,target,callBack){
// target value by default
void 0===target&&(target=!1),seo.onSave(),function(){var namesDiv=$("#form_step1_names"),defaultLanguageValue=null;$("input[id^='form_step1_name_']",namesDiv).each(function(index){var value=$(this).val();
// The first language is ALWAYS the employee language
0===index?defaultLanguageValue=value:0===value.length&&$(this).val(defaultLanguageValue)})}();var data=$("input, textarea, select",elem).not(":input[type=button], :input[type=submit], :input[type=reset]").serialize();if("_blank"==target&&redirect){var openBlank=window.open("about:blank",target,"");openBlank.document.write('<p style="text-align: center;"><img src="'+document.location.origin+baseAdminDir+'/themes/default/img/spinner.gif"></p>')}$.ajax({type:"POST",data:data,beforeSend:function(){$("#submit",elem).attr("disabled","disabled"),$(".btn-submit",elem).attr("disabled","disabled"),$("ul.text-danger").remove(),$("*.has-danger").removeClass("has-danger"),$("#form-nav li.has-error").removeClass("has-error")},success:function(response){callBack&&callBack(),showSuccessMessage(translate_javascripts["Form update success"]),
//update the customization ids
void 0!==response.customization_fields_ids&&$.each(response.customization_fields_ids,function(k,v){$("#form_step6_custom_fields_"+k+"_id_customization_field").val(v)}),$(".js-spinner").hide(),redirect&&(!1!==target?"_blank"===target?openBlank.location=redirect:window.open(redirect,target):window.location=redirect)},error:function(response){showErrorMessage(translate_javascripts["Form update errors"]),"_blank"==target&&redirect&&openBlank.close();var tabsWithErrors=[];if($.each(jQuery.parseJSON(response.responseText),function(key,errors){tabsWithErrors.push(key);var html='<ul class="list-unstyled text-danger">';$.each(errors,function(key,error){html+="<li>"+error+"</li>"}),html+="</ul>",key.match(/^combination_.*/)?$("#"+key).parent().addClass("has-danger").append(html):$("#form_"+key).parent().addClass("has-danger").append(html)}),/** find first tab with error, then switch to it */
tabsWithErrors.sort(),$.each(tabsWithErrors,function(key,tabIndex){0===key&&$('#form-nav li a[href="#'+tabIndex.split("_")[0]+'"]').tab("show"),$('#form-nav li a[href="#'+tabIndex.split("_")[0]+'"]').parent().addClass("has-error")}),$('div[class*="translation-label-"].has-danger').length>0){var translationLabelClass=$.grep($('div[class*="translation-label-"].has-danger').first().attr("class").split(" "),function(v,i){return 0===v.indexOf("translation-label-")}).join();if(translationLabelClass){var selectValue=translationLabelClass.replace("translation-label-","");$('#form_switch_language option[value="'+selectValue+'"]').length>0&&$("#form_switch_language").val(selectValue).change()}}/** scroll to 1st error */
$(".has-danger").first().offset()&&$("html, body").animate({scrollTop:$(".has-danger").first().offset().top-$("nav.main-header").height()},500)},complete:function(){$("#submit",elem).removeAttr("disabled"),$(".btn-submit",elem).removeAttr("disabled")}})}function switchLanguage(iso_code){$("div.translations.tabbable > div > div.translation-field:not(.translation-label-"+iso_code+")").removeClass("visible"),$("div.translations.tabbable > div > div.translation-field.translation-label-"+iso_code).addClass("visible")}var elem=$("#form");return{init:function(){/** prevent form submit on ENTER keypress */
jwerty.key("enter",function(e){e.preventDefault()}),/** create keyboard event for save */
jwerty.key("alt+shift+S",function(e){e.preventDefault(),send()}),/** create keyboard event for save & duplicate */
jwerty.key("alt+shift+D",function(e){e.preventDefault(),send($(".product-footer .duplicate").attr("data-redirect"))}),/** create keyboard event for save & new */
jwerty.key("alt+shift+P",function(e){e.preventDefault(),send($(".product-footer .new-product").attr("data-redirect"))}),/** create keyboard event for save & go catalog */
jwerty.key("alt+shift+Q",function(e){e.preventDefault(),send($(".product-footer .go-catalog").attr("data-redirect"))}),/** create keyboard event for save & go preview */
jwerty.key("alt+shift+V",function(e){e.preventDefault();var productFooter=$(".product-footer .preview");send(productFooter.attr("data-redirect"),productFooter.attr("target"))}),/** create keyboard event for save & active or desactive product*/
jwerty.key("alt+shift+O",function(e){e.preventDefault();var step1CheckBox=$("#form_step1_active");step1CheckBox.prop("checked",!step1CheckBox.is(":checked"))}),elem.submit(function(event){event.preventDefault(),send()}),elem.find("#form_switch_language").change(function(event){event.preventDefault(),switchLanguage(event.target.value)}),/** on save with duplicate|new|preview */
$(".btn-submit, .preview",elem).click(function(event){event.preventDefault(),send($(this).attr("data-redirect"),$(this).attr("target"))}),$(".js-btn-save").on("click",function(event){event.preventDefault(),$(".js-spinner").css("display","inline-block"),send($(this).attr("href"))}),/** on active field change, send form */
$("#form_step1_active",elem).on("change",function(){var active=$(this).prop("checked");$(".for-switch.online-title").toggle(active),$(".for-switch.offline-title").toggle(!active);
// update link preview
var previewButton=$("#product_form_preview_btn"),urlActive=previewButton.attr("data-redirect"),urlDeactive=previewButton.attr("data-url-deactive");previewButton.attr("data-redirect",urlDeactive),previewButton.attr("data-url-deactive",urlActive),
// update product
send()}),/** on delete product */
$(".product-footer .delete",elem).click(function(e){e.preventDefault();var _this=$(this);modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){window.location=_this.attr("href")}}).show()}),$("#form-loading").fadeIn(function(){/** Create Bloodhound engine */
var engine=new Bloodhound({datumTokenizer:function(d){return Bloodhound.tokenizers.whitespace(d.label)},queryTokenizer:Bloodhound.tokenizers.whitespace,prefetch:{url:$("#form_step3_attributes").attr("data-prefetch"),cache:!1}});/** init input typeahead */
$("#form_step3_attributes").tokenfield({typeahead:[{hint:!1,cache:!1},{source:function(query,syncResults){engine.search(query,function(suggestions){syncResults(filter(suggestions))})},display:"label"}],minWidth:"768px"});/** Filter suggestion with selected tokens */
var filter=function(suggestions){var selected=[];return $("#attributes-generator input.attribute-generator").each(function(){selected.push($(this).val())}),$.grep(suggestions,function(suggestion){return-1===$.inArray(suggestion.value,selected)&&-1===$.inArray("group-"+suggestion.data.id_group,selected)})};/** On event "tokenfield:createtoken" : stop event if its not a typehead result */
$("#form_step3_attributes").on("tokenfield:createtoken",function(e){if(!e.attrs.data&&"tokenfield:createtoken"!==e.handleObj.origType)return!1}),/** On event "tokenfield:createdtoken" : store attributes in input when add a token */
$("#form_step3_attributes").on("tokenfield:createdtoken",function(e){e.attrs.data?$("#attributes-generator").append('<input type="hidden" id="attribute-generator-'+e.attrs.value+'" class="attribute-generator" value="'+e.attrs.value+'" name="options['+e.attrs.data.id_group+"]["+e.attrs.value+']" />'):"tokenfield:createdtoken"==e.handleObj.origType&&$("#attributes-generator").append('<input type="hidden" id="attribute-generator-'+$('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data("value")+'" class="attribute-generator" value="'+$('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data("value")+'" name="options['+$('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data("group-id")+"]["+$('.js-attribute-checkbox[data-value="'+e.attrs.value+'"]').data("value")+']" />')}),/** On event "tokenfield:removedtoken" : remove stored attributes input when remove token */
$("#form_step3_attributes").on("tokenfield:removedtoken",function(e){$("#attribute-generator-"+e.attrs.value).remove()})})},send:function(redirect,target,callBack){send(redirect,target,callBack)},switchLanguage:function(iso_code){switchLanguage(iso_code)}}}(),customFieldCollection=function(){var collectionHolder=$("ul.customFieldCollection");return{init:function(){/** Click event on the add button */
$("#custom_fields a.add").on("click",function(e){e.preventDefault(),/** Add a custom field */
function(){var newForm=collectionHolder.attr("data-prototype").replace(/__name__/g,collectionHolder.children().length);collectionHolder.append("<li>"+newForm+"</li>")}()}),/** Click event on the remove button */
$(document).on("click","ul.customFieldCollection .delete",function(e){e.preventDefault();var _this=$(this);modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){_this.parent().parent().parent().remove()}}).show()})}}}(),virtualProduct=function(){var id_product=$("#form_id_product").val(),getOnDeleteVirtualProductFileHandler=function($deleteButton){return $.ajax({type:"GET",url:$deleteButton.attr("href").replace(/\/\d+(?=\?.*)/,"/"+id_product),success:function(){$("#form_step3_virtual_product_file_input").removeClass("hide").addClass("show"),$("#form_step3_virtual_product_file_details").removeClass("show").addClass("hide")}})};return{init:function(){$(document).on("change",'input[name="form[step3][virtual_product][is_virtual_file]"]',function(){if("1"===$(this).val())$("#virtual_product_content").show();else{$("#virtual_product_content").hide();var url=$("#virtual_product").attr("data-action-remove").replace(/remove\/\d+/,"remove/"+id_product);
//delete virtual product
$.ajax({type:"GET",url:url,success:function(){
//empty form
$("#form_step3_virtual_product_file_input").removeClass("hide").addClass("show"),$("#form_step3_virtual_product_file_details").removeClass("show").addClass("hide"),$("#form_step3_virtual_product_name").val(""),$("#form_step3_virtual_product_nb_downloadable").val(0),$("#form_step3_virtual_product_expiration_date").val(""),$("#form_step3_virtual_product_nb_days").val(0)}})}}),$("#form_step3_virtual_product_file").change(function(e){if(void 0!==$(this)[0].files){var files=$(this)[0].files,name="";$.each(files,function(index,value){name+=value.name+", "}),$("#form_step3_virtual_product_name").val(name.slice(0,-2))}else{
// Internet Explorer 9 Compatibility
name=$(this).val().split(/[\\/]/);$("#form_step3_virtual_product_name").val(name[name.length-1])}}),"1"===$('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val()?$("#virtual_product_content").show():$("#virtual_product_content").hide(),/** delete attached file */
$("#form_step3_virtual_product_file_details .delete").click(function(e){e.preventDefault();var $deleteButton=$(this);modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){getOnDeleteVirtualProductFileHandler($deleteButton)}}).show()}),/** save virtual product */
$("#form_step3_virtual_product_save").click(function(){var _this=$(this),data=new FormData;$("#form_step3_virtual_product_file")[0].files[0]&&data.append("product_virtual[file]",$("#form_step3_virtual_product_file")[0].files[0]),data.append("product_virtual[is_virtual_file]",$('input[name="form[step3][virtual_product][is_virtual_file]"]:checked').val()),data.append("product_virtual[name]",$("#form_step3_virtual_product_name").val()),data.append("product_virtual[nb_downloadable]",$("#form_step3_virtual_product_nb_downloadable").val()),data.append("product_virtual[expiration_date]",$("#form_step3_virtual_product_expiration_date").val()),data.append("product_virtual[nb_days]",$("#form_step3_virtual_product_nb_days").val()),$.ajax({type:"POST",url:$("#virtual_product").attr("data-action").replace(/save\/\d+/,"save/"+id_product),data:data,contentType:!1,processData:!1,beforeSend:function(){_this.prop("disabled","disabled"),$("ul.text-danger").remove(),$("*.has-danger").removeClass("has-danger")},success:function(response){showSuccessMessage(translate_javascripts["Form update success"]),response.file_download_link&&($("#form_step3_virtual_product_file_details a.download").attr("href",response.file_download_link),$("#form_step3_virtual_product_file_input").removeClass("show").addClass("hide"),$("#form_step3_virtual_product_file_details").removeClass("hide").addClass("show"))},error:function(response){$.each(jQuery.parseJSON(response.responseText),function(key,errors){var html='<ul class="list-unstyled text-danger">';$.each(errors,function(key,error){html+="<li>"+error+"</li>"}),html+="</ul>",$("#form_step3_virtual_product_"+key).parent().append(html),$("#form_step3_virtual_product_"+key).parent().addClass("has-danger")})},complete:function(){_this.removeAttr("disabled")}})})},destroy:function(){if(!$("#form_step3_virtual_product_file_details").hasClass("hide")){var $deleteButton=$("#form_step3_virtual_product_file_details .delete");getOnDeleteVirtualProductFileHandler($deleteButton)}$("#form_step3_virtual_product_is_virtual_file_0").prop("checked",!1),$("#form_step3_virtual_product_is_virtual_file_1").prop("checked",!0),$("#virtual_product_content input").val("")}}}(),attachmentProduct=function(){var id_product=$("#form_id_product").val();return{init:function(){function resetAttachmentForm(){$("#form_step6_attachment_product_file").val(""),$("#form_step6_attachment_product_name").val(""),$("#form_step6_attachment_product_description").val("")}var buttonSave=$("#form_step6_attachment_product_add"),buttonCancel=$("#form_step6_attachment_product_cancel");/** check all attachments files */
$("#product-attachment-files-check").change(function(){$(this).is(":checked")?$('#product-attachment-file input[type="checkbox"]').prop("checked",!0):$('#product-attachment-file input[type="checkbox"]').prop("checked",!1)}),buttonCancel.click(function(){resetAttachmentForm()}),/** add attachment */
$("#form_step6_attachment_product_add").click(function(){$(this);var data=new FormData;$("#form_step6_attachment_product_file")[0].files[0]&&data.append("product_attachment[file]",$("#form_step6_attachment_product_file")[0].files[0]),data.append("product_attachment[name]",$("#form_step6_attachment_product_name").val()),data.append("product_attachment[description]",$("#form_step6_attachment_product_description").val()),$.ajax({type:"POST",url:$("#form_step6_attachment_product").attr("data-action").replace(/\/\d+(?=\?.*)/,"/"+id_product),data:data,contentType:!1,processData:!1,beforeSend:function(){buttonSave.prop("disabled","disabled"),$("ul.text-danger").remove(),$("*.has-danger").removeClass("has-danger")},success:function(response){
//inject new attachment in attachment list
if(resetAttachmentForm(),response.id){var row='<tr>                <td class="col-md-3"><input type="checkbox" name="form[step6][attachments][]" value="'+response.id+'" checked="checked"> '+response.real_name+'</td>                <td class="col-md-6">'+response.file_name+'</td>                <td class="col-md-2">'+response.mime+"</td>              </tr>";$("#product-attachment-file tbody").append(row),$(".js-options-no-attachments").addClass("hide"),$(".js-options-with-attachments").removeClass("hide")}},error:function(response){$.each(jQuery.parseJSON(response.responseText),function(key,errors){var html='<ul class="list-unstyled text-danger">';$.each(errors,function(key,error){html+="<li>"+error+"</li>"}),html+="</ul>",$("#form_step6_attachment_product_"+key).parent().append(html),$("#form_step6_attachment_product_"+key).parent().addClass("has-danger")})},complete:function(){buttonSave.removeAttr("disabled")}})})}}}(),imagesProduct=function(){function checkDropzoneMode(){dropZoneElem.find(".dz-preview:not(.openfilemanager)").length?dropZoneElem.find(".dz-preview.openfilemanager").show():(dropZoneElem.removeClass("dz-started"),dropZoneElem.find(".dz-preview.openfilemanager").hide())}var dropZoneElem=$("#product-images-dropzone"),expanderElem=$("#product-images-container .dropzone-expander");return{toggleExpand:function(){expanderElem.hasClass("expand")?(dropZoneElem.css("height","auto"),expanderElem.removeClass("expand").addClass("compress")):(dropZoneElem.css("height",""),expanderElem.removeClass("compress").addClass("expand"))},displayExpander:function(){expanderElem.show()},hideExpander:function(){expanderElem.hide()},shouldDisplayExpander:function(){var oldHeight=dropZoneElem.css("height");dropZoneElem.css("height","");var closedHeight=dropZoneElem.outerHeight(),realHeight=dropZoneElem[0].scrollHeight;return dropZoneElem.css("height",oldHeight),realHeight>closedHeight},updateExpander:function(){this.shouldDisplayExpander()&&this.displayExpander()},initExpander:function(){this.shouldDisplayExpander()&&(this.displayExpander(),expanderElem.addClass("expand"));var self=this;$(document).on("click","#product-images-container .dropzone-expander",function(){self.toggleExpand()})},init:function(){Dropzone.autoDiscover=!1;var errorElem=$("#product-images-dropzone-error");
//on click image, display custom form
$(document).on("click","#product-images-dropzone .dz-preview",function(){$(this).attr("data-id")&&formImagesProduct.form($(this).attr("data-id"))});var dropzoneOptions={url:dropZoneElem.attr("url-upload"),paramName:"form[file]",maxFilesize:dropZoneElem.attr("data-max-size"),addRemoveLinks:!0,clickable:".openfilemanager",thumbnailWidth:250,thumbnailHeight:null,acceptedFiles:"image/*",dictRemoveFile:translate_javascripts.Delete,dictFileTooBig:translate_javascripts.ToLargeFile,dictCancelUpload:translate_javascripts.Delete,sending:function(file,response){checkDropzoneMode(),expanderElem.addClass("expand").click(),errorElem.html("")},queuecomplete:function(){checkDropzoneMode(),dropZoneElem.sortable("enable"),imagesProduct.updateExpander()},processing:function(){dropZoneElem.sortable("disable")},success:function(file,response){
//manage error on uploaded file
if(0!==response.error)return errorElem.append("<p>"+file.name+": "+response.error+"</p>"),void this.removeFile(file);
//define id image to file preview
$(file.previewElement).attr("data-id",response.id),$(file.previewElement).attr("url-update",response.url_update),$(file.previewElement).attr("url-delete",response.url_delete),$(file.previewElement).addClass("ui-sortable-handle"),1===response.cover&&imagesProduct.updateDisplayCover(response.id)},error:function(file,response){var message="";"undefined"!==$.type(response)&&("string"===$.type(response)?message=response:response.message&&(message=response.message),""!==message&&(
//append new error
errorElem.append("<p>"+file.name+": "+message+"</p>"),
//remove uploaded item
this.removeFile(file)))},init:function(){
//if already images uploaded, mask drop file message
dropZoneElem.find(".dz-preview:not(.openfilemanager)").length?dropZoneElem.addClass("dz-started"):dropZoneElem.find(".dz-preview.openfilemanager").hide(),
//init sortable
dropZoneElem.sortable({items:"div.dz-preview:not(.disabled)",opacity:.9,containment:"parent",distance:32,tolerance:"pointer",cursorAt:{left:64,top:64},cancel:".disabled",stop:function(event,ui){var sort={};$.each(dropZoneElem.find(".dz-preview:not(.disabled)"),function(index,value){$(value).attr("data-id")?sort[$(value).attr("data-id")]=index+1:sort=!1}),
//if sortable ok, update it
sort&&$.ajax({type:"POST",url:dropZoneElem.attr("url-position"),data:{json:JSON.stringify(sort)}})},start:function(event,ui){
//init zindex
dropZoneElem.find(".dz-preview").css("zIndex",1),ui.item.css("zIndex",10)}}),dropZoneElem.disableSelection(),imagesProduct.initExpander()}};dropZoneElem.dropzone(jQuery.extend(dropzoneOptions))},updateDisplayCover:function(id_image){$("#product-images-dropzone .dz-preview .iscover").remove(),$('#product-images-dropzone .dz-preview[data-id="'+id_image+'"]').append('<div class="iscover">'+translate_javascripts.Cover+"</div>")},checkDropzoneMode:function(){checkDropzoneMode()},getOlderImageId:function(){return Math.min.apply(Math,$(".dz-preview").map(function(){return $(this).data("id")}))}}}(),formImagesProduct=function(){function toggleColDropzone(enlarge){!0===enlarge?dropZoneElem.removeClass("col-md-8").addClass("col-md-12"):dropZoneElem.removeClass("col-md-12").addClass("col-md-8")}var dropZoneElem=$("#product-images-dropzone"),formZoneElem=$("#product-images-form-container");return formZoneElem.magnificPopup({delegate:"a.open-image",type:"image"}),{form:function(id){dropZoneElem.find(".dz-preview.active").removeClass("active"),dropZoneElem.find(".dz-preview[data-id='"+id+"']").addClass("active"),0==imagesProduct.shouldDisplayExpander()&&dropZoneElem.css("height","auto"),$.ajax({url:dropZoneElem.find(".dz-preview[data-id='"+id+"']").attr("url-update"),success:function(response){formZoneElem.find("#product-images-form").html(response),form.switchLanguage($("#form_switch_language").val())},complete:function(){toggleColDropzone(!1),formZoneElem.show()}})},send:function(id){$.ajax({type:"POST",url:dropZoneElem.find(".dz-preview[data-id='"+id+"']").attr("url-update"),data:formZoneElem.find("textarea, input").serialize(),beforeSend:function(){formZoneElem.find(".actions button").prop("disabled","disabled"),formZoneElem.find("ul.text-danger").remove(),formZoneElem.find("*.has-danger").removeClass("has-danger")},success:function(){formZoneElem.find("#form_image_cover:checked").length&&imagesProduct.updateDisplayCover(id)},error:function(response){response&&response.responseText&&$.each(jQuery.parseJSON(response.responseText),function(key,errors){var html='<ul class="list-unstyled text-danger">';$.each(errors,function(key,error){html+="<li>"+error+"</li>"}),html+="</ul>",$("#form_image_"+key).parent().append(html),$("#form_image_"+key).parent().addClass("has-danger")})},complete:function(){formZoneElem.find(".actions button").removeAttr("disabled")}})},delete:function(id){modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){$.ajax({url:dropZoneElem.find('.dz-preview[data-id="'+id+'"]').attr("url-delete"),complete:function(){formZoneElem.find(".close").click();var wasCover=!!dropZoneElem.find('.dz-preview[data-id="'+id+'"] .iscover').length;dropZoneElem.find('.dz-preview[data-id="'+id+'"]').remove(),$(".images .product-combination-image [value="+id+"]").parent().remove(),imagesProduct.checkDropzoneMode(),!0===wasCover&&
// The controller will choose the oldest image as the new cover.
imagesProduct.updateDisplayCover(imagesProduct.getOlderImageId())}})}}).show()},close:function(){toggleColDropzone(!0),dropZoneElem.css("height",""),formZoneElem.find("#product-images-form").html(""),formZoneElem.hide(),dropZoneElem.find(".dz-preview.active").removeClass("active")}}}(),priceCalculation=function(){/**
   * Add taxes to a price
   * @param {Number} price - Price without tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod The computation calculate method
   */
function addTaxes(price,rates,computationMethod){var price_with_taxes=price,i=0;if("0"===computationMethod)for(i in rates){price_with_taxes*=1+parseFloat(rates[i])/100;break}else if("1"===computationMethod){var rate=0;for(i in rates)rate+=rates[i];price_with_taxes*=1+parseFloat(rate)/100}else if("2"===computationMethod)for(i in rates)price_with_taxes*=1+parseFloat(rates[i])/100;return price_with_taxes}/**
   * Remove taxes from a price
   * @param {Number} price - Price with tax
   * @param {Number[]} rates - Rates to apply
   * @param {Number} computationMethod - The computation method
   */
function removeTaxes(price,rates,computationMethod){var i=0;if("0"===computationMethod)for(i in rates){price/=1+rates[i]/100;break}else if("1"===computationMethod){var rate=0;for(i in rates)rate+=rates[i];price/=1+rate/100}else if("2"===computationMethod)for(i in rates)price/=1+rates[i]/100;return price}/**
   *
   * @return {Number}
   */
function getEcotaxTaxIncluded(){var ecoTax=Tools.parseFloatFromString(ecoTaxElem.val());if(isNaN(ecoTax)&&(ecoTax=0),0===ecoTax)return ecoTax;var ecotaxTaxExcl=ecoTax/(1+ecoTaxRate);return ps_round(ecotaxTaxExcl*(1+ecoTaxRate),6)}var priceHTElem=$("#form_step2_price"),priceHTShortcutElem=$("#form_step1_price_shortcut"),priceTTCElem=$("#form_step2_price_ttc"),priceTTCShorcutElem=$("#form_step1_price_ttc_shortcut"),ecoTaxElem=$("#form_step2_ecotax"),taxElem=$("#form_step2_id_tax_rules_group"),reTaxElem=$("#step2_id_tax_rules_group_rendered"),displayPricePrecision=priceHTElem.attr("data-display-price-precision"),ecoTaxRate=ecoTaxElem.attr("data-eco-tax-rate");return{init:function(){/** on update tax recalculate tax include price */
taxElem.change(function(){reTaxElem.val()!==taxElem.val()&&reTaxElem.val(taxElem.val()).trigger("change"),priceCalculation.taxInclude(),priceTTCElem.change()}),reTaxElem.change(function(){taxElem.val(reTaxElem.val()).trigger("change")}),/** update without tax price and shortcut price field on change */
$("#form_step1_price_shortcut, #form_step2_price").keyup(function(){var price=priceCalculation.normalizePrice($(this).val());"form_step1_price_shortcut"===$(this).attr("id")?$("#form_step2_price").val(price).change():$("#form_step1_price_shortcut").val(price).change(),priceCalculation.taxInclude()}),/** update HT price and shortcut price field on change */
$("#form_step1_price_ttc_shortcut, #form_step2_price_ttc").keyup(function(){var price=priceCalculation.normalizePrice($(this).val());"form_step1_price_ttc_shortcut"===$(this).attr("id")?$("#form_step2_price_ttc").val(price).change():$("#form_step1_price_ttc_shortcut").val(price).change(),priceCalculation.taxExclude()}),/** on price change, update final retails prices */
$("#form_step2_price, #form_step2_price_ttc").change(function(){var taxExcludedPrice=priceCalculation.normalizePrice($("#form_step2_price").val()),taxIncludedPrice=priceCalculation.normalizePrice($("#form_step2_price_ttc").val());formatCurrencyCldr(taxExcludedPrice,function(result){$("#final_retail_price_te").text(result)}),formatCurrencyCldr(taxIncludedPrice,function(result){$("#final_retail_price_ti").text(result)})}),/** update HT price and shortcut price field on change */
$("#form_step2_ecotax").keyup(function(){priceCalculation.taxExclude()}),/** combinations : update TTC price field on change */
$(document).on("keyup",".combination-form .attribute_priceTE",function(){priceCalculation.impactTaxInclude($(this)),priceCalculation.impactFinalPrice($(this))}),/** combinations : update HT price field on change */
$(document).on("keyup",".combination-form .attribute_priceTI",function(){priceCalculation.impactTaxExclude($(this))}),/** combinations : update wholesale price, unity and price TE field on blur */
$(document).on("blur",".combination-form .attribute_wholesale_price,.combination-form .attribute_unity,.combination-form .attribute_priceTE",function(){$(this).val(priceCalculation.normalizePrice($(this).val()))}),priceCalculation.taxInclude(),$("#form_step2_price, #form_step2_price_ttc").change()},/**
     * Converts a price string into a number
     * @param {String} price
     * @return {Number}
     */
normalizePrice:function(price){return Tools.parseFloatFromString(price,!0)},/**
     * Adds taxes to a price
     * @param {Number} price Price without taxes
     * @return {Number} Price with added taxes
     */
addCurrentTax:function(price){var rates=this.getRates(),computation_method=taxElem.find("option:selected").attr("data-computation-method");return Number(ps_round(addTaxes(price,rates,computation_method),displayPricePrecision))+Number(getEcotaxTaxIncluded())},/**
     * Calculates the price with taxes and updates the elements containing it
     */
taxInclude:function(){var newPrice=truncateDecimals(this.addCurrentTax(this.normalizePrice(priceHTElem.val())),6);priceTTCElem.val(newPrice).change(),priceTTCShorcutElem.val(newPrice).change()},/**
     * Removes taxes from a price
     * @param {Number} price Price with taxes
     * @return {Number} Price without taxes
     */
removeCurrentTax:function(price){var rates=this.getRates(),computation_method=taxElem.find("option:selected").attr("data-computation-method");return ps_round(removeTaxes(ps_round(price-getEcotaxTaxIncluded(),displayPricePrecision),rates,computation_method),displayPricePrecision)},/**
     * Calculates the price without taxes and updates the elements containing it
     */
taxExclude:function(){var newPrice=truncateDecimals(this.removeCurrentTax(this.normalizePrice(priceTTCElem.val())),6);priceHTElem.val(newPrice).change(),priceHTShortcutElem.val(newPrice).change()},/**
     * Calculates and displays the impact on price (including tax) for a combination
     * @param {jQuery} obj
     */
impactTaxInclude:function(obj){var price=Tools.parseFloatFromString(obj.val()),targetInput=obj.closest('div[id^="combination_form_"]').find("input.attribute_priceTI"),newPrice=0;if(!isNaN(price)){var rates=this.getRates(),computation_method=taxElem.find("option:selected").attr("data-computation-method");newPrice=ps_round(addTaxes(price,rates,computation_method),6),newPrice=truncateDecimals(newPrice,6)}targetInput.val(newPrice).trigger("change")},/**
     * Calculates and displays the final price for a combination
     * @param {jQuery} obj
     */
impactFinalPrice:function(obj){var price=this.normalizePrice(obj.val()),finalPrice=obj.closest('div[id^="combination_form_"]').find(".final-price"),defaultFinalPrice=finalPrice.attr("data-price"),priceToBeChanged=Number(price)+Number(defaultFinalPrice);priceToBeChanged=truncateDecimals(priceToBeChanged,6),finalPrice.html(priceToBeChanged)},/**
     * Calculates and displays the impact on price (excluding tax) for a combination
     * @param {jQuery} obj
     */
impactTaxExclude:function(obj){var price=Tools.parseFloatFromString(obj.val()),targetInput=obj.closest('div[id^="combination_form_"]').find("input.attribute_priceTE"),newPrice=0;if(!isNaN(price)){var rates=this.getRates(),computation_method=taxElem.find("option:selected").attr("data-computation-method");newPrice=removeTaxes(ps_round(price,displayPricePrecision),rates,computation_method),newPrice=truncateDecimals(newPrice,6)}targetInput.val(newPrice).trigger("change")},/**
     * Returns the tax rates that apply
     * @return {Number[]}
     */
getRates:function(){return taxElem.find("option:selected").attr("data-rates").split(",").map(function(rate){return Tools.parseFloatFromString(rate,!0)})}}}(),seo=function(){/** Hide or show the input product selector */
function hideShowRedirectToProduct(){"404"===redirectTypeElem.val()?$("#id-product-redirected").hide():(updateRemoteUrl(),$("#id-product-redirected").show())}function updateRemoteUrl(){switch(redirectTypeElem.val()){case"301-category":case"302-category":productRedirect.find("label").html(redirectTypeElem.attr("data-labelcategory")),productRedirect.find("input").attr("placeholder",redirectTypeElem.attr("data-placeholdercategory"));break;default:productRedirect.find("label").html(redirectTypeElem.attr("data-labelproduct")),productRedirect.find("input").attr("placeholder",redirectTypeElem.attr("data-placeholderproduct"))}productRedirect.find(".autocomplete-search").attr("data-remoteurl",redirectTypeElem.find("option:selected").data("remoteurl")),productRedirect.find(".autocomplete-search").trigger("buildTypeahead")}var redirectTypeElem=$("#form_step5_redirect_type"),productRedirect=$("#id-product-redirected"),updateFriendlyUrl=function(elem){/** Attr name equals "form[step1][name][1]".
     * We need in this string the second integer */
var id_lang=elem.attr("name").match(/\d+/g)[1];$("#form_step5_link_rewrite_"+id_lang).val(str2url(elem.val(),"UTF-8"))};return{init:function(){hideShowRedirectToProduct(),updateRemoteUrl(),/** On redirect type select change */
redirectTypeElem.change(function(){productRedirect.find("#form_step5_id_type_redirected-data").html(""),hideShowRedirectToProduct()}),/** On product title change, update friendly URL*/
$("#form_step1_names.friendly-url-force-update input").keyup(function(){updateFriendlyUrl($(this))}),/** Reset all languages title to friendly url*/
$("#seo-url-regenerate").click(function(){$.each($("#form_step1_names input"),function(){updateFriendlyUrl($(this))})})},onSave:function(){
// check all friendly URLs have been filled. If not, fill them.
$('input[id^="form_step5_link_rewrite_"]',"#form_step5_link_rewrite").each(function(){var elem=$(this);if(0===elem.val().length){var id_lang=elem.attr("name").match(/\d+/g)[1];updateFriendlyUrl($("#form_step1_name_"+id_lang))}})}}}(),tags={init:function(){$("#form_step6_tags .tokenfield").tokenfield({minWidth:"768px"})}},recommendedModules={init:function(){this.moduleActionMenuLinkSelectors="button.module_action_menu_install, button.module_action_menu_enable, button.module_action_menu_uninstall, button.module_action_menu_disable, button.module_action_menu_reset, button.module_action_menu_update",$(this.moduleActionMenuLinkSelectors).on("module_card_action_event",this.saveProduct)},saveProduct:function(event,action){form.send()}}},/* 5 */
/***/
function(module,exports){({init:function(){var addButton=$("#add_brand_button"),resetButton=$("#reset_brand_product"),manufacturerContent=$("#manufacturer-content"),selectManufacturer=$("#form_step1_id_manufacturer");/** Click event on the add button */
addButton.on("click",function(e){e.preventDefault(),manufacturerContent.removeClass("hide"),addButton.hide()}),resetButton.on("click",function(e){e.preventDefault(),modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){manufacturerContent.addClass("hide"),selectManufacturer.val("").trigger("change"),addButton.show()}}).show()})}}).init()},/* 6 */
/***/
function(module,exports){({init:function(){var addButton=$("#add-related-product-button"),resetButton=$("#reset_related_product"),relatedContent=$("#related-content"),productItems=$("#form_step1_related_products-data"),searchProductsBar=$("#form_step1_related_products");addButton.on("click",function(e){e.preventDefault(),relatedContent.removeClass("hide"),addButton.hide()}),resetButton.on("click",function(e){e.preventDefault(),modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){productItems.find("li").toArray().forEach(function(item){item.remove()}),searchProductsBar.val(""),relatedContent.addClass("hide"),addButton.show()}}).show()})}}).init()},/* 7 */
/***/
function(module,exports){(function(){var defaultCategoryForm=$("#form_step1_id_category_default"),categoriesForm=$("#form_step1_categories"),tagsContainer=$("#ps_categoryTags");return{init:function(){selectedCategories=this.getTags(),selectedCategories.forEach(this.createTag),
// add tags management
this.manageTagsOnInput(),this.manageTagsOnTags(),
// add default category management
this.checkDefaultCategory(),
// add search box
this.initSearchBox()},removeTag:function(categoryId){return $('span[data-id^="'+categoryId+'"]').parent().remove(),!0},getTags:function(){var tags=[],that=this;return $("#form_step1_categories").find("label > input[type=checkbox]:checked").toArray().forEach(function(input){var tree=that.getTree(),tag={name:input.parentNode.innerText,id:input.value};tree.forEach(function(_category){_category.id==tag.id&&(tag.breadcrumb=_category.breadcrumb)}),tags.push(tag)}),tags},manageTagsOnInput:function(){var that=this;return $("#form_step1_categories").on("change","input[type=checkbox]",function(event){var input=$(this);if(!1===input.prop("checked"))that.removeTag($(this).val());else{var tag={name:input.parent().text(),id:input.val(),breadcrumb:""};that.createTag(tag)}}),!0},manageTagsOnTags:function(){var that=this;return tagsContainer.on("click","a.pstaggerClosingCross",function(event){event.preventDefault();var id=$(this).data("id");that.removeTag(id),categoriesForm.find('input[value="'+id+'"].category').prop("checked",!1),tagsContainer.focus()}),!0},checkDefaultCategory:function(){$("#form_step1_categories").find('input[value=""].default-category').prop("checked",!0)},getTree:function(){return JSON.parse($("#ps_categoryTree").html())},createTag:function(category){if(""==category.breadcrumb){this.getTree().forEach(function(_category){_category.id==category.id&&(category.breadcrumb=_category.breadcrumb)})}if(0==tagsContainer.find("span[data-id="+category.id+"]").length){tagsContainer.append('<span class="pstaggerTag"><span data-id="'+category.id+'" title="'+category.breadcrumb+'">'+category.name+'</span><a class="pstaggerClosingCross" href="#" data-id="'+category.id+'">x</a></span>');var optionId="#form_step1_id_category_default_"+category.id;0==$(optionId).length&&defaultCategoryForm.append('<div class="radio"><label class="required"><input type="radio"id="form_step1_id_category_default_'+category.id+'" name="form[step1][id_category_default]" required="required" value="'+category.id+'">'+category.name+"</label></div>")}return!0},getNameFromBreadcrumb:function(name){return-1!==name.indexOf("&gt;")?name.substring(name.lastIndexOf("&gt")+4):name},initSearchBox:function(){var searchBox=$("#ps-select-product-category"),tags=[],that=this;let searchResultMsg="";this.getTree().forEach(function(tagObject){tags.push({label:tagObject.breadcrumb,value:tagObject.id})}),searchBox.autocomplete({source:tags,minChars:2,autoFill:!0,max:20,matchContains:!0,mustMatch:!1,scroll:!1,focus:function(event,ui){event.preventDefault();let $this=$(this);$this.val(that.getNameFromBreadcrumb(ui.item.label)),searchResultMsg=$this.parent().find("[role=status]").text()},select:function(event,ui){event.preventDefault();var label=ui.item.label,categoryName=that.getNameFromBreadcrumb(label),categoryId=ui.item.value;that.createTag({name:categoryName,id:categoryId,breadcrumb:label});$("#form_step1_categories").find('input[value="'+categoryId+'"].category').prop("checked",!0),$(this).val("")}}).data("ui-autocomplete")._renderItem=function(ul,item){return $("<li>").data("ui-autocomplete-item",item).append("<a>"+item.label+"</a>").appendTo(ul)},searchBox.parent().find("[role=status]").on("DOMSubtreeModified",function(){let $this=$(this);$.isNumeric($this.text())&&""!==searchResultMsg&&""!==searchBox.val()&&$this.text(searchResultMsg)}),$("body").on("focusout","#ps-select-product-category",function(event){var $searchInput=$(event.currentTarget);0===$searchInput.val().length&&($searchInput.parent().find("[role=status]").text(""),searchResultMsg="")})}}})().init()},/* 8 */
/***/
function(module,exports){(function(){var defaultCategoryForm=$("#form_step1_id_category_default");return{init:function(){/** Populate category tree with the default category **/
var defaultCategoryId=defaultCategoryForm.find("input:checked").val();this.checkDefaultCategory(defaultCategoryId),/** Hide the default form, if javascript disabled it will be visible and so we
       * still can select a default category using the form
       */
defaultCategoryForm.hide()},checkDefaultCategory:function(categoryId){var selector='input[value="'+categoryId+'"].default-category';$("#form_step1_categories").find(selector).prop("checked",!0)},/**
     * Check the radio bouton with the selected value
     */
check:function(value){defaultCategoryForm.find('input[value="'+value+'"]').prop("checked",!0)},isChecked:function(value){return defaultCategoryForm.find('input[value="'+value+'"]').is(":checked")},/**
     * When the category selected as a default is unselected
     * The default category MUST be a selected category
     */
reset:function(){var firstInput=defaultCategoryForm.find("input:first-child");firstInput.prop("checked",!0);var categoryId=firstInput.val();this.checkDefaultCategory(categoryId)}}})().init()},/* 9 */
/***/
function(module,exports){/**
 * Combination management
 */
var combinations=function(){/**
   * Update final price, regarding the impact on price in combinations table
   * @param {jQuery} tableRow - Table row that contains the combination
   */
function updateFinalPrice(tableRow){if(!tableRow.is("tr"))throw new Error("Structure of table has changed, this function needs to be updated.");var priceImpactInput=tableRow.find(".attribute_priceTE").first(),finalPriceLabel=tableRow.find(".attribute-finalprice span"),impactOnPrice=Tools.parseFloatFromString(priceImpactInput.val()),previousImpactOnPrice=Tools.parseFloatFromString(priceImpactInput.attr("value")),finalPrice=Tools.parseFloatFromString(finalPriceLabel.data("price"),!0)-previousImpactOnPrice+impactOnPrice;finalPriceLabel.html(Number(ps_round(finalPrice,6)).toFixed(6))}/**
   * Returns a reference to the form for a specific combination
   * @param {String} attributeId
   * @return {jQuery}
   */
function getCombinationForm(attributeId){return $("#combination_form_"+attributeId)}/**
   * Returns a reference to the row of a specific combination
   * @param {String} attributeId
   * @return {jQuery}
   */
function getCombinationRow(attributeId){return $("#accordion_combinations #attribute_"+attributeId)}$("#form_id_product").val();return{init:function(){var productTypeSelector=$("#form_step1_type_product"),combinationsList=$("#accordion_combinations .combination");combinationsList.length>0&&productTypeSelector.prop("disabled",!0),$(document).on("click","#accordion_combinations .delete",function(e){e.preventDefault(),/**
   * Remove a combination
   * @param {object} elem - The clicked link
   */
function(elem){var combinationElem=$("#attribute_"+elem.attr("data"));modalConfirmation.create(translate_javascripts["Are you sure to delete this?"],null,{onContinue:function(){var attributeId=elem.attr("data");$.ajax({type:"DELETE",data:{"attribute-ids":[attributeId]},url:elem.attr("href"),beforeSend:function(){elem.attr("disabled","disabled"),$("#create-combinations, #apply-on-combinations, #submit, .btn-submit").attr("disabled","disabled")},success:function(response){refreshTotalCombinations(-1,1),combinationElem.remove(),showSuccessMessage(response.message),displayFieldsManager.refresh()},error:function(response){showErrorMessage(jQuery.parseJSON(response.responseText).message)},complete:function(){elem.removeAttr("disabled"),$("#create-combinations, #apply-on-combinations, #submit, .btn-submit").removeAttr("disabled"),supplierCombinations.refresh(),warehouseCombinations.refresh(),$(".js-combinations-list .combination").length<=0&&$("#combinations_thead").fadeOut()}})}}).show()}($(this))}).on("keyup",'input[id^="combination"][id$="_attribute_quantity"]',function(){getCombinationRow($(this).closest(".combination-form").attr("data")).find(".attribute-quantity input").val($(this).val())}).on("keyup",".attribute-quantity input",function(){getCombinationForm($(this).closest(".combination").attr("data")).find('input[id^="combination"][id$="_attribute_quantity"]').val($(this).val())}).on({
// when typing a new impact on price on the form, update it on the row
keyup:function(){getCombinationRow($(this).closest(".combination-form").attr("data")).find(".attribute-price input").val($(this).val())},
// when impact on price on the form is changed, update final price
change:function(){var input=getCombinationRow($(this).closest(".combination-form").attr("data")).find(".attribute-price input");input.val($(this).val()),updateFinalPrice($(input.parents("tr")[0]))}},'input[id^="combination"][id$="_attribute_price"]').on("change",".attribute-price input",function(){getCombinationForm($(this).closest(".combination").attr("data")).find('input[id^="combination"][id$="_attribute_price"]').val($(this).val()),updateFinalPrice($(this).parent().parent().parent())}).on("click","input.attribute-default",function(){var selectedCombination=$(this),combinationRadioButtons=$("input.attribute-default"),attributeId=$(this).closest(".combination").attr("data");combinationRadioButtons.each(function(index){var combination=$(this);combination.data("id")!==selectedCombination.data("id")&&combination.prop("checked",!1)}),$(".attribute_default_checkbox").removeAttr("checked"),getCombinationForm(attributeId).find('input[id^="combination"][id$="_attribute_default"]').prop("checked",!0)}).on("change","#show_variations_selector input",function(){displayFieldsManager.refresh(),combinationsList=$("#accordion_combinations .combination"),"0"===$(this).val()?
//if combination(s) exists, alert user for deleting it
combinationsList.length>0?modalConfirmation.create(translate_javascripts["Are you sure to disable variations ? they will all be deleted"],null,{onCancel:function(){$('#show_variations_selector input[value="1"]').prop("checked",!0),displayFieldsManager.refresh()},onContinue:function(){$.ajax({type:"GET",url:$("#accordion_combinations").attr("data-action-delete-all").replace(/\/\d+(?=\?.*)/,"/"+$("#form_id_product").val()),success:function(response){combinationsList.remove(),displayFieldsManager.refresh()},error:function(response){showErrorMessage(jQuery.parseJSON(response.responseText).message)}}),
// enable the top header selector
// we want to use a "Simple product" without any combinations
productTypeSelector.prop("disabled",!1)}}).show():
// enable the top header selector if no combination(s) exists
productTypeSelector.prop("disabled",!1):
// this means we have or we want to have combinations
// disable the product type selector
productTypeSelector.prop("disabled",!0)}).on("click","#accordion_combinations .btn-open",function(e){function countSelectedProducts(){return $("#combination_form_"+contentElem.attr("data")+" .img-highlight").length}e.preventDefault();var contentElem=$($(this).attr("href")),navElem=contentElem.find(".nav"),id_attribute=contentElem.attr("data"),prevCombinationId=$('#accordion_combinations tr[data="'+id_attribute+'"]').prev().attr("data"),nextCombinationId=$('#accordion_combinations tr[data="'+id_attribute+'"]').next().attr("data");navElem.find(".prev, .next").hide(),prevCombinationId&&navElem.find(".prev").attr("data",prevCombinationId).show(),nextCombinationId&&navElem.find(".next").attr("data",nextCombinationId).show(),/** init combination tax include price */
priceCalculation.impactTaxInclude(contentElem.find(".attribute_priceTE")),contentElem.insertBefore("#form-nav").removeClass("hide").show(),contentElem.find(".datepicker").datetimepicker({locale:iso_user,format:"YYYY-MM-DD"});var number=$("#combination_form_"+contentElem.attr("data")+" .number-of-images"),allProductCombination=$("#combination_form_"+contentElem.attr("data")+" .product-combination-image").length;number.text(countSelectedProducts()+"/"+allProductCombination),$(document).on("click",".tabs .product-combination-image",function(){number.text(countSelectedProducts()+"/"+allProductCombination)}),/** Add title on product's combination image */
$(function(){$("#combination_form_"+contentElem.attr("data")).find("img").each(function(){title=$(this).attr("src").split("/").pop(),$(this).attr("title",title)})}),$("#form-nav, #form_content").hide()}).on("click","#form .combination-form .btn-back",function(e){e.preventDefault(),$(this).closest(".combination-form").hide(),$("#form-nav, #form_content").show()}).on("click","#form .combination-form .nav a",function(e){e.preventDefault(),$(".combination-form").hide(),$('#accordion_combinations .combination[data="'+$(this).attr("data")+'"] .btn-open').click()})}}}(),refreshTotalCombinations=function(sign,number){var $bulkCombinationsTotal=$("#js-bulk-combinations-total"),currentnumber=parseInt($bulkCombinationsTotal.text())+sign*number;$bulkCombinationsTotal.text(currentnumber)};combinations.init()},/* 10 */
/***/
function(module,exports){/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
!function($){$.fn.categorytree=function(settings){
// if a method call execute the method on all selected instances
if("string"==typeof settings)switch(settings){case"unselect":$("div.radio > label > input:radio",this).prop("checked",!1);
// TODO: add a callback method feature?
break;case"unfold":$("ul",this).show(),$("li",this).has("ul").addClass("less");break;case"fold":$("ul ul",this).hide(),$("li",this).has("ul").addClass("more");break;default:throw"Unknown method"}else $("li > ul",this).each(function(i,item){var clickHandler=function(event){var $ui=$(event.target);if("radio"!==$ui.attr("type")&&"checkbox"!==$ui.attr("type"))return event.stopPropagation(),0===$ui.next("ul").length&&($ui=$ui.parent()),$ui.next("ul").toggle(),$ui.next("ul").is(":visible")?$ui.parent("li").removeClass().addClass("less"):$ui.parent("li").removeClass().addClass("more"),!1},$inputWrapper=$(item).prev("div");$inputWrapper.on("click",clickHandler),$inputWrapper.find("label").on("click",clickHandler),$(item).is(":visible")?$(item).parent("li").removeClass().addClass("less"):$(item).parent("li").removeClass().addClass("more")});
// return the jquery selection (or if it was a method call that returned a value - the returned value)
return this}}(jQuery)},/* 11 */
/***/
function(module,exports){var module_card_controller={};$(document).ready(function(){(module_card_controller=new AdminModuleCard).init()});/**
 * AdminModule card Controller.
 * @constructor
 */
var AdminModuleCard=function(){/* Selectors for module action links (uninstall, reset, etc...) to add a confirm popin */
this.moduleActionMenuLinkSelector="button.module_action_menu_",this.moduleActionMenuInstallLinkSelector="button.module_action_menu_install",this.moduleActionMenuEnableLinkSelector="button.module_action_menu_enable",this.moduleActionMenuUninstallLinkSelector="button.module_action_menu_uninstall",this.moduleActionMenuDisableLinkSelector="button.module_action_menu_disable",this.moduleActionMenuEnableMobileLinkSelector="button.module_action_menu_enable_mobile",this.moduleActionMenuDisableMobileLinkSelector="button.module_action_menu_disable_mobile",this.moduleActionMenuResetLinkSelector="button.module_action_menu_reset",this.moduleActionMenuUpdateLinkSelector="button.module_action_menu_upgrade",this.moduleItemListSelector=".module-item-list",this.moduleItemGridSelector=".module-item-grid",/* Selectors only for modal buttons */
this.moduleActionModalDisableLinkSelector="a.module_action_modal_disable",this.moduleActionModalResetLinkSelector="a.module_action_modal_reset",this.moduleActionModalUninstallLinkSelector="a.module_action_modal_uninstall",this.forceDeletionOption="#force_deletion",/**
     * Initialize all listeners and bind everything
     * @method init
     * @memberof AdminModuleCard
     */
this.init=function(){this.initActionButtons()},this.getModuleItemSelector=function(){return $(this.moduleItemListSelector).length?this.moduleItemListSelector:this.moduleItemGridSelector},this.confirmAction=function(action,element){var modal=$("#"+$(element).data("confirm_modal"));return 1!=modal.length||(modal.first().modal("show"),!1)},/**
     * Update the content of a modal asking a confirmation for PrestaTrust and open it
     * 
     * @param {array} result containing module data
     * @return {void}
     */
this.confirmPrestaTrust=function(result){var that=this,modal=this.replacePrestaTrustPlaceholders(result);modal.find(".pstrust-install").off("click").on("click",function(){
// Find related form, update it and submit it
var install_button=$(that.moduleActionMenuInstallLinkSelector,'.module-item[data-tech-name="'+result.module.attributes.name+'"]'),form=install_button.parent("form");$("<input>").attr({type:"hidden",value:"1",name:"actionParams[confirmPrestaTrust]"}).appendTo(form),install_button.click(),modal.modal("hide")}),modal.modal()},this.replacePrestaTrustPlaceholders=function(result){var modal=$("#modal-prestatrust"),module=result.module.attributes;if("PrestaTrust"===result.confirmation_subject&&modal.length){var alertClass=module.prestatrust.status?"success":"warning";return module.prestatrust.check_list.property?(modal.find("#pstrust-btn-property-ok").show(),modal.find("#pstrust-btn-property-nok").hide()):(modal.find("#pstrust-btn-property-ok").hide(),modal.find("#pstrust-btn-property-nok").show(),modal.find("#pstrust-buy").attr("href",module.url).toggle(null!==module.url)),modal.find("#pstrust-img").attr({src:module.img,alt:module.name}),modal.find("#pstrust-name").text(module.displayName),modal.find("#pstrust-author").text(module.author),modal.find("#pstrust-label").attr("class","text-"+alertClass).text(module.prestatrust.status?"OK":"KO"),modal.find("#pstrust-message").attr("class","alert alert-"+alertClass),modal.find("#pstrust-message > p").text(module.prestatrust.message),modal}},this.dispatchPreEvent=function(action,element){var event=jQuery.Event("module_card_action_event");return $(element).trigger(event,[action]),!1===event.isPropagationStopped()&&!1===event.isImmediatePropagationStopped()&&!1!==event.result},this.initActionButtons=function(){var _this=this;$(document).on("click",this.forceDeletionOption,function(){var btn=$(_this.moduleActionModalUninstallLinkSelector,$("div.module-item-list[data-tech-name='"+$(this).attr("data-tech-name")+"']"));!0===$(this).prop("checked")?btn.attr("data-deletion","true"):btn.removeAttr("data-deletion")}),$(document).on("click",this.moduleActionMenuInstallLinkSelector,function(){return $("#modal-prestatrust").length&&$("#modal-prestatrust").modal("hide"),_this.dispatchPreEvent("install",this)&&_this.confirmAction("install",this)&&_this.requestToController("install",$(this))}),$(document).on("click",this.moduleActionMenuEnableLinkSelector,function(){return _this.dispatchPreEvent("enable",this)&&_this.confirmAction("enable",this)&&_this.requestToController("enable",$(this))}),$(document).on("click",this.moduleActionMenuUninstallLinkSelector,function(){return _this.dispatchPreEvent("uninstall",this)&&_this.confirmAction("uninstall",this)&&_this.requestToController("uninstall",$(this))}),$(document).on("click",this.moduleActionMenuDisableLinkSelector,function(){return _this.dispatchPreEvent("disable",this)&&_this.confirmAction("disable",this)&&_this.requestToController("disable",$(this))}),$(document).on("click",this.moduleActionMenuEnableMobileLinkSelector,function(){return _this.dispatchPreEvent("enable_mobile",this)&&_this.confirmAction("enable_mobile",this)&&_this.requestToController("enable_mobile",$(this))}),$(document).on("click",this.moduleActionMenuDisableMobileLinkSelector,function(){return _this.dispatchPreEvent("disable_mobile",this)&&_this.confirmAction("disable_mobile",this)&&_this.requestToController("disable_mobile",$(this))}),$(document).on("click",this.moduleActionMenuResetLinkSelector,function(){return _this.dispatchPreEvent("reset",this)&&_this.confirmAction("reset",this)&&_this.requestToController("reset",$(this))}),$(document).on("click",this.moduleActionMenuUpdateLinkSelector,function(){return _this.dispatchPreEvent("update",this)&&_this.confirmAction("update",this)&&_this.requestToController("update",$(this))}),$(document).on("click",this.moduleActionModalDisableLinkSelector,function(){return _this.requestToController("disable",$(_this.moduleActionMenuDisableLinkSelector,$("div.module-item-list[data-tech-name='"+$(this).attr("data-tech-name")+"']")))}),$(document).on("click",this.moduleActionModalResetLinkSelector,function(){return _this.requestToController("reset",$(_this.moduleActionMenuResetLinkSelector,$("div.module-item-list[data-tech-name='"+$(this).attr("data-tech-name")+"']")))}),$(document).on("click",this.moduleActionModalUninstallLinkSelector,function(){return _this.requestToController("uninstall",$(_this.moduleActionMenuUninstallLinkSelector,$("div.module-item-list[data-tech-name='"+$(this).attr("data-tech-name")+"']")),$(this).attr("data-deletion"))})},this.requestToController=function(action,element,forceDeletion){var _this=this,jqElementObj=element.closest("div.btn-group"),form=element.closest("form"),spinnerObj=$('<button class="btn-primary-reverse onclick unbind spinner "></button>'),url="//"+window.location.host+form.attr("action"),actionParams=form.serializeArray();return"true"!==forceDeletion&&!0!==forceDeletion||actionParams.push({name:"actionParams[deletion]",value:!0}),$.ajax({url:url,dataType:"json",method:"POST",data:actionParams,beforeSend:function(){jqElementObj.hide(),jqElementObj.after(spinnerObj)}}).done(function(result){if(void 0===typeof result)$.growl.error({message:"No answer received from server"});else{var moduleTechName=Object.keys(result)[0];if(!1===result[moduleTechName].status)void 0!==result[moduleTechName].confirmation_subject&&_this.confirmPrestaTrust(result[moduleTechName]),$.growl.error({message:result[moduleTechName].msg});else{$.growl.notice({message:result[moduleTechName].msg});var alteredSelector=null,mainElement=null;"uninstall"==action?(jqElementObj.fadeOut(function(){alteredSelector=_this.getModuleItemSelector().replace(".",""),(mainElement=jqElementObj.parents("."+alteredSelector).first()).remove()}),BOEvent.emitEvent("Module Uninstalled","CustomEvent")):"disable"==action?(alteredSelector=_this.getModuleItemSelector().replace(".",""),(mainElement=jqElementObj.parents("."+alteredSelector).first()).addClass(alteredSelector+"-isNotActive"),mainElement.attr("data-active","0"),BOEvent.emitEvent("Module Disabled","CustomEvent")):"enable"==action&&(alteredSelector=_this.getModuleItemSelector().replace(".",""),(mainElement=jqElementObj.parents("."+alteredSelector).first()).removeClass(alteredSelector+"-isNotActive"),mainElement.attr("data-active","1"),BOEvent.emitEvent("Module Enabled","CustomEvent")),jqElementObj.replaceWith(result[moduleTechName].action_menu_html)}}}).always(function(){jqElementObj.fadeIn(),spinnerObj.remove()}),!1}}},/* 12 */
/***/
function(module,exports){/**
 * modal confirmation management
 */
var modalConfirmation=function(){var modal=$("#confirmation_modal");if(!modal)throw new Error("Modal confirmation is not available");var actionsCallbacks={onCancel:function(){console.log("modal canceled")},onContinue:function(){console.log("modal continued")}};return modal.find("button.cancel").click(function(){"function"==typeof actionsCallbacks.onCancel&&actionsCallbacks.onCancel(),modalConfirmation.hide()}),modal.find("button.continue").click(function(){"function"==typeof actionsCallbacks.onContinue&&actionsCallbacks.onContinue(),modalConfirmation.hide()}),{init:function(){},create:function(content,title,callbacks){return null!=title&&modal.find(".modal-title").html(title),null!=content&&modal.find(".modal-body").html(content),actionsCallbacks=callbacks,this},show:function(){modal.modal("show")},hide:function(){modal.modal("hide")}}}();modalConfirmation.init()}]);