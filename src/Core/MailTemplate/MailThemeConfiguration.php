<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
