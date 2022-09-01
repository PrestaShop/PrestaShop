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

use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorCollection;
use PrestaShopBundle\Controller\Exception\FieldLabelNotFoundException;
use PrestaShopBundle\Form\ErrorMessage\LabelProvider;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Base configuration error factory which cycles trough all existing configuration error factories
 * to create error messages based on error class and code.
 */
class ConfigurationErrorFactory
{
    /**
     * @var iterable
     */
    private $configurationErrorFactories;

    /**
     * @var LabelProvider
     */
    private $labelProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        iterable $configurationErrorFactories,
        LabelProvider $labelProvider,
        TranslatorInterface $translator
    ) {
        $this->configurationErrorFactories = $configurationErrorFactories;
        $this->labelProvider = $labelProvider;
        $this->translator = $translator;
    }

    /**
     * @param ConfigurationErrorCollection $errors
     * @param FormInterface $form
     *
     * @return array
     *
     * @throws FieldLabelNotFoundException
     */
    public function getErrorMessages(ConfigurationErrorCollection $errors, FormInterface $form): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $label = $this->labelProvider->getLabel($form, $error->getFieldName());
            $errorMessage = null;
            foreach ($this->configurationErrorFactories as $factory) {
                $errorMessage = $factory->getErrorMessageForConfigurationError(
                    $error,
                    $label
                );
                if ($errorMessage) {
                    break;
                }
            }
            $messages[] = $errorMessage ?: $this->getDefaultErrorMessage($label);
        }

        return $messages;
    }

    /**
     * @param string $label
     *
     * @return string
     */
    private function getDefaultErrorMessage(string $label): string
    {
        return $this->translator->trans(
            '%s is invalid.',
            [
                $label,
            ],
            'Admin.Notifications.Error'
        );
    }
}
