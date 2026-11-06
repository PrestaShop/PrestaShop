<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditBusinessEntityStatusType extends TranslatorAwareType
{
    public const FIELD_STATUS = 'status';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_STATUS, EnumType::class, [
                'label' => $this->trans('New status', 'Admin.Orderscustomers.Feature'),
                'class' => BusinessEntityStatus::class,
                'constraints' => [new NotBlank()],
            ]);
    }
}
