<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\GeneralConfiguration;
use PrestaShop\PrestaShop\Adapter\Product\PageConfiguration;
use PrestaShop\PrestaShop\Adapter\Product\PaginationConfiguration;
use PrestaShop\PrestaShop\Adapter\Product\StockConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 *
 * @deprecated since 1.7.8, will be removed in the next major version
 */
class ProductPreferencesFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var GeneralConfiguration
     */
    private $generalConfiguration;

    /**
     * @var PaginationConfiguration
     */
    private $paginationConfiguration;

    /**
     * @var PageConfiguration
     */
    private $pageConfiguration;

    /**
     * @var StockConfiguration
     */
    private $stockConfiguration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        GeneralConfiguration $generalConfiguration,
        PaginationConfiguration $paginationConfiguration,
        PageConfiguration $pageConfiguration,
        StockConfiguration $stockConfiguration,
        TranslatorInterface $translator
    ) {
        $this->generalConfiguration = $generalConfiguration;
        $this->paginationConfiguration = $paginationConfiguration;
        $this->pageConfiguration = $pageConfiguration;
        $this->stockConfiguration = $stockConfiguration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'general' => $this->generalConfiguration->getConfiguration(),
            'pagination' => $this->paginationConfiguration->getConfiguration(),
            'page' => $this->pageConfiguration->getConfiguration(),
            'stock' => $this->stockConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->generalConfiguration->updateConfiguration($data['general']) +
            $this->paginationConfiguration->updateConfiguration($data['pagination']) +
            $this->pageConfiguration->updateConfiguration($data['page']) +
            $this->stockConfiguration->updateConfiguration($data['stock']);
    }

    /**
     * Perform validation on form data before saving it.
     *
     * @param array $data
     *
     * @return array Returns array of errors
     */
    protected function validate(array $data)
    {
        $invalidFields = [];

        $newDaysNumber = $data['general']['new_days_number'];
        if (!is_numeric($newDaysNumber) || 0 > $newDaysNumber) {
            $invalidFields[] = $this->translator->trans(
                'Number of days for which the product is considered \'new\'',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $shortDescriptionLimit = $data['general']['short_description_limit'];
        if (!is_numeric($shortDescriptionLimit) || 0 >= $shortDescriptionLimit) {
            $invalidFields[] = $this->translator->trans(
                'Max size of product summary',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $displayLastQuantities = $data['page']['display_last_quantities'];
        if (!is_numeric($displayLastQuantities) || 0 > $displayLastQuantities) {
            $invalidFields[] = $this->translator->trans(
                'Display remaining quantities when the quantity is lower than',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $productsPerPage = $data['pagination']['products_per_page'];
        if (!is_numeric($productsPerPage) || 0 > $productsPerPage) {
            $invalidFields[] = $this->translator->trans('Products per page', [], 'Admin.Shopparameters.Feature');
        }

        $errors = [];
        foreach ($invalidFields as $field) {
            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$field],
            ];
        }

        return $errors;
    }
}
