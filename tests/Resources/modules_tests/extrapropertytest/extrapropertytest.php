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
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Component\Validator\Constraints as Assert;

if (!defined('_PS_VERSION_')) {
    exit;
}

class extrapropertytest extends Module
{
    public function __construct()
    {
        $this->name = 'extrapropertytest';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Extra Property Test');
        $this->description = $this->l('A module to test extra properties on the Admin API');
    }

    public function install()
    {
        if (parent::install() == false) {
            return false;
        }

        // product / api_flag — BOOL, COMMON scope, exposed on the product API endpoints AND the product grid.
        // It is associated with the grid too so the API product list reuses the value fetched by the grid query
        // and exposes it inline at the item root (only properties in associatedGrids ∩ associatedApis show in lists).
        if (!$this->registerExtraProperty(new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'api_flag',
            type: ExtraPropertyType::BOOL,
            scope: ExtraPropertyScope::COMMON,
            constraints: [new Assert\Type('bool')],
            associatedGrids: ['product'],
            associatedApis: ['/products', '/products/{productId}'],
            labelWording: 'API flag',
        ))) {
            return false;
        }

        // product / api_note — STRING, LANG scope (per-language values), exposed on the product API endpoints and
        // the product grid. In a list it surfaces as the single current-locale scalar fetched by the grid query.
        if (!$this->registerExtraProperty(new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'api_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::LANG,
            constraints: [new Assert\All([new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])])],
            associatedGrids: ['product'],
            associatedApis: ['/products', '/products/{productId}'],
            labelWording: 'API note',
        ))) {
            return false;
        }

        // customer / api_score — INT, COMMON scope, exposed on the customer API endpoints
        if (!$this->registerExtraProperty(new ExtraPropertyDefinition(
            entityName: 'customer',
            propertyName: 'api_score',
            type: ExtraPropertyType::INT,
            scope: ExtraPropertyScope::COMMON,
            constraints: [new Assert\PositiveOrZero()],
            associatedApis: ['/customers', '/customers/{customerId}'],
        ))) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $this->unregisterExtraProperty(new ExtraPropertyDefinition('product', 'api_flag'), true);
        $this->unregisterExtraProperty(new ExtraPropertyDefinition('product', 'api_note'), true);
        $this->unregisterExtraProperty(new ExtraPropertyDefinition('customer', 'api_score'), true);

        return parent::uninstall();
    }
}
