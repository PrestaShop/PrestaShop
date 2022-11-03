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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Extension;

use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;
use TypeError;

/**
 * This trait is used by the DisablingSwitchExtension and the AddDisablingSwitchListener because they both need
 * to detect if a form should be disabled and the need to detect it the same way.
 */
trait DisablingSwitchTrait
{
    protected function shouldFormBeDisabled(FormInterface $form, $data): bool
    {
        $disabledValue = $form->getConfig()->getOption(DisablingSwitchExtension::DISABLED_VALUE_OPTION);
        if (is_callable($disabledValue)) {
            try {
                $shouldBeDisabled = $disabledValue($data, $form);
            } catch (TypeError $typeError) {
                throw new InvalidConfigurationException(
                    'The callable provided for disabled_value option seems invalid, its prototype should be compatible with function($data, FormInterface $form): bool And $data is usually nullable',
                    0,
                    $typeError
                );
            }
        } else {
            if (null === $disabledValue) {
                $disabledValue = $form->getConfig()->getOption('default_empty_data');
            }
            if (null === $disabledValue) {
                $emptyData = $form->getConfig()->getOption('empty_data');
                $disabledValue = $emptyData instanceof \Closure ? $emptyData($form) : $emptyData;
            }

            $shouldBeDisabled = $disabledValue === $data;
        }

        return $shouldBeDisabled;
    }
}
