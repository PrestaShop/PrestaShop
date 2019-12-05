module.exports = class Tax {
  constructor(taxRulesGroupToCreate = {}) {
    this.name = taxRulesGroupToCreate.name || 'FR tax Rule';
    this.enabled = taxRulesGroupToCreate.enabled === undefined ? true : taxRulesGroupToCreate.enabled;
  }
};
