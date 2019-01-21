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

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShopChoiceTreeType.
 */
class ShopChoiceTreeType extends AbstractType
{
    /**
     * @var array
     */
    private $shopTreeChoices;

    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @param array $shopTreeChoices
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     */
    public function __construct(
        array $shopTreeChoices,
        DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
    ) {
        $this->shopTreeChoices = $shopTreeChoices;
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->stringArrayToIntegerArrayDataTransformer);

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_tree' => $this->shopTreeChoices,
            'multiple' => true,
            'choice_label' => 'name',
            'choice_value' => 'id_shop',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MaterialChoiceTreeType::class;
    }
}
