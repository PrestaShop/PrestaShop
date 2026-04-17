<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PageLayoutsCustomizationType is used to customize Front Office theme's page layouts.
 */
class PageLayoutsCustomizationType extends AbstractType
{
    /**
     * @var array
     */
    private $pageLayoutsChoices;

    /**
     * @param array $pageLayoutsChoices
     */
    public function __construct(array $pageLayoutsChoices)
    {
        $this->pageLayoutsChoices = $pageLayoutsChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('layouts', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'translation_domain' => false,
                'entry_options' => [
                    'label' => false,
                    'translation_domain' => false,
                    'choices' => $this->pageLayoutsChoices,
                ],
            ]);
    }
}
