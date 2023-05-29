import OrderShippingCreator from '@data/types/orderShipping';

/**
 * @class
 */
export default class OrderShippingData {
  public readonly trackingNumber: string;

  public readonly carrier: string;

  public readonly carrierID: number;

  /**
   * Constructor for class OrderShippingData
   * @param valueToCreate {OrderShippingCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: OrderShippingCreator) {
    this.trackingNumber = valueToCreate.trackingNumber;
    this.carrier = valueToCreate.carrier;
    this.carrierID = valueToCreate.carrierID;
  }
}
