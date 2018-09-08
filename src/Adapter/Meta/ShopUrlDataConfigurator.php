<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopDatabaseException;
use PrestaShopException;
use ShopUrl;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ShopUrlDataConfigurator is responsible for updating and getting data from shop_url table.
 */
final class ShopUrlDataConfigurator implements DataConfigurationInterface
{
    /**
     * @var ShopUrl
     */
    private $mainShopUrl;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Validate
     */
    private $validate;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ShopUrlsDataConfigurator constructor.
     *
     * @param ShopUrl $mainShopUrl
     * @param ConfigurationInterface $configuration
     * @param Validate $validate
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ShopUrl $mainShopUrl,
        ConfigurationInterface $configuration,
        Validate $validate,
        TranslatorInterface $translator
    ) {
        $this->mainShopUrl = $mainShopUrl;
        $this->configuration = $configuration;
        $this->validate = $validate;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'domain' => $this->mainShopUrl->domain,
            'domain_ssl' => $this->mainShopUrl->domain_ssl,
            'physical_uri' => $this->mainShopUrl->physical_uri,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        try {
            if ($this->validateConfiguration($configuration)) {
                $this->mainShopUrl->domain = $configuration['domain'];
                $this->mainShopUrl->domain_ssl = $configuration['domain_ssl'];
                $this->mainShopUrl->physical_uri = (string) $configuration['physical_uri'];
                $this->mainShopUrl->update();

                $this->configuration->set('PS_SHOP_DOMAIN', $configuration['domain']);
                $this->configuration->set('PS_SHOP_DOMAIN_SSL', $configuration['domain_ssl']);
            }
        } catch (InvalidArgumentException $exception) {
            $errors[] = $exception->getMessage();
        } catch (PrestaShopException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function validateConfiguration(array $configuration)
    {
        $issetRequiredVars = isset(
            $configuration['domain'],
            $configuration['domain_ssl']
        );

        if (!$issetRequiredVars) {
            return false;
        }

        if (!$this->validate->isCleanHtml($configuration['domain'])) {
            throw new InvalidArgumentException(
                $this->translator->trans('This domain is not valid.', [], 'Admin.Notifications.Error')
            );
        }

        if (!$this->validate->isCleanHtml($configuration['domain_ssl'])) {
            throw new InvalidArgumentException(
                $this->translator->trans('The SSL domain is not valid.', [], 'Admin.Notifications.Error')
            );
        }

        return true;
    }
}
