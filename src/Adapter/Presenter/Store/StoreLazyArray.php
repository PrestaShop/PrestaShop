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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Store;

use Address;
use AddressFormat;
use Language;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use Store;
use Symfony\Contracts\Translation\TranslatorInterface;

class StoreLazyArray extends AbstractLazyArray
{
    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var array
     */
    protected $store;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        array $store,
        Language $language,
        ImageRetriever $imageRetriever,
        TranslatorInterface $translator,
    ) {
        $this->store = $store;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->translator = $translator;

        parent::__construct();
        $this->appendArray($this->store);
    }

    /**
     * @arrayAccess
     *
     * @return array|null
     */
    public function getImage()
    {
        return $this->imageRetriever->getImage(
            new Store($this->store['id'], $this->language->getId()),
            $this->store['id']
        );
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getAddress()
    {
        // If we already have processed address, let's return it directly
        if (isset($this->store['address'])) {
            return $this->store['address'];
        }

        // Prepare new address object and address array
        // The object will be used only for generating the formatted version
        $this->store['address'] = [];
        $addressObj = new Address();
        $attr = ['address1', 'address2', 'postcode', 'city', 'id_state', 'id_country'];
        foreach ($attr as $a) {
            $addressObj->{$a} = $this->store[$a];
            $this->store['address'][$a] = $this->store[$a];
        }
        $this->store['address']['formatted'] = AddressFormat::generateAddress($addressObj, [], '<br />');

        return $this->store['address'];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getBusinessHours()
    {
        if (isset($this->store['business_hours'])) {
            return $this->store['business_hours'];
        }

        $temp = json_decode($this->store['hours'], true);
        $this->store['business_hours'] = [
            [
                'day' => $this->translator->trans('Monday', [], 'Shop.Theme.Global'),
                'hours' => $temp[0],
            ],
            [
                'day' => $this->translator->trans('Tuesday', [], 'Shop.Theme.Global'),
                'hours' => $temp[1],
            ],
            [
                'day' => $this->translator->trans('Wednesday', [], 'Shop.Theme.Global'),
                'hours' => $temp[2],
            ],
            [
                'day' => $this->translator->trans('Thursday', [], 'Shop.Theme.Global'),
                'hours' => $temp[3],
            ],
            [
                'day' => $this->translator->trans('Friday', [], 'Shop.Theme.Global'),
                'hours' => $temp[4],
            ],
            [
                'day' => $this->translator->trans('Saturday', [], 'Shop.Theme.Global'),
                'hours' => $temp[5],
            ],
            [
                'day' => $this->translator->trans('Sunday', [], 'Shop.Theme.Global'),
                'hours' => $temp[6],
            ],
        ];

        return $this->store['business_hours'];
    }
}
