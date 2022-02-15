const fs = require('fs');

// Get mochawesome report
const reportPath = process.env.REPORT_PATH || './mochawesome-report/mochawesome.json';
const jsonFile = fs.readFileSync(reportPath);

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
    key => dict[key] > 1 && ['loginBO', 'logoutBO', 'loginFO', 'logoutFO'].indexOf(key) === -1,
  );

/**
 * Get all tests from json file
 * @param jsonFile
 * @returns {[]}
 */
const getAllTests = (jsonFile) => {
  // Parse json report
  const jsonReport = JSON.parse(jsonFile);

  // Map all tests contexts
  const testsInSuites = jsonReport.results[0].suites.map(suite => suite.tests);

  let allTests = [];
  for (let i = 0; i < testsInSuites.length - 1; i++) {
    allTests = allTests.concat(testsInSuites[i]);
  }

  return allTests;
};

/**
 * Check for undefined context
 * @param jsonFile
 */
const checkUndefined = (jsonFile) => {
  const allTests = getAllTests(jsonFile);
  const undefinedContextsSteps = allTests
    .filter(test => (test.context === undefined || !test.context) && !test.pending)
    .map(test => test.fullTitle);

  if (undefinedContextsSteps.length !== 0) {
    throw new Error(`Some steps are missing contexts on these scenarios: \n ${undefinedContextsSteps}`);
  }
};

/**
 * Check for doubles in mochawesome report
 * @param jsonFile
 */
const checkDoubles = (jsonFile) => {
  const allTests = getAllTests(jsonFile);

  const reportContexts = Object.values(allTests)
    .filter(test => test.context)
    .map(test => JSON.parse(test.context).value);

  const contextDoubles = duplicates(count(reportContexts));

  if (contextDoubles.length !== 0) {
    throw new Error(`Some test identifiers must be fixed:\n${contextDoubles}`);
  } else {
    console.log('All good, no changes are required');
  }
};

// Run file
checkUndefined(jsonFile);

checkDoubles(jsonFile);
