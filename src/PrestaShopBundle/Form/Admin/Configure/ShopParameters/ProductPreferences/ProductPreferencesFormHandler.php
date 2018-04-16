<?php
/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Cache\CacheClearer;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class manages the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormDataProviderInterface
     */
    private $formDataProvider;

    /**
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param FormDataProviderInterface $formDataProvider
     * @param CacheClearer $cacheClearer
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormDataProviderInterface $formDataProvider,
        CacheClearer $cacheClearer,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->formDataProvider = $formDataProvider;
        $this->cacheClearer = $cacheClearer;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $builder = $this->formFactory->createBuilder()
            ->add('general', GeneralType::class)
            ->add('pagination', PaginationType::class)
            ->add('page', PageType::class)
            ->add('stock', StockType::class)
            ->setData($this->formDataProvider->getData())
        ;

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

        $this->cacheClearer->clearSmartyCache();
        $this->cacheClearer->clearMediaCache();

        if (!$data['stock']['stock_management']) {
            $data['stock']['allow_ordering_oos'] = 1;
            $data['page']['display_quantities'] = 0;
        }

        return $this->formDataProvider->setData($data);
    }

    /**
     * Perform validation on form data before saving it
     *
     * @param array $data
     *
     * @return array Returns array of errors
     */
    protected function validate(array $data)
    {
        $invalidFields = [];

        $newDaysNumber = $data['general']['new_days_number'];
        if (!is_numeric($newDaysNumber) || 0 > $newDaysNumber) {
            $invalidFields[] = $this->translator->trans(
                'Number of days for which the product is considered \'new\'',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $shortDescriptionLimit = $data['general']['short_description_limit'];
        if (!is_numeric($shortDescriptionLimit) || 0 > $shortDescriptionLimit) {
            $invalidFields[] = $this->translator->trans(
                'Max size of product summary',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $displayLastQuantities = $data['page']['display_last_quantities'];
        if (!is_numeric($displayLastQuantities) || 0 > $displayLastQuantities) {
            $invalidFields[] = $this->translator->trans(
                'Display remaining quantities when the quantity is lower than',
                [],
                'Admin.Shopparameters.Feature'
            );
        }

        $productsPerPage = $data['pagination']['products_per_page'];
        if (!is_numeric($productsPerPage) || 0 >  $productsPerPage) {
            $invalidFields[] = $this->translator->trans('Products per page', [], 'Admin.Shopparameters.Feature');
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
