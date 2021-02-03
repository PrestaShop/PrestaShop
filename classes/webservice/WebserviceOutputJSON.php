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
class WebserviceOutputJSONCore implements WebserviceOutputInterface
{
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * Main function used to render node in desired format
     *
     * @param ApiNode $apiNode
     * @param int $type_of_view Use constants WebserviceOutputBuilderCore::VIEW_DETAILS / WebserviceOutputBuilderCore::VIEW_LIST
     *
     * @return string json-encoded string
     */
    public function renderNode($apiNode)
    {
        if ($apiNode->getType() == ApiNode::TYPE_LIST) {
            $jsonArray = [$apiNode->getName() => $this->toJsonArray($apiNode)];
        } else {
            $jsonArray = $this->toJsonArray($apiNode);
        }

        return json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Transform tree structure of desired node to array, suitable for JSON output.
     * JSON output completely ignores attributes - those are used just in XML output.
     *
     * @param ApiNode $apiNode
     *
     * @return array|string Node type ApiNode::TYPE_NODE returns just value as string.
     *                      Node type ApiNode::TYPE_PARENT returns recursive array of underlying nodes in the form of [Name => [... subnodes ...]]
     *                      Node type ApiNode::TYPE_LIST returns recursive array of underlying nodes in the form of [[... subnodes ...]]
     */
    private function toJsonArray($apiNode)
    {
        switch ($apiNode->getType()) {
            case ApiNode::TYPE_VALUE:
                return $apiNode->getValue();
            case ApiNode::TYPE_LANGUAGE:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    /* @var $node ApiNode */
                    $langId = $node->getAttributes()['id'];
                    $value = $node->getValue();
                    $out[] = ['id' => $langId, 'value' => $value];
                }

                return $out;
            case ApiNode::TYPE_LIST:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    $out[] = $this->toJsonArray($node);
                }

                return $out;
            case ApiNode::TYPE_PARENT:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    $out[$node->getName()] = $this->toJsonArray($node);
                }

                return $out;
        }
    }
}
