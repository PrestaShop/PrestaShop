<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $fieldErrors = [];
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

            $errors = $this->routeValidator->isRouteValid($routeId, $rule);

            foreach (['missing', 'unknown'] as $type) {
                if (!empty($errors[$type])) {
                    foreach ($errors[$type] as $keyword) {
                        $fieldErrors[] = $this->translator->trans(
                            $type === 'missing'
                                ? 'Keyword "{%keyword%}" required for route "%routeName%" (rule: "%routeRule%")'
                                : 'Keyword "{%keyword%}" doesn\'t exist for route "%routeName%" (rule: "%routeRule%")',
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
        }

        if (!empty($patternErrors)) {
            return $patternErrors;
        }

        return $fieldErrors;
    }
}
