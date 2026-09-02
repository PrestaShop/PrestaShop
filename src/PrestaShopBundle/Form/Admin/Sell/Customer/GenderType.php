<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GenderByIdChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenderType extends AbstractType
{
    /**
     * @var GenderByIdChoiceProvider
     */
    private $genderByIdChoiceProvider;

    public function __construct(GenderByIdChoiceProvider $genderByIdChoiceProvider)
    {
        $this->genderByIdChoiceProvider = $genderByIdChoiceProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->genderByIdChoiceProvider->getChoices(),
            'choice_translation_domain' => false,
            'label' => 'Social title',
            'translation_domain' => 'Admin.Global',
        ]);
    }
}
