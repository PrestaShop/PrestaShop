<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Domain\Database\DataLimits;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class enabling TinyMCE on a Textarea field.
 */
class FormattedTextareaType extends TranslatorAwareType
{
    /**
     * Max size of UTF-8 content in MySQL text columns
     */
    public const LIMIT_TINYTEXT_UTF8 = DataLimits::LIMIT_TINYTEXT_UTF8_4BYTE;
    public const LIMIT_TEXT_UTF8 = DataLimits::LIMIT_TEXT_UTF8_4BYTE;
    public const LIMIT_MEDIUMTEXT_UTF8 = DataLimits::LIMIT_MEDIUMTEXT_UTF8_4BYTE;
    public const LIMIT_LONGTEXT_UTF8 = DataLimits::LIMIT_LONGTEXT_UTF8_4BYTE;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined(['message'])
            ->setDefaults([
                'autoload' => true, // Start automatically TinyMCE
                'limit' => DataLimits::LIMIT_TEXT_UTF8_4BYTE,
            ])
            ->setAllowedTypes('limit', 'int')
            ->setAllowedTypes('autoload', 'bool')
            ->setAllowedTypes('message', ['string', 'null'])
            ->setNormalizer('constraints', function (Options $options, $constraints) {
                $limit = $options->offsetGet('limit');
                // provide message from options if exists, or default one
                $message = $options->offsetExists('message') ? $options->offsetGet('message') : $this->trans(
                    'This field cannot be longer than %limit% characters.',
                    'Admin.Notifications.Error',
                    [
                        '%limit%' => $limit,
                    ]
                );
                foreach ($constraints as $constraint) {
                    if ($constraint instanceof TinyMceMaxLength) {
                        // this means the TinyMceMaxLength constraint was overridden by child form, so we don't need to do anything
                        return $constraints;
                    }
                }
                // add length constraint
                $constraints[] = new TinyMceMaxLength([
                    'max' => $limit,
                    'message' => $message,
                ]);

                return $constraints;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if (!isset($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = '';
        }

        if (true === $options['autoload']) {
            $view->vars['attr']['class'] .= ' autoload_rte';
        }
        $view->vars['attr']['counter'] = $options['limit'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }
}
