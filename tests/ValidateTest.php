<?php 

use PrestaShop as PrestaShop;

class ValidateTest extends PHPUnit_Framework_TestCase {
	
	private $faker;
	
	public function setUp() {
		PrestaShop\Test\Runtime :: disableDb();
		$this->faker = Faker\Factory :: create('fr_FR');
	}
	
	public function testIsSiretReturnsTrue() {
		$this->markTestSkipped('Faker doesn\'t seem to return valid SIRET, only well formatted ones');
		for ($i = 0; $i < 10; $i++) {
			$siret = str_replace(' ', '', $this->faker->siret);
			$this->assertTrue(Validate :: isSiret($siret));
		}
	}
	
	public function testIsSiretReturnsFalse() {
		$codes = array(
			'01234567890123', 
			'012345'
		);
		foreach ($codes as $siret) {
			$this->assertFalse(Validate :: isSiret($siret));
		}
	}
	
	/**
	 * @see http://www.rouen.cci.fr/outils/ape/homepage.asp
	 */
	public function testIsApeReturnsTrue() {
		$codes = array(
			'1101Z', // Production de boissons alcooliques distillées 
			'1102A', // Fabrication de vins effervescents 
 			'1102B', // Vinification 
 			'1103Z', // Fabrication de cidre et de vins de fruits 
 			'1104Z', // Production d'autres boissons fermentées non distillées 
 			'1105Z', // Fabrication de bière 
 			'1106Z', // Fabrication de malt 
 			'1107A', // Industrie des eaux de table 
 			'1107B'  // Production de boissons rafraîchissantes 
		);
		foreach ($codes as $ape) {
			$this->assertTrue(Validate :: isApe($ape));
		}
	}
	
	public function testIsApeReturnsFalse() {
		$codes = array(
			'1101ZA',
			'1102', 
			'ABCDEFG'
		);
		foreach ($codes as $ape) {
			$this->assertFalse(Validate :: isApe($ape));
		}
	}
	
	public function testIsLoadedObject() {
		
		$object = new StdClass();
		$object->id = 1;
		$this->assertTrue(Validate :: isLoadedObject($object));
		
		$object->id = null;
		$this->assertFalse(Validate :: isLoadedObject($object));
		
		$object = array('id' => 1);
		$this->assertFalse(Validate :: isLoadedObject($object));
		
	}
	
	public function testIsModuleUrlReturnsFase() {
		
		$unknowArchiveType = 'http://www.prestashop.com/module.rar';
		
		$errors = array();
		$this->assertFalse(Validate :: isModuleUrl($unknowArchiveType, $errors));
		
	}
	
}