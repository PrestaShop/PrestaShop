<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerPreferencesFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    public function __construct(
        FormFactoryInterface $formFactory,
        FormDataProviderInterface $dataProvider,
        TranslatorInterface $translator,
        HookDispatcher $dispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->dataProvider = $dataProvider;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $builder = $this->formFactory->createBuilder()
            ->add('general', GeneralType::class)
            ->setData($this->dataProvider->getData());

        $this->dispatcher->dispatchForParameters('displayCustomerPreferencesForm', [
            'form_builder' => $builder,
        ]);

        return $builder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        $errors = $this->dataProvider->setData($data);

        $this->dispatcher->dispatchForParameters('actionCustomerPreferencesSave', [
            'errors' => &$errors,
            'form_data' => &$data,
        ]);

        return $errors;
    }

    /**
     * Perform validations on form data
     *
     * @param array $data
     *
     * @return array    Array of errors if any
     */
    protected function validate(array $data)
    {
        $invalidFields = [];

        $passwordResetDelay = $data['general']['password_reset_delay'];
        if (!is_numeric($passwordResetDelay) || $passwordResetDelay < 0) {
            $invalidFields[] = $this->translator->trans(
                'Password reset delay',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $errors = [];
        foreach ($invalidFields as $field) {
            $errors[] = [
                'key' => 'The %s field is invalid.',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => [$field],
            ];
        }

        return $errors;
    }
}
