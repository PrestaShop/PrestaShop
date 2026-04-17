<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeOrdersStatusType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatusChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $orderStatusChoiceProvider
     */
    public function __construct(FormChoiceProviderInterface $orderStatusChoiceProvider)
    {
        $this->orderStatusChoiceProvider = $orderStatusChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_order_status_id', ChoiceType::class, [
                'choices' => $this->orderStatusChoiceProvider->getChoices(),
                'translation_domain' => false,
            ])
            ->add('order_ids', CollectionType::class, [
                'allow_add' => true,
                'entry_type' => HiddenType::class,
                'label' => false,
            ])
        ;

        $builder->get('order_ids')
            ->addModelTransformer(new CallbackTransformer(
                static function ($orderIds) {
                    return $orderIds;
                },
                static function (array $orderIds) {
                    return array_map(static function ($orderId) {
                        return (int) $orderId;
                    }, $orderIds);
                }
            ))
        ;
    }
}
