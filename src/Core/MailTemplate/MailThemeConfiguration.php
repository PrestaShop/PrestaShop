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

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Class MailThemeConfiguration is used to save Mail Theme settings
 * in "Design > Mail Theme" page.
 */
final class MailThemeConfiguration implements DataConfigurationInterface
{
    /** @var ConfigurationInterface */
    private $configuration;

    /** @var ThemeCatalogInterface */
    private $themeCatalog;

    /**
     * @param ConfigurationInterface $configuration
     * @param ThemeCatalogInterface $themeCatalog
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ThemeCatalogInterface $themeCatalog
    ) {
        $this->configuration = $configuration;
        $this->themeCatalog = $themeCatalog;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'defaultTheme' => $this->configuration->get('PS_MAIL_THEME'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        try {
            $this->validateConfiguration($configuration);
            $this->configuration->set('PS_MAIL_THEME', $configuration['defaultTheme']);
        } catch (CoreException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        if (empty($configuration['defaultTheme'])) {
            throw new InvalidArgumentException('Default theme can not be empty');
        }

        return null !== $this->themeCatalog->getByName($configuration['defaultTheme']);
    }
}
