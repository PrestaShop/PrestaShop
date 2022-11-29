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

namespace PrestaShopBundle\Form\ErrorMessage\Factory;

use PrestaShop\PrestaShop\Core\Form\ErrorMessage\AdministrationConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\Factory\ConfigurationErrorMessageProviderInterface;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralDataProvider;
use Symfony\Component\Translation\TranslatorInterface;

/** Provider to get messages for errors specific to administration page */
class AdministrationConfigurationErrorMessageProvider implements ConfigurationErrorMessageProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param ConfigurationErrorInterface $error
     * @param string $label
     *
     * @return string|null
     */
    public function getErrorMessageForConfigurationError(ConfigurationErrorInterface $error, string $label): ?string
    {
        if (!$error instanceof AdministrationConfigurationError) {
            return null;
        }

        switch ($error->getErrorCode()) {
            case AdministrationConfigurationError::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED:
                return $this->translator->trans(
                    '%s is invalid. Please enter an integer lower than %s.',
                    [
                        $label,
                        GeneralDataProvider::MAX_COOKIE_VALUE,
                    ],
                    'Admin.Notifications.Error'
                );
            case AdministrationConfigurationError::ERROR_COOKIE_SAMESITE_NONE:
                return $this->translator->trans(
                    'The SameSite=None is only available in secure mode.',
                    [],
                    'Admin.Advparameters.Notification'
                );
        }

        return null;
    }
}
