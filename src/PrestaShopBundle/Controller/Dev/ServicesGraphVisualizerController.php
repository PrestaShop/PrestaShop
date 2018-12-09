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

namespace PrestaShopBundle\Controller\Dev;

use PrestaShop\PrestaShop\Core\Dev\ServicesGraph\ServicesGraphDumper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ServicesGraphVisualizerController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        if ($request->getMethod() === Request::METHOD_POST) {

            $controllerFilepath = $request->request->get('controller_filepath_input');
            $printGraphAsText = ('on' === $request->request->get('print_graph_as_text'));

            if (empty($controllerFilepath)) {
                throw new \Exception('Bad controller filepath (empty)');
            }

            $realpath = $this->getParameter('ps_root_dir') . trim($controllerFilepath);

            /** @var ServicesGraphDumper $graphDumper */
            $graphDumper = $this->get('prestashop.dev.graph_dumper');
            $graph = $graphDumper->buildAndDumpGraphForController($realpath);

        } else {
            $graph = '';
            $controllerFilepath = '';
            $printGraphAsText = false;
        }

        return $this->render(
            '@PrestaShop/Dev/graph.html.twig',
            [
                'graph_data' => $graph,
                'controllerFilepath' => $controllerFilepath,
                'printGraphAsText' => $printGraphAsText,
            ]
        );
    }
}
