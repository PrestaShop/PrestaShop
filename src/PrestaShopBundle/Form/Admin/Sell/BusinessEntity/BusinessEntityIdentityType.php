<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Names identifying the company. Declared with inherit_data so the submitted data stays flat
 * under the general information section; the nesting only exists to lay the fields out.
 */
class BusinessEntityIdentityType extends TranslatorAwareType
{
    public const MAX_NAME_LENGTH = 255;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(BusinessEntityGeneralInformationType::FIELD_NAME, TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('The display name of the business entity.', 'Admin.Catalog.Feature'),
                'constraints' => $this->getNameConstraints(),
                'required' => true,
            ])
            ->add(BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME, TextType::class, [
                'label' => $this->trans('Legal name', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans('The official registered name of the company.', 'Admin.Catalog.Feature'),
                'constraints' => $this->getNameConstraints(),
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('inherit_data', true);
    }

    /**
     * @return array<int, Constraint>
     */
    private function getNameConstraints(): array
    {
        return [
            new NotBlank([
                'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
            ]),
            new Length([
                'max' => self::MAX_NAME_LENGTH,
                'maxMessage' => $this->trans(
                    'This field cannot be longer than %limit% characters',
                    'Admin.Notifications.Error',
                    ['%limit%' => self::MAX_NAME_LENGTH]
                ),
            ]),
            new TypedRegex([
                'type' => TypedRegex::TYPE_GENERIC_NAME,
            ]),
            new CleanHtml([
                'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
            ]),
        ];
    }
}
