import {
  // Import data
  FakerCarrier,
} from '@prestashop-core/ui-testing';

export default {
  default: new FakerCarrier({
    id: 1,
    name: 'Click and collect',
    delay: 'Pick up in-store',
    enable: true,
    freeShipping: true,
    position: 1,
  }),
  myCarrier: new FakerCarrier({
    id: 2,
    name: 'My carrier',
    delay: 'Delivery next day!',
    priceTTC: 8.40,
    price: 7.00,
    enable: true,
    freeShipping: false,
    position: 2,
  }),
  cheapCarrier: new FakerCarrier({
    id: 3,
    name: 'My cheap carrier',
    delay: 'Buy more to pay less!',
    enable: false,
    freeShipping: false,
    position: 3,
  }),
  lightCarrier: new FakerCarrier({
    id: 4,
    name: 'My light carrier',
    delay: 'The lighter the cheaper!',
    enable: false,
    freeShipping: false,
    position: 4,
  }),
};
