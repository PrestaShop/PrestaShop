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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Sell\Product\SearchedProductItemType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Initiates input with ability to search for any type of product
 */
class ProductSearchType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $languageIsoCode;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $employeeIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->languageIsoCode = $employeeIsoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $refLabel = $this->trans('Ref: %s', 'Admin.Catalog.Feature');
        $router = $this->router;
        $languageIsoCode = $this->languageIsoCode;

        $resolver
            ->setRequired([
                'include_combinations',
            ])
            ->setAllowedTypes('include_combinations', 'bool')
            ->setDefaults([
                // error_bubbling => false allows constraint validators to set property path for error to be shown under this input, else it is not working
                'error_bubbling' => false,
                'required' => false,
                'label' => false,
                'placeholder' => $this->trans('Search product', 'Admin.Catalog.Help'),
                'min_length' => 3,
                'limit' => 1,
                'filters' => [],
                'identifier_field' => static function (Options $options): string {
                    return $options->offsetGet('include_combinations') === true ? 'unique_identifier' : 'id';
                },
                'entry_type' => static function (Options $options): string {
                    return $options->offsetGet('include_combinations') === true ? SearchedProductItemType::class : EntityItemType::class;
                },
                'remote_url' => static function (Options $options) use ($router, $languageIsoCode): string {
                    if ($options->offsetGet('include_combinations') === true) {
                        return $router->generate('admin_products_search_combinations_for_association', [
                            'languageCode' => $languageIsoCode,
                            'query' => '__QUERY__',
                            'filters' => $options['filters'],
                        ]);
                    } else {
                        return $router->generate('admin_products_search_products_for_association', [
                            'languageCode' => $languageIsoCode,
                            'query' => '__QUERY__',
                        ]);
                    }
                },
            ])
            ->setNormalizer('attr', static function (Options $options, ?array $value) use ($refLabel): array {
                if ($options->offsetGet('include_combinations') === true) {
                    return array_merge(['data-reference-label' => $refLabel], (array) $value);
                } else {
                    return $value;
                }
            })
        ;
    }

    public function getParent(): string
    {
        return EntitySearchInputType::class;
    }
}
