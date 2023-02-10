import type {
  ImportAddress,
  ImportBrand,
  ImportCategory,
  ImportCombination,
  ImportCreator,
  ImportCustomer,
  ImportHeaderItem,
  ImportProduct,
} from '@data/types/import';

/**
 * Create new address to use in customer address form on BO and FO
 * @class
 */
export default class ImportData {
  public readonly entity: string;

  public readonly header: ImportHeaderItem[];

  public readonly records: ImportAddress[]|ImportBrand[]|ImportCategory[]|ImportCombination[]|ImportCustomer[]|ImportProduct[];

  /**
   * Constructor for class ImportData
   * @param valueToCreate {ImportCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: ImportCreator) {
    this.entity = valueToCreate.entity;
    this.header = valueToCreate.header;
    this.records = valueToCreate.records;
  }
}
