function spinFinish() {
  return console.log($(this).attr('productid') + ": " + $(this).val());
}

$( document ).ready(function() {
  $("input[name='product-quantity-spin']").TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin',
    buttonup_class: 'btn btn-touchspin'
  }).change(spinFinish);
});
