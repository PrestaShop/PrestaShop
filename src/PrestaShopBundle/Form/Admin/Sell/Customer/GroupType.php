<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    /**
     * @var GroupByIdChoiceProvider
     */
    private $groupByIdChoiceProvider;

    public function __construct(GroupByIdChoiceProvider $groupByIdChoiceProvider)
    {
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->groupByIdChoiceProvider->getChoices(),
            'choice_translation_domain' => false,
        ]);
    }
}
