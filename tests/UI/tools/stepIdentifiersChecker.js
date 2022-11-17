const fs = require('fs');

// Get mochawesome report
const reportPath = process.env.REPORT_PATH || './mochawesome-report/mochawesome.json';
const jsonFile = fs.readFileSync(reportPath);

/**
 * Count doubles for each context
 * @param contexts
 * @return {*}, key = context, value =
 */
const count = (contexts) => contexts.reduce(
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
const duplicates = (dict) => Object.keys(dict)
  .filter(
    (key) => dict[key] > 1 && ['loginBO', 'logoutBO', 'loginFO', 'logoutFO'].indexOf(key) === -1,
  );

/**
 * Get all tests from json file
 * @param jsonFile
 * @returns {[]}
 */
const getAllTests = (jsonFile) => {
  // Parse json report
  const jsonReport = JSON.parse(jsonFile);
  const parentSuites = jsonReport.results[0].suites;

  let allTests = [];

  // eslint-disable-next-line no-restricted-syntax
  for (const suite of parentSuites) {
    allTests = allTests.concat(getTestsFromSuite(suite));
  }

  return allTests;
};

/**
 * Get tests from suite and nested suites
 * @param suite
 * @returns {*[]}
 */
const getTestsFromSuite = (suite) => {
  const {file} = suite;
  let tests = suite.tests || [];
  tests.forEach((test) => {
    const testReturned = test;
    testReturned.file = file;

    return testReturned;
  });

  const nestedSuites = suite.suites;

  // eslint-disable-next-line no-restricted-syntax
  for (const nestedSuite of nestedSuites) {
    tests = tests.concat(getTestsFromSuite(nestedSuite));
  }

  return tests;
};

/**
 * Check for undefined context
 * @param jsonFile
 */
const checkUndefined = (jsonFile) => {
  const allTests = getAllTests(jsonFile);
  const undefinedContextsSteps = allTests
    .filter((test) => (test.context === undefined || !test.context) && !test.pending)
    .map((test) => test.fullTitle.trim());

  if (undefinedContextsSteps.length !== 0) {
    console.error(
      `Some steps (${undefinedContextsSteps.length}) are missing contexts on these scenarios: \n - ${
        undefinedContextsSteps.join('\n - ')}`,
    );
    return false;
  }
  return true;
};

/**
 * Check for doubles in mochawesome report
 * @param jsonFile
 */
const checkDoubles = (jsonFile) => {
  const allTests = getAllTests(jsonFile);

  const reportContexts = Object.values(allTests)
    .filter((test) => test.context)
    .map((test) => JSON.parse(test.context).value);

  const contextDoubles = duplicates(count(reportContexts));

  if (contextDoubles.length !== 0) {
    console.error(`Some test identifiers (${contextDoubles.length}) must be fixed:\n - ${
      contextDoubles.join('\n - ')}`);
    return false;
  }
  return true;
};

const checkBaseContext = (jsonFile) => {
  const allTests = getAllTests(jsonFile);

  const reportBaseContext = [];
  const reportContexts = Object.values(allTests)
    .filter((test) => test.context)
    .map((test) => {
      const value = JSON.parse(test.context).value.split('_');
      // Extract the base context from the context
      const baseContext = value.slice(0, value.length - 1).join('_');
      const {file} = test;

      if (!baseContext || reportBaseContext.includes(file)) {
        return false;
      }

      reportBaseContext.push(file);
      return {file, baseContext};
    })
    .filter((test) => test !== false);

  const baseContextErrors = [];
  reportContexts.forEach((context) => {
    const contextFile = context.file.split('/');
    // Try to rebuild the baseContext from the filename
    const baseContextFile = contextFile
      .slice(2, contextFile.length)
      .map((part) => part
        .replace(/[0-9]{2}_/, '')
        .replace('.js', '')
        .replace('.ts', ''))
      .join('_');

    if (!context.baseContext.startsWith(baseContextFile)) {
      baseContextErrors.push(
        `File : ${context.file} - BaseContext : \`${context.baseContext}\` should start with \`${baseContextFile}\``,
      );
    }
  });

  if (baseContextErrors.length > 0) {
    console.error(
      `Some base contexts (${baseContextErrors.length}) must be fixed :\n - ${baseContextErrors.join('\n - ')}`,
    );
    return false;
  }
  return true;
};

// Run file
if (!checkUndefined(jsonFile)) {
  process.exit(1);
}

if (!checkDoubles(jsonFile)) {
  process.exit(1);
}

if (!checkBaseContext(jsonFile)) {
  process.exit(1);
}
