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

use PrestaShop\PrestaShop\Core\Form\ErrorMessage\CommonConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\Factory\ConfigurationErrorMessageProviderInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Translation\TranslatorInterface;

/** Provider to get messages for common configuration errors */
class CommonConfigurationErrorMessageProvider implements ConfigurationErrorMessageProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LangRepository
     */
    protected $langRepository;

    public function __construct(
        TranslatorInterface $translator,
        LangRepository $langRepository
    ) {
        $this->translator = $translator;
        $this->langRepository = $langRepository;
    }

    /**
     * @param ConfigurationErrorInterface $error
     * @param string $label
     *
     * @return string|null
     */
    public function getErrorMessageForConfigurationError(ConfigurationErrorInterface $error, string $label): ?string
    {
        if (!$error instanceof CommonConfigurationError) {
            return null;
        }

        switch ($error->getErrorCode()) {
            case CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO:
                return $this->translator->trans(
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    [
                        $label,
                    ],
                    'Admin.Notifications.Error'
                );
        }

        return null;
    }
}
