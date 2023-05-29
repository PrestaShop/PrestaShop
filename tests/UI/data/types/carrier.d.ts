type CarrierCreator = {
  id?: number
  position?: number
  name?: string
  transitName?: string
  delay?: string
  speedGrade?: number
  trakingURL?: string
  handlingCosts?: boolean
  freeShipping?: boolean
  billing?: string
  taxRule?: string
  outOfRangeBehavior?: string
  rangeSup?: number
  allZones?: boolean
  allZonesValue?: number
  zoneID?: number
  maxWidth?: number
  maxHeight?: number
  maxDepth?: number
  maxWeight?: number
  enable?: boolean
  price?: number
  priceText?: string
  priceTTC?: number
};

export default CarrierCreator;
