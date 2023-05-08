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

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds an option in the form builder to enable autocomplete on select inputs.
 */
class AutoCompleteExtension extends AbstractTypeExtension
{
    private const DEFAULT_MINIMUM_INPUT_LENGTH = 7;

    public static function getExtendedTypes(): iterable
    {
        return [
            ChoiceType::class,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('autocomplete');
        $resolver->setDefined('autocomplete_minimum_choices');

        $resolver->setAllowedTypes('autocomplete', 'bool');
        $resolver->setAllowedTypes('autocomplete_minimum_choices', 'int');

        $resolver->setNormalizer('attr', function (Options $options, ?array $attr) {
            if (isset($options['autocomplete']) && $options['autocomplete']) {
                $attr['data-toggle'] = 'select2';
                $attr['data-minimumResultsForSearch'] = $options['autocomplete_minimum_choices'] ?? self::DEFAULT_MINIMUM_INPUT_LENGTH;
            }

            return $attr;
        });
    }
}
