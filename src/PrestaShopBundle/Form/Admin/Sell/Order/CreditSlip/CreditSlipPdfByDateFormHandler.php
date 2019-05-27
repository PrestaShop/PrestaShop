<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip;

use DateTime;
use PrestaShop\PrestaShop\Core\CreditSlip\CreditSlipDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Manages pdf generation of creditSlip in Sell > Orders > Credit Slip Generate pdf by date
 */
final class CreditSlipPdfByDateFormHandler extends FormHandler
{
    /**
     * @var CreditSlipDataProviderInterface
     */
    private $creditSlipDataProvider;

    /**
     * @var PDFGeneratorInterface
     */
    private $pdfGenerator;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $formDataProvider
     * @param array $formTypes
     * @param string $hookName
     * @param CreditSlipDataProviderInterface $creditSlipDataProvider
     * @param PDFGeneratorInterface $pdfGenerator
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        array $formTypes,
        $hookName,
        CreditSlipDataProviderInterface $creditSlipDataProvider,
        PDFGeneratorInterface $pdfGenerator
    ) {
        parent::__construct($formBuilder, $hookDispatcher, $formDataProvider, $formTypes, $hookName);
        $this->creditSlipDataProvider = $creditSlipDataProvider;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if ($errors = parent::save($data)) {
            return $errors;
        }

        // Get credit slips by submitted date interval
        $slipsCollection = $this->creditSlipDataProvider->getByDateInterval(
            new DateTime($data['generate_by_date']['date_from']),
            new DateTime($data['generate_by_date']['date_to'])
        );

        // Generate PDF out of found credit slips
        $this->pdfGenerator->generatePDF($slipsCollection);

        return [];
    }
}
