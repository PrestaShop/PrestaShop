<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\MailTheme;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MailThemeConfigurationType is used to create the form allowing to define mail
 * theme settings (for now just the defaultTheme).
 */
class MailThemeConfigurationType extends AbstractType
{
    /** @var array */
    private $mailThemes;

    /**
     * @param array $mailThemes
     */
    public function __construct(array $mailThemes)
    {
        $this->mailThemes = $mailThemes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultTheme', ChoiceType::class, [
                'choices' => $this->mailThemes,
            ])
        ;
    }
}
