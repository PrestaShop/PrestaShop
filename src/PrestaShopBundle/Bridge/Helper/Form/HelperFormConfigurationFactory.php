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
declare(strict_types=1);

namespace PrestaShopBundle\Bridge\Helper\Form;

use ObjectModel;
use PrestaShopBundle\Bridge\AdminController\Field\FormField;

//@todo: this service doesn't seem to be very useful now, but maybe we can enrich it with data providers and create/edit methods later
class HelperFormConfigurationFactory
{
    /**
     * @param int|null $objectModelId
     * @param string $className
     * @param FormField[] $formFields
     * @param array<string, mixed> $formData
     *
     * @return HelperFormConfiguration
     */
    public function create(
        ?int $objectModelId,
        string $className,
        array $formFields,
        array $formData
    ): HelperFormConfiguration {
        return new HelperFormConfiguration(
            $this->loadObjectModel($objectModelId, $className),
            $formFields,
            $formData
        );
    }

    private function loadObjectModel(?int $objectModelId, string $className): ObjectModel
    {
        if ($objectModelId) {
            return new $className($objectModelId);
        }

        return new $className();
    }
}
