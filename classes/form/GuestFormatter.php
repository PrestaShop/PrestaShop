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
use Symfony\Contracts\Translation\TranslatorInterface;

class GuestFormatterCore implements FormFormatterInterface
{
    private $translator;
    private $language;

    public function __construct(
        TranslatorInterface $translator,
        Language            $language
    )
    {
        $this->translator = $translator;
        $this->language = $language;
    }

    public function getFormat()
    {
        $format['email'] = (new FormField())
            ->setName('email')
            ->setType('email')
            ->setLabel(
                $this->translator->trans(
                    'Email',
                    [],
                    'Shop.Forms.Labels'
                )
            )
            ->setRequired(true);

        $format['id_customer'] = (new FormField())
            ->setName('id_customer')
            ->setType('hidden')
            ->setValue('');

        return $this->addConstraints($format);
    }

    private function addConstraints(array $format)
    {
        $constraints = Customer::$definition['fields'];

        foreach ($format as $field) {
            if (!empty($constraints[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $constraints[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }
}
