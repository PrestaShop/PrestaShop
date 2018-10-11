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

namespace PrestaShop\PrestaShop\Core\SqlManager\Form;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObjectFormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObjectFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql\SqlRequestType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class SqlRequestFormFactory creates form used in Back Office for adding/editing SqlRequests.
 */
final class SqlRequestFormFactory implements IdentifiableObjectFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var IdentifiableObjectFormDataProviderInterface
     */
    private $sqlRequestFormDataProvider;

    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param IdentifiableObjectFormDataProviderInterface $sqlRequestFormDataProvider
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        IdentifiableObjectFormDataProviderInterface $sqlRequestFormDataProvider
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->sqlRequestFormDataProvider = $sqlRequestFormDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, $id = null)
    {
        $builder = $this->formFactory->createBuilder()
            ->add('request_sql', SqlRequestType::class)
        ;

        $this->hookDispatcher->dispatchWithParameters('actionSqlRequestFormModifier', [
            'form_builder' => $builder,
        ]);

        $builder->setData([
            'request_sql' => $this->sqlRequestFormDataProvider->getData($id),
        ]);

        return $builder->getForm();
    }
}
