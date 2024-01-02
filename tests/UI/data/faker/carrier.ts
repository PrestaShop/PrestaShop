import TaxRules from '@data/demo/taxRule';
import Zones from '@data/demo/zones';
import TaxRuleData from '@data/faker/taxRule';
import ZoneData from '@data/faker/zone';
import CarrierCreator from '@data/types/carrier';

import {faker} from '@faker-js/faker';

const taxes: string[] = Object.values(TaxRules).map((tax: TaxRuleData) => tax.name);
const zonesID: number[] = Object.values(Zones).map((zone: ZoneData) => zone.id);
const outOfRangeBehavior: string[] = ['Apply the cost of the highest defined range', 'Disable carrier'];
const billing: string[] = ['According to total price', 'According to total weight'];

/**
 * Create new carrier to use in carrier form on BO
 * @class
 */
export default class CarrierData {
  public readonly id: number;

  public readonly position: number;

  public readonly name: string;

  public readonly transitName: string;

  public readonly delay: string;

  public readonly speedGrade: number;

  public readonly trakingURL: string;

  public readonly handlingCosts: boolean;

  public readonly freeShipping: boolean;

  public readonly billing: string;

  public readonly taxRule: string;

  public readonly outOfRangeBehavior: string;

  public readonly rangeSup: number;

  public readonly allZones: boolean;

  public readonly allZonesValue: number;

  public readonly zoneID: number;

  public readonly maxWidth: number;

  public readonly maxHeight: number;

  public readonly maxDepth: number;

  public readonly maxWeight: number;

  public readonly enable: boolean;

  public readonly price: number;

  public readonly priceText: string;

  public readonly priceTTC: number;

  /**
   * Constructor for class CarrierData
   * @param carrierToCreate {CarrierCreator} Could be used to force the value of some members
   */
  constructor(carrierToCreate: CarrierCreator = {}) {
    /** @type {number} ID of the carrier */
    this.id = carrierToCreate.id || 0;

    /** @type {number} Position of the carrier */
    this.position = carrierToCreate.position || 0;

    /** @type {string} Name of the carrier */
    this.name = carrierToCreate.name || faker.company.name();

    /** @type {string} Transit name of the carrier */
    this.transitName = carrierToCreate.transitName || faker.company.name();

    /** @type {string} Delay of the carrier */
    this.delay = carrierToCreate.delay || '';

    /** @type {number} Shipping delay, 0 for longest and 9 for shortest */
    this.speedGrade = carrierToCreate.speedGrade || faker.number.int({min: 1, max: 9});

    /** @type {string} Url of carrier tracking */
    this.trakingURL = carrierToCreate.trakingURL || 'https://example.com/track.php?num=20';

    /** @type {boolean} True to include handling costs on the price */
    this.handlingCosts = carrierToCreate.handlingCosts === undefined ? true : carrierToCreate.handlingCosts;

    /** @type {boolean} True to make shipping free */
    this.freeShipping = carrierToCreate.freeShipping === undefined ? true : carrierToCreate.freeShipping;

    /** @type {string} Billing method of the carrier */
    this.billing = carrierToCreate.billing || faker.helpers.arrayElement(billing);

    /** @type {string} Tax rule of the carrier */
    this.taxRule = carrierToCreate.taxRule || faker.helpers.arrayElement(taxes);

    /** @type {string} Behavior when no defined range matches the customer carts */
    this.outOfRangeBehavior = carrierToCreate.outOfRangeBehavior || faker.helpers.arrayElement(outOfRangeBehavior);

    /** @type {number} Superior range for the carrier */
    this.rangeSup = carrierToCreate.rangeSup || faker.number.int({min: 1, max: 100});

    /** @type {boolean} True to apply it to all zones */
    this.allZones = carrierToCreate.allZones === undefined ? true : carrierToCreate.allZones;

    /** @type {number} Value to set when all zones is checked */
    this.allZonesValue = carrierToCreate.allZonesValue || faker.number.int({min: 1, max: 100});

    /** @type {number} ID of the zone on carrier form */
    this.zoneID = carrierToCreate.zoneID || faker.helpers.arrayElement(zonesID);

    /** @type {number} Max width that the carrier can handle */
    this.maxWidth = carrierToCreate.maxWidth || faker.number.int({min: 1, max: 100});

    /** @type {number} Max height that the carrier can handle */
    this.maxHeight = carrierToCreate.maxHeight || faker.number.int({min: 1, max: 100});

    /** @type {number} Max depth that the carrier can handle */
    this.maxDepth = carrierToCreate.maxDepth || faker.number.int({min: 1, max: 100});

    /** @type {number} Max weight that the carrier can handle */
    this.maxWeight = carrierToCreate.maxWeight || faker.number.int({min: 1, max: 100});

    /** @type {boolean} Status of the carrier */
    this.enable = carrierToCreate.enable === undefined ? true : carrierToCreate.enable;

    /** @type {number} Price HT */
    this.price = carrierToCreate.price || 0;

    /** @type {string} */
    this.priceText = carrierToCreate.priceText || this.price.toString();

    /** @type {number} Price TTC */
    this.priceTTC = carrierToCreate.priceTTC || 0;
  }
}
