<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;

/**
 * Class MailThemeConfiguration is used to save Mail Theme settings
 * in "Design > Mail Theme" page.
 */
final class MailThemeConfiguration implements DataConfigurationInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var ThemeCatalogInterface */
    private $themeCatalog;

    /**
     * @param Configuration $configuration
     * @param ThemeCatalogInterface $themeCatalog
     */
    public function __construct(
        Configuration $configuration,
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
        return array(
            'defaultTheme' => $this->configuration->get('PS_MAIL_THEME'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

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

        /** @var ThemeInterface $theme */
        $theme = $this->themeCatalog->getByName($configuration['defaultTheme']);

        return null !== $theme;
    }
}
