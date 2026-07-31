<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use AddressFormat;
use Configuration;
use Context;
use Shop;
use Tools;

/**
 * Builds the header/footer variables shared by every PDF document type
 * (invoice, credit slip, order return, delivery slip...), equivalent to the
 * legacy HTMLTemplate::assignCommonHeaderData()/getFooter() base methods.
 */
final class PdfDocumentCommonDataBuilder
{
    private const MAX_LOGO_HEIGHT = 100;

    /**
     * @return array<string, mixed>
     */
    public function buildHeaderData(Shop $shop, string $title, string $date, string $header): array
    {
        $shopId = (int) $shop->id;
        $logo = $this->getLogo($shopId);

        $width = 0;
        $height = 0;
        if (!empty($logo)) {
            [$width, $height] = getimagesize(_PS_IMG_DIR_ . $logo);
            if ($height > self::MAX_LOGO_HEIGHT) {
                $ratio = self::MAX_LOGO_HEIGHT / $height;
                $height *= $ratio;
                $width *= $ratio;
            }
        }

        return [
            'logo_path' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_ . $logo,
            'img_ps_dir' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'date' => $date,
            'title' => $title,
            'shop_name' => Configuration::get('PS_SHOP_NAME', null, null, $shopId),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, $shopId),
            'width_logo' => $width,
            'height_logo' => $height,
            'header' => $header,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildFooterData(Shop $shop, bool $availableInYourAccount): array
    {
        $shopId = (int) $shop->id;

        return [
            'available_in_your_account' => $availableInYourAccount,
            'shop_address' => AddressFormat::generateAddress($shop->getAddress(), [], ' - ', ' '),
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $shopId),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $shopId),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $shopId),
            'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int) Context::getContext()->language->id, null, $shopId),
        ];
    }

    private function getLogo(int $shopId): ?string
    {
        $invoiceLogo = Configuration::get('PS_LOGO_INVOICE', null, null, $shopId);
        if ($invoiceLogo && file_exists(_PS_IMG_DIR_ . $invoiceLogo)) {
            return $invoiceLogo;
        }

        $logo = Configuration::get('PS_LOGO', null, null, $shopId);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            return $logo;
        }

        return null;
    }
}
