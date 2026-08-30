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

/**
 * A second extra-property test module, used to validate that two modules declaring extra properties on the same
 * entity (product) for the Admin API cohabit correctly, each keyed under its own module name (no cross-contamination).
 */
class extrapropertytest2 extends Module
{
    public function __construct()
    {
        $this->name = 'extrapropertytest2';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Extra Property Test 2');
        $this->description = $this->l('A second module to test extra properties cohabitation on the Admin API');
    }

    public function install()
    {
        if (parent::install() == false) {
            return false;
        }

        // product / extra_tag — STRING, COMMON scope, exposed on the product API endpoints and grid. Declared by a
        // SECOND module on the same entity as extrapropertytest, to prove two modules cohabit without clashing.
        if (!$this->registerExtraProperty(new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'extra_tag',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            constraints: [new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])],
            associatedGrids: ['product'],
            associatedApis: ['/products', '/products/{productId}'],
            labelWording: 'API extra tag',
        ))) {
            return false;
        }

        // product / api_only_note — STRING, LANG scope, exposed on the product API endpoints but on NO grid. The
        // product grid never fetches it, so in the API product list it must be read through the batch reader rather
        // than reused from the grid-record collector (this is the case the video_link bug was about).
        if (!$this->registerExtraProperty(new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'api_only_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::LANG,
            constraints: [new Assert\All([new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME])])],
            associatedApis: ['/products', '/products/{productId}'],
            labelWording: 'API only note',
        ))) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $this->unregisterExtraProperty(new ExtraPropertyDefinition('product', 'extra_tag'), true);
        $this->unregisterExtraProperty(new ExtraPropertyDefinition('product', 'api_only_note'), true);

        return parent::uninstall();
    }
}
