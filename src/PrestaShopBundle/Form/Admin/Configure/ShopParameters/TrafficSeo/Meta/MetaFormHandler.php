<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class MetaFormHandler is responsible for providing data for Meta form and saving data.
 */
class MetaFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormDataProviderInterface
     */
    private $metaDataProvider;

    /**
     * MetaFormHandler constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param MetaFormDataProvider $metaDataProvider
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        MetaFormDataProvider $metaDataProvider
    ) {
        $this->formFactory = $formFactory;
        $this->metaDataProvider = $metaDataProvider;
    }

    /**
     * Get Meta form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->getMetaForm();
    }

    /**
     * Get form for given Meta.
     *
     * @param int $metaId
     *
     * @return FormInterface
     */
    public function getFormFor($metaId)
    {
        $data = $this->metaDataProvider->getData($metaId);
        $data['metaId'] = $metaId;
        return $this->getMetaForm($data);
    }

    /**
     * Saves meta form.
     *
     * @param array $data
     *
     * @return array - if array contains strings then it returned errors.
     */
    public function save(array $data)
    {
        return $this->metaDataProvider->saveData($data);
    }

    /**
     * Gets meta form with.
     *
     * @param array|null $metaFormData - if data is provided then it gets data with MetaType form field results.
     *
     * @return FormInterface
     */
    private function getMetaForm(array $metaFormData = null)
    {
        $builder = $this->formFactory->createBuilder()
            ->add('meta', MetaType::class)
            ->setData([
                'meta' => $metaFormData,
            ])
        ;

        return $builder->getForm();
    }
}
