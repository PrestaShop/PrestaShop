<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Adapter\Localization\AdvancedConfiguration;
use PrestaShop\PrestaShop\Adapter\Localization\LocalizationConfiguration;
use PrestaShop\PrestaShop\Adapter\Localization\LocalUnitsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class LocalizationFormDataProvider is responsible for handling
 * 'Improve > International > Localization' page form data.
 */
class LocalizationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var LocalizationConfiguration
     */
    private $localizationConfiguration;

    /**
     * @var LocalUnitsConfiguration
     */
    private $localUnitsConfiguration;

    /**
     * @var AdvancedConfiguration
     */
    private $advancedConfiguration;

    /**
     * @param LocalizationConfiguration $localizationConfiguration
     * @param LocalUnitsConfiguration $localUnitsConfiguration
     * @param AdvancedConfiguration $advancedConfiguration
     */
    public function __construct(
        LocalizationConfiguration $localizationConfiguration,
        LocalUnitsConfiguration $localUnitsConfiguration,
        AdvancedConfiguration $advancedConfiguration
    ) {
        $this->localizationConfiguration = $localizationConfiguration;
        $this->localUnitsConfiguration = $localUnitsConfiguration;
        $this->advancedConfiguration = $advancedConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'configuration' => $this->localizationConfiguration->getConfiguration(),
            'local_units' => $this->localUnitsConfiguration->getConfiguration(),
            'advanced' => $this->advancedConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->localizationConfiguration->updateConfiguration($data['configuration']) +
            $this->localUnitsConfiguration->updateConfiguration($data['local_units']) +
            $this->advancedConfiguration->updateConfiguration($data['advanced']);
    }
}
