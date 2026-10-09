/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import initShopTree from '@components/shop-tree';
import ShopUrlMap from '@pages/shop-url/shop-url-map';

const fillFinalUrl = (): void => {
  const domain = $(ShopUrlMap.domainInput).val() || '???';
  const url = `${domain}/${$(ShopUrlMap.physicalUriInput).val()}/${$(ShopUrlMap.virtualUriInput).val()}/`;

  $(ShopUrlMap.finalUrlInput).val(`http://${url.replace(/\/+/g, '/')}`);
};

const checkMainUrl = (): void => {
  const shopSelect = $(ShopUrlMap.shopSelect);
  const shopId = Number(shopSelect.val());
  const shopHasUrl = shopSelect.data('shop-ids-with-url').includes(shopId);
  const isAlreadyMainUrl = shopId === Number(shopSelect.data('main-url-shop-id'));
  const mainInputs = $(ShopUrlMap.mainInput);
  const mainUrlAlert = mainInputs.closest(ShopUrlMap.formGroup).find(ShopUrlMap.mainUrlAlert);
  const [mainUrlRequiredNotice, mainUrlReplacedNotice] = mainUrlAlert.find(ShopUrlMap.alertText).toArray();

  if (!shopHasUrl) {
    mainInputs.filter('[value="1"]').prop('checked', true);
  }
  mainInputs.filter('[value="0"]').prop('disabled', !shopHasUrl);
  $(mainUrlRequiredNotice).toggleClass('d-none', shopHasUrl);
  $(mainUrlReplacedNotice).toggleClass('d-none', !shopHasUrl || isAlreadyMainUrl);
  mainUrlAlert.toggleClass('d-none', shopHasUrl && isAlreadyMainUrl);
};

$(() => {
  initShopTree();
  fillFinalUrl();
  checkMainUrl();

  $(ShopUrlMap.shopSelect).on('change', checkMainUrl);
  $(`${ShopUrlMap.physicalUriInput}, ${ShopUrlMap.virtualUriInput}`).on('input', fillFinalUrl);

  const domainInput = $(ShopUrlMap.domainInput);
  const domainSslInput = $(ShopUrlMap.domainSslInput);
  let previousDomain = domainInput.val();

  domainInput.on('input', () => {
    if (!domainSslInput.val() || domainSslInput.val() === previousDomain) {
      domainSslInput.val(<string> domainInput.val());
    }
    previousDomain = domainInput.val();
    fillFinalUrl();
  });

  $([
    ShopUrlMap.domainInput,
    ShopUrlMap.domainSslInput,
    ShopUrlMap.physicalUriInput,
    ShopUrlMap.virtualUriInput,
  ].join(', ')).on('blur', (event) => {
    const input = $(event.currentTarget);
    input.val((<string> input.val()).trim().replace(/ /g, '-'));
    fillFinalUrl();
  });
});
