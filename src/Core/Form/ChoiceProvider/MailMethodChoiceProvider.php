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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MailMethodChoiceProvider provides choices for mail methods.
 */
final class MailMethodChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        if (null === $this->configuration->get('_PS_HOST_MODE_')) {
            $choices[
                $this->trans('Use /usr/sbin/sendmail (recommended; works in most cases)', [], 'Admin.Advparameters.Feature')
            ] = MailOption::METHOD_NATIVE;
        }

        $choices[
            $this->trans('Set my own SMTP parameters (for advanced users ONLY)', [], 'Admin.Advparameters.Feature')
        ] = MailOption::METHOD_SMTP;

        $choices[
            $this->trans('Never send emails (may be useful for testing purposes)', [], 'Admin.Advparameters.Feature')
        ] = MailOption::METHOD_NONE;

        return $choices;
    }

    /**
     * @param string $key
     * @param array $params
     * @param string $domain
     *
     * @return string
     */
    private function trans($key, array $params, $domain)
    {
        return $this->translator->trans($key, $params, $domain);
    }
}
