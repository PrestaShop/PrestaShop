<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form type is not used in the project but in the tests, it allows to build a simple
 * form type for combination listener and use it in test.
 *
 * @see CombinationListener
 * @see CombinationListenerTest
 */
class TestCombinationFormType extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock', FormType::class)
        ;

        $stock = $builder->get('stock');
        $stock->add('quantities', FormType::class);
        $quantities = $stock->get('quantities');
        $quantities->add('stock_movements', FormType::class);
    }
}
