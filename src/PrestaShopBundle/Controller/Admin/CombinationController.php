<?php

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Adapter\CombinationDataProvider;
use PrestaShopBundle\Form\Admin\Product\ProductCombination;

class CombinationController extends Controller
{
    public function generateCombinationFormAction($combinationIds)
    {
        $response = new Response();
        $result = '';

        $combinations = explode('-', $combinationIds);
        if ($combinationIds == 0 || count($combinations) == 0) {
            return $response;
        }

        $combinationDataProvider = new combinationDataProvider();

        foreach ($combinations as $combinationId) {
            $form = $this->createForm(ProductCombination::class, $combinationDataProvider->getFormCombination($combinationId));
            $result .= $this->renderView('PrestaShopBundle:Admin/Product/Include:form_combination.html.twig', ['form' => $form->createView()]);
        }

        return $response->create($result);
    }
}
