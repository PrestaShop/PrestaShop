/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import ProductSelect from './components/product-select';

$(document).ready(function () {
  createProductSpin();
  createInputFile();
  coverImage();
  imageScrollBox();

  prestashop.on('updatedProduct', function (event) {
    createInputFile();
    coverImage();
    if (event && event.product_minimal_quantity) {
      const minimalProductQuantity = parseInt(event.product_minimal_quantity, 10);
      const quantityInputSelector = prestashop.selectors.quantityWanted;
      let quantityInput = $(quantityInputSelector);

      // @see http://www.virtuosoft.eu/code/bootstrap-touchspin/ about Bootstrap TouchSpin
      quantityInput.trigger('touchspin.updatesettings', {
        min: minimalProductQuantity,
      });
    }
    imageScrollBox();
    $($(prestashop.themeSelectors.product.activeTabs).attr('href')).addClass('active').removeClass('fade');
    $(prestashop.themeSelectors.product.imagesModal).replaceWith(event.product_images_modal);

    let productSelect = new ProductSelect();
    productSelect.init();
  });

  function coverImage() {
    $(prestashop.themeSelectors.product.thumb).on('click', (event) => {
      $(prestashop.themeSelectors.product.modalProductCover).attr('src', $(event.target).data('image-large-src'));
      $(prestashop.themeSelectors.product.selected).removeClass('selected');
      $(event.target).addClass('selected');
      $(prestashop.themeSelectors.product.cover).prop('src', $(event.currentTarget).data('image-large-src'));
    });
  }

  function imageScrollBox() {
    if ($('#main .js-qv-product-images li').length > 2) {
      $('#main .js-qv-mask').addClass('scroll');
      $('.scroll-box-arrows').addClass('scroll');
      $('#main .js-qv-mask').scrollbox({
        direction: 'h',
        distance: 113,
        autoPlay: false,
      });
      $('.scroll-box-arrows .left').click(function () {
        $('#main .js-qv-mask').trigger('backward');
      });
      $('.scroll-box-arrows .right').click(function () {
        $('#main .js-qv-mask').trigger('forward');
      });
    } else {
      $('#main .js-qv-mask').removeClass('scroll');
      $('.scroll-box-arrows').removeClass('scroll');
    }
  }

  function createInputFile() {
    $(prestashop.themeSelectors.fileInput).on('change', (event) => {
      let target, file;

      if ((target = $(event.currentTarget)[0]) && (file = target.files[0])) {
        $(target).prev().text(file.name);
      }
    });
  }

  function createProductSpin() {
    const $quantityInput = $(prestashop.selectors.quantityWanted);

    $quantityInput.TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'material-icons touchspin-up',
      verticaldownclass: 'material-icons touchspin-down',
      buttondown_class: 'btn btn-touchspin js-touchspin',
      buttonup_class: 'btn btn-touchspin js-touchspin',
      min: parseInt($quantityInput.attr('min'), 10),
      max: 1000000,
    });

    $quantityInput.focusout(() => {
      if ($quantityInput.val() === '' || $quantityInput.val() < $quantityInput.attr('min')) {
        $quantityInput.val($quantityInput.attr('min'));
        $quantityInput.trigger('change');
      }
    });

    $('body').on('change keyup', prestashop.selectors.quantityWanted, (e) => {
      if ($quantityInput.val() !== '') {
        $(e.currentTarget).trigger('touchspin.stopspin');
        prestashop.emit('updateProduct', {
          eventType: 'updatedProductQuantity',
          event: e,
        });
      }
    });
  }
});
