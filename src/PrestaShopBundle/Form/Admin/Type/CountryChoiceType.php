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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CountryChoiceType is responsible for providing country choices with -- symbol in front of array.
 */
class CountryChoiceType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $countriesChoiceProvider;

    /**
     * @var FormChoiceAttributeProviderInterface
     */
    private $countriesAttrChoicesProvider;

    /**
     * @var array
     */
    private $countriesAttr = [];

    /**
     * @var bool
     */
    private $needDni = false;

    /**
     * @var bool
     */
    private $needPostcode = false;

    /**
     * @param FormChoiceProviderInterface $countriesChoiceProvider
     */
    public function __construct(FormChoiceProviderInterface $countriesChoiceProvider, FormChoiceAttributeProviderInterface $countriesAttrChoicesProvider)
    {
        $this->countriesChoiceProvider = $countriesChoiceProvider;
        $this->countriesAttrChoicesProvider = $countriesAttrChoicesProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['with_dni_attr'] || $options['with_postcode_attr']) {
            $this->needDni = $options['with_dni_attr'];
            $this->needPostcode = $options['with_postcode_attr'];
            $this->countriesAttr = $this->countriesAttrChoicesProvider->getChoicesAttributes();
        }
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->countriesChoiceProvider->getChoices(),
            'choice_attr' => [$this, 'getChoiceAttr'],
            'placeholder' => '--',
            'with_dni_attr' => false,
            'with_postcode_attr' => false,
        ]);

        $resolver
            ->setAllowedTypes('with_dni_attr', 'boolean')
            ->setAllowedTypes('with_postcode_attr', 'boolean');
    }

    public function getChoiceAttr($value, $key)
    {
        $attr = [];
        if ($this->needDni && isset($this->countriesAttr[$key], $this->countriesAttr[$key]['need_dni'])) {
            $attr['need_dni'] = 1;
        }
        if ($this->needPostcode && isset($this->countriesAttr[$key], $this->countriesAttr[$key]['need_postcode'])) {
            $attr['need_postcode'] = 1;
        }

        return $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
