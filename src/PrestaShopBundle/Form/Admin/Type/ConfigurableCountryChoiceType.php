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

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class responsible for providing configurable countries list
 * 
 * Form type documentation:
 * https://devdocs.prestashop-project.org/8/development/components/form/types-reference/configurable-country-choice-type/
 */
class ConfigurableCountryChoiceType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $countriesChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $countriesChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $countriesChoiceProvider)
    {
        $this->countriesChoiceProvider = $countriesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Set normalizer enables to use closure for choice generation with options
        $resolver->setNormalizer(
            'choices', function (Options $options) {
                return $this->countriesChoiceProvider->getChoices([
                    'active' => $options['active'],
                    'contains_states' => $options['contains_states'],
                    'list_states' => $options['list_states'],
                ]);
            }
        );

        $resolver->setDefaults([
            'active' => false,
            'contains_states' => false,
            'list_states' => false,
            'placeholder' => '--',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
