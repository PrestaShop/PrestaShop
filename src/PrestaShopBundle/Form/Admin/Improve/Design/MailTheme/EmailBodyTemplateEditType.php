<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\MailTheme;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailBodyTemplateEditType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('html_content', FormattedTextareaType::class, [
                'label' => $this->trans('HTML content', 'Admin.International.Feature'),
                'required' => false,
                'limit' => FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8,
                'attr' => [
                    'rows' => 20,
                ],
            ])
            ->add('txt_content', TextareaType::class, [
                'label' => $this->trans('Text content', 'Admin.International.Feature'),
                'required' => false,
                'attr' => [
                    'rows' => 20,
                ],
            ])
        ;
    }
}
