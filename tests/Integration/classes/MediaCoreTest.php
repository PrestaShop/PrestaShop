<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Configuration;
use Media;

class MediaCoreTest extends IntegrationTestCase
{
    public function testCorrectJQueryNoConflictURL()
    {
        $domain = Configuration::get('PS_SHOP_DOMAIN');
        $result = Media::getJqueryPath('1.11');
        $this->assertEquals(true, in_array('http://'.$domain.__PS_BASE_URI__.'js/jquery/jquery.noConflict.php?version=1.11', $result));
    }

	public function isJsInputsProvider()
	{
		return array(
			array('<script>test 1</script>', '<script>/* <![CDATA[ */;test 1;/* ]]> */</script>'),
			array('<script type="text/javascript">test 2</script>', '<script type="text/javascript">/* <![CDATA[ */;test 2;/* ]]> */</script>'),
			array('<script type="javascript">test 3</script>', '<script type="javascript">/* <![CDATA[ */;test 3;/* ]]> */</script>'),
			array('<script type= "javascript" nonsense>test 4</script>', '<script type= "javascript" nonsense>/* <![CDATA[ */;test 4;/* ]]> */</script>'),
			array('<script language="JavaScript" type="text/javascript">test 5</script>', '<script language="JavaScript" type="text/javascript">/* <![CDATA[ */;test 5;/* ]]> */</script>'),
			array('<script class="myJS" type="text/javascript">test 6</script>', '<script class="myJS" type="text/javascript">/* <![CDATA[ */;test 6;/* ]]> */</script>'),
			array('<scripttype="text/javascript"> test 7</script>', '<scripttype="text/javascript"> test 7</script>'),
			array('<script type="application/javascript">test 8</script>', '<script type="application/javascript">/* <![CDATA[ */;test 8;/* ]]> */</script>'),
			array('<script type=\'application/javascript\'>test 9</script>', '<script type=\'application/javascript\'>/* <![CDATA[ */;test 9;/* ]]> */</script>'),
			array('<script type=\'text/javascript\'>test 10</script>', '<script type=\'text/javascript\'>/* <![CDATA[ */;test 10;/* ]]> */</script>'),
			array('<script type=\'javascript\'>test 11</script>', '<script type=\'javascript\'>/* <![CDATA[ */;test 11;/* ]]> */</script>'),
			array('<script type="application/ld+json">{"@context": http://schema.org","@type": "Product","name": "[the name of the product]","aggregateRating": {"@type": "AggregateRating","ratingValue": "[rating]","reviewCount": "[number of reviews]"}}</script>', '<script type="application/ld+json">{"@context": http://schema.org","@type": "Product","name": "[the name of the product]","aggregateRating": {"@type": "AggregateRating","ratingValue": "[rating]","reviewCount": "[number of reviews]"}}</script>'),
		);
	}

	/**
	 * @dataProvider isJsInputsProvider
	 */
	public function testPackJSinHTML($input, $output)
	{
		$return = Media::packJSinHTML($input);
		$this->assertEquals($output, $return, 'packJSinHTML failed for data input='.$input);
	}
}
