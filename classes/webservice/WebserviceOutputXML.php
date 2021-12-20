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
class WebserviceOutputXMLCore implements WebserviceOutputInterface
{
    private const XLINK_NS = 'http://www.w3.org/1999/xlink';

    public function getContentType()
    {
        return 'text/xml';
    }

    /**
     * Iterate over ApiNode tree and returns XML formatted output.
     * If requested schema is DETAIL instead of node, parent resource is not being printed out (i.e. <strong>&lt;products/&gt;</strong> tag)
     *
     * @param Apinode $apiNode
     * @param int $type_of_view Use constants WebserviceOutputBuilderCore::VIEW_DETAILS / WebserviceOutputBuilderCore::VIEW_LIST
     *
     * @return string Properly XML formatted string
     */
    public function renderNode($apiNode)
    {
        $xml = new SuperXMLElement("<?xml version='1.0' encoding='UTF-8'?><prestashop xmlns:xlink='" . self::XLINK_NS . "'/>");

        /* @var $rootXml SuperXMLElement */
        if ($apiNode->getType() == ApiNode::TYPE_LIST) {
            $rootXml = $xml->addChild($apiNode->getName(), $apiNode->getValue());
        } else {
            $rootXml = $xml;
    }

        $this->injectAttributes($rootXml, $apiNode);
        $this->injectChildren($rootXml, $apiNode);

        return $xml->asXML();
    }

    /**
     * Iterates over all attributes specified in ApiNode and injects them into $xml
     *
     * @param \SuperXMLElement $xml
     * @param ApiNode $node
     *
     * @return void
     */
    private function injectAttributes(&$xml, $node)
    {
        if (empty($node->getAttributes()) || !is_array($node->getAttributes())) {
            return;
        }

        foreach ($node->getAttributes() as $name => $value) {
            if (strpos($name, 'xlink:') === 0) {    //when name begins on xlink, inject it underneath proper namespace
                $xml->addAttribute($name, $value, self::XLINK_NS);
        } else {
                $xml->addAttribute($name, $value);
                }
            }
            }

    /**
     * Inject children node
     *
     * @param \SuperXMLElement $parentXml
     * @param ApiNode $apiNode
     *
     * @return void
     */
    private function injectChildren(&$parentXml, $apiNode)
    {
        if (empty($apiNode->getNodes())) {
            return;
                }

        foreach ($apiNode->getNodes() as $node) {
            /* @var $node ApiNode */
            $childXml = $node->getType() == ApiNode::TYPE_VALUE ? $parentXml->addChildCData($node->getName(), $node->getValue()) : $parentXml->addChild($node->getName(), $node->getValue());
            $this->injectAttributes($childXml, $node);
            $this->injectChildren($childXml, $node);
    }
        }
    }
