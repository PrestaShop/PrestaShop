<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StoreCore extends ObjectModel
{
	/** @var integer Country id */
	public		$id_country;

	/** @var integer State id */
	public		$id_state;
	
	/** @var string Store name */
	public 		$name;
	
	/** @var string Address first line */
	public 		$address1;

	/** @var string Address second line (optional) */
	public 		$address2;

	/** @var string Postal code */
	public 		$postcode;

	/** @var string City */
	public 		$city;
	
	/** @var float Latitude */
	public 		$latitude;
	
	/** @var float Longitude */
	public 		$longitude;
	
	/** @var string Store hours (PHP serialized) */
	public 		$hours;
	
	/** @var string Phone number */
	public 		$phone;
	
	/** @var string Fax number */
	public 		$fax;
	
	/** @var string Note */
	public		$note;
	
	/** @var string e-mail */
	public 		$email;
	
	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;
	
	/** @var boolean Store status */
	public 		$active = true;
	
 	protected 	$fieldsRequired = array('id_country', 'name', 'address1', 'city', 'active');
 	protected 	$fieldsSize = array('name' => 128, 'address1' => 128, 'address2' => 128, 'postcode' => 12, 'city' => 64, 'latitude' => 10, 'longitude' => 10, 'hours' => 254, 'phone' => 16, 'fax' => 16, 'email' => 128, 'note' => 65000);
 	protected 	$fieldsValidate = array('id_country' => 'isUnsignedId', 'id_state' => 'isNullOrUnsignedId', 'name' => 'isGenericName', 'address1' => 'isAddress', 'address2' => 'isAddress',
	'city' => 'isCityName', 'latitude' => 'isCoordinate', 'longitude' => 'isCoordinate', 'hours' => 'isSerializedArray', 'phone' => 'isPhoneNumber', 'fax' => 'isPhoneNumber',
	'note' => 'isCleanHtml', 'email' => 'isEmail', 'active' => 'isBool');

	protected 	$table = 'store';
	protected 	$identifier = 'id_store';
	
	protected	$webserviceParameters = array(
		'fields' => array(
			'id_country' => array('xlink_resource'=> 'countries'),
			'id_state' => array('xlink_resource'=> 'states'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		
		$fields['id_country'] = (int)($this->id_country);
		$fields['id_state'] = (int)($this->id_state);
		$fields['name'] = pSQL($this->name);
		$fields['address1'] = pSQL($this->address1);
		$fields['address2'] = pSQL($this->address2);
		$fields['postcode'] = pSQL($this->postcode);
		$fields['city'] = pSQL($this->city);
		$fields['latitude'] = (float)($this->latitude);
		$fields['longitude'] = (float)($this->longitude);
		$fields['hours'] = pSQL($this->hours);
		$fields['phone'] = pSQL($this->phone);
		$fields['fax'] = pSQL($this->fax);
		$fields['note'] = pSQL($this->note);
		$fields['email'] = pSQL($this->email);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)($this->active);
		
		return $fields;
	}
}


