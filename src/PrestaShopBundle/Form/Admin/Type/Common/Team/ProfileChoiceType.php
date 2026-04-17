<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type\Common\Team;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProfileChoiceType is choice type for selecting employee's profile.
 */
class ProfileChoiceType extends AbstractType
{
    /**
     * @var array
     */
    private $profileChoices;

    /**
     * @param array $profileChoices
     */
    public function __construct(array $profileChoices)
    {
        $this->profileChoices = $profileChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => $this->profileChoices,
                'translation_domain' => false,
                'expanded' => false,
                'multiple' => false,
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
