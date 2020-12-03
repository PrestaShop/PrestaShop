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
class WebserviceSpecificManagementSearchCore implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilder */
    protected $objOutput;
    protected $output;

    /** @var WebserviceRequest */
    protected $wsObject;

    /* ------------------------------------------------
     * GETTERS & SETTERS
     * ------------------------------------------------ */

    /**
     * @param WebserviceOutputBuilderCore $obj
     *
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;

        return $this;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setUrlSegment($segments)
    {
        $this->urlSegment = $segments;

        return $this;
    }

    public function getUrlSegment()
    {
        return $this->urlSegment;
    }

    /**
     * Management of search.
     */
    public function manage()
    {
        if (!isset($this->wsObject->urlFragments['query']) || !isset($this->wsObject->urlFragments['language'])) {
            throw new WebserviceException('You have to set both the \'language\' and \'query\' parameters to get a result', [100, 400]);
        }
        $objects_products = [];
        $objects_categories = [];
        $objects_products['empty'] = new Product();
        $objects_categories['empty'] = new Category();

        $this->_resourceConfiguration = $objects_products['empty']->getWebserviceParameters();

        if (!$this->wsObject->setFieldsToDisplay()) {
            return false;
        }

        $results = Search::find($this->wsObject->urlFragments['language'], $this->wsObject->urlFragments['query'], 1, 1, 'position', 'desc', true, false);
        $categories = [];
        foreach ($results as $result) {
            $current = new Product($result['id_product']);
            $objects_products[] = $current;
            $categories_result = $current->getWsCategories();
            foreach ($categories_result as $category_result) {
                foreach ($category_result as $id) {
                    $categories[] = $id;
                }
            }
        }
        $categories = array_unique($categories);
        foreach ($categories as $id) {
            $objects_categories[] = new Category($id);
        }

        $this->output .= $this->objOutput->getContent($objects_products, null, $this->wsObject->fieldsToDisplay, $this->wsObject->depth, WebserviceOutputBuilder::VIEW_LIST, false);
        // @todo allow fields of type category and product
        // $this->_resourceConfiguration = $objects_categories['empty']->getWebserviceParameters();
        // if (!$this->setFieldsToDisplay())
        // return false;

        $this->output .= $this->objOutput->getContent($objects_categories, null, $this->wsObject->fieldsToDisplay, $this->wsObject->depth, WebserviceOutputBuilder::VIEW_LIST, false);
    }

    /**
     * This must be return a string with specific values as WebserviceRequest expects.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->objOutput->getObjectRender()->overrideContent($this->output);
    }
}
