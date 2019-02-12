<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Able to look at translation for a specific domain and locale
 * accross multiple Translation providers
 */
final class MultipleSourcesProvider implements SearchProviderInterface
{
    /**
     * @var AbstractProvider[]
     */
    private $providers;

    /**
     * @var string the locale
     */
    private $locale = 'en-US';

    /**
     * @var string the domain
     */
    private $domain = '';

    /**
     * @var string the module name
     */
    private $moduleName = '';

    public function __construct(array $providers)
    {
        $this->providers = $providers;

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'setModuleName')) {
                $provider->setModuleName('*');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        $directories = [];

        foreach ($this->providers as $provider) {
            foreach ($provider->getDirectories() as $directory) {
                $directories[] = $directory;
            }
        }

        return $directories;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            '#^' . $this->domain . '#',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [
            '^' . $this->domain,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $catalogue = new MessageCatalogue($this->locale);

        foreach ($this->providers as $provider) {
            $catalogue->addCatalogue($provider->getMessageCatalogue());
        }

        return $catalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'multiple_sources';
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue($empty = true)
    {
        $catalogue = new MessageCatalogue($this->locale);

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'getDefaultCatalogue')) {
                $catalogue->add($provider->getDefaultCatalogue());
            }
        }

        return $catalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        $directories = [];

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'getDefaultResourceDirectory')) {
                foreach ($provider->getDefaultResourceDirectory() as $directory) {
                    $directories[] = $directory;
                }
            }
        }

        return $directories;
    }

    /**
     * {@inheritdoc}
     */
    public function getXliffCatalogue()
    {
        $catalogue = new MessageCatalogue($this->locale);

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'getXliffCatalogue')) {
                $catalogue->add($provider->getXliffCatalogue());
            }
        }

        return $catalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        $catalogue = new MessageCatalogue($this->locale);

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'getDatabaseCatalogue')) {
                $catalogue->add($provider->getDatabaseCatalogue());
            }
        }

        return $catalogue;
    }
}
