const fs = require('fs');

// Get mochawesome report
const jsonFile = fs.readFileSync('./mochawesome-report/mochawesome.json');

/**
 * Count doubles for each context
 * @param contexts
 * @return {*}, key = context, value =
 */
const count = contexts => contexts.reduce(
  (a, b) => ({
    ...a,
    [b]: (a[b] || 0) + 1,
  }),
  {},
);

/**
 * Get only doubles and delete common tests (loginBO and logoutBO)
 * @param dict
 * @return {string[]}
 */
const duplicates = dict => Object.keys(dict)
  .filter(
    key => dict[key] > 1 && key !== 'loginBO' && key !== 'logoutBO',
  );

/**
 * Check for doubles in mochawesome report
 * @param jsonFile
 */
const checkDoubles = (jsonFile) => {
  // Parse json report
  const jsonReport = JSON.parse(jsonFile);

  // Map all tests contexts
  const reportContexts = Object.values(jsonReport.allTests).map(test => JSON.parse(test.context).value);

  const contextDoubles = duplicates(count(reportContexts));

  if (contextDoubles.length !== 0) {
    throw new Error(`Some test identifiers must be fixed:\n${contextDoubles}`);
  } else {
    console.log('All good, no changes are required');
  }
};

// Run file
checkDoubles(jsonFile);
