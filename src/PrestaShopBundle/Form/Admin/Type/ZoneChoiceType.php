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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class is responsible for providing configurable zone choices with -- symbol in front of array.
 */
class ZoneChoiceType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $zonesChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $zonesChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $zonesChoiceProvider)
    {
        $this->zonesChoiceProvider = $zonesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setNormalizer(
            'choices', function (Options $options) {
                $choices = array_merge(
                    ['--' => ''],
                    $this->zonesChoiceProvider->getChoices([
                        'active' => $options['active'],
                        'activeFirst' => $options['activeFirst'],
                    ])
                );

                return $choices;
            }
        );

        $resolver->setDefaults([
            'active' => false,
            'activeFirst' => false,
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
