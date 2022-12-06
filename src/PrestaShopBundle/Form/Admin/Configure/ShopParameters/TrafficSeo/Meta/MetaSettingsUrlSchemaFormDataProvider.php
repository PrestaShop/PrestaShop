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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Adapter\Routes\RouteValidator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MetaSettingsFormDataProvider is responsible for providing configurations data and responsible for persisting data
 * in configuration database.
 */
final class MetaSettingsUrlSchemaFormDataProvider implements FormDataProviderInterface
{
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
     * @param DataConfigurationInterface $urlSchemaDataConfiguration
     * @param TranslatorInterface $translator
     * @param RouteValidator $routeValidator
     */
    public function __construct(
        DataConfigurationInterface $urlSchemaDataConfiguration,
        TranslatorInterface $translator,
        RouteValidator $routeValidator
    ) {
        $this->urlSchemaDataConfiguration = $urlSchemaDataConfiguration;
        $this->routeValidator = $routeValidator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->urlSchemaDataConfiguration->getConfiguration();
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

        return $this->urlSchemaDataConfiguration->updateConfiguration($data);
    }

    /**
     * Implements custom validation for configuration form.
     *
     * @param array $data
     *
     * @return array - if array is not empty then error strings are returned
     *
     * @throws PrestaShopException
     */
    private function validateData(array $data)
    {
        $patternErrors = [];
        $requiredFieldErrors = [];
        foreach ($data as $routeId => $rule) {
            if (!$this->routeValidator->isRoutePattern($rule)) {
                $patternErrors[] = $this->translator->trans(
                  'The route %routeRule% is not valid',
                  [
                      '%routeRule%' => htmlspecialchars($rule),
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
