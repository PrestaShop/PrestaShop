<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
        if ($options['withDniAttr'] || $options['withPostcodeAttr']) {
            $this->needDni = $options['withDniAttr'];
            $this->needPostcode = $options['withPostcodeAttr'];
            $this->countriesAttr = $this->countriesAttrChoicesProvider->getChoicesAttributes();
        }
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = array_merge(
            ['--' => ''],
            $this->countriesChoiceProvider->getChoices()
        );

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_attr' => [$this, 'getChoiceAttr'],
            'withDniAttr' => false,
            'withPostcodeAttr' => false,
        ]);

        $resolver
            ->setAllowedTypes('withDniAttr', 'boolean')
            ->setAllowedTypes('withPostcodeAttr', 'boolean');
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
