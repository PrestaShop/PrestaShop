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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo;

use PrestaShop\PrestaShop\Adapter\Routes\RouteValidator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MetaFormDataProvider is responsible for providing configurations data and responsible for persisting data
 * in configuration database.
 */
final class MetaFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $setUpUrlDataConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $shopUrlsDataConfiguration;

    /**
     * @var DataConfigurationInterface
     */
    private $urlSchemaDataConfiguration;
    /**
     * @var RouteValidator
     */
    private $routeValidator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param DataConfigurationInterface $setUpUrlDataConfiguration
     * @param DataConfigurationInterface $shopUrlsDataConfiguration
     * @param DataConfigurationInterface $urlSchemaDataConfiguration
     * @param TranslatorInterface $translator
     * @param RouteValidator $routeValidator
     */
    public function __construct(
        DataConfigurationInterface $setUpUrlDataConfiguration,
        DataConfigurationInterface $shopUrlsDataConfiguration,
        DataConfigurationInterface $urlSchemaDataConfiguration,
        TranslatorInterface $translator,
        RouteValidator $routeValidator
    ) {
        $this->setUpUrlDataConfiguration = $setUpUrlDataConfiguration;
        $this->shopUrlsDataConfiguration = $shopUrlsDataConfiguration;
        $this->urlSchemaDataConfiguration = $urlSchemaDataConfiguration;
        $this->routeValidator = $routeValidator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'set_up_urls' => $this->setUpUrlDataConfiguration->getConfiguration(),
            'shop_urls' => $this->shopUrlsDataConfiguration->getConfiguration(),
            'url_schema' => $this->urlSchemaDataConfiguration->getConfiguration(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = $this->validateData($data);

        if (!empty($errors)) {
            return $errors;
        }

        return array_merge(
            $this->setUpUrlDataConfiguration->updateConfiguration($data['set_up_urls']),
            $this->shopUrlsDataConfiguration->updateConfiguration($data['shop_urls']),
            $this->urlSchemaDataConfiguration->updateConfiguration($data['url_schema'])
        );
    }

    /**
     * Implements custom validation for configuration form.
     *
     * @param array $data
     *
     * @return array - if array is not empty then error strings are returned.
     */
    private function validateData(array $data)
    {
        $urlSchemaErrors = $this->validateUrlSchema($data['url_schema']);

        return $urlSchemaErrors;
    }

    /**
     * Validates if configuration matches route pattern and if route has mandatory fields.
     *
     * @param array $configuration
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    private function validateUrlSchema(array $configuration)
    {
        $patternErrors = [];
        $requiredFieldErrors = [];
        foreach ($configuration as $routeId => $rule) {
            if (!$this->routeValidator->isRoutePattern($rule)) {
                $patternErrors[] = $this->translator->trans(
                  'The route %routeRule% is not valid',
                  [
                      '%routeRule%' => htmlspecialchars($rule)
                  ],
                  'Admin.Shopparameters.Feature'
                );
            }

            $missingKeywords = $this->routeValidator->doesRouteContainsRequiredKeywords($routeId, $rule);

            if (!empty($missingKeywords)) {
                foreach ($missingKeywords as $keyword) {
                    $requiredFieldErrors[] = $this->translator->trans(
                        'Keyword "{%keyword%}" required for route "%routeName%" (rule: "%routeRule%")',
                        [
                            '%keyword%' => $keyword,
                            '%routeName%' => $routeId,
                            '%routeRule%' => $rule,
                        ],
                        'Admin.Shopparameters.Feature'
                    );
                }
            }
        }

        if (!empty($patternErrors)) {
            return $patternErrors;
        }

        return $requiredFieldErrors;
    }
}
