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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Advanced Parameters > Administration" page.
 */
final class FormDataProvider implements FormDataProviderInterface
{
    public const ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO = 1;
    public const ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED = 2;
    public const ERROR_COOKIE_SAMESITE_NONE = 3;

    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    public function __construct(
        DataConfigurationInterface $dataConfiguration
    ) {
        $this->dataConfiguration = $dataConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
