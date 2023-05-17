import fs from 'fs';

type JsonReportTest = {
  context?: string,
  file: string,
  pending: boolean,
  fullTitle: string,
}
type JsonReportSuite = {
  file: string,
  tests: Array<JsonReportTest>,
  suites: Array<JsonReportSuite>,
}
type JsonReportResult = {
  suites?: Array<JsonReportSuite>,
}
type JsonReport = {
  results: Array<JsonReportResult>,
}

// Get mochawesome report
const reportPath: string = process.env.REPORT_PATH || './mochawesome-report/mochawesome.json';
const jsonFile: string = fs.readFileSync(reportPath).toString();

/**
 * Get all tests from json file
 * @param jsonFile
 * @returns {[]}
 */
const getAllTests = (jsonFile: string): JsonReportTest[] => {
  // Parse json report
  const jsonReport: JsonReport = JSON.parse(jsonFile);
  const parentSuites = jsonReport.results[0].suites;

  let allTests: JsonReportTest[] = [];

  if (parentSuites) {
    parentSuites.forEach((suite: JsonReportSuite): void => {
      allTests = allTests.concat(getTestsFromSuite(suite));
    });
  }

  return allTests;
};

/**
 * Get tests from suite and nested suites
 * @param suite
 * @returns {JsonReportTest[]}
 */
const getTestsFromSuite = (suite: JsonReportSuite): JsonReportTest[] => {
  const {file} = suite;
  let tests = suite.tests || [];
  tests.forEach((test: JsonReportTest) => {
    const testReturned = test;
    testReturned.file = file;

    return testReturned;
  });

  const nestedSuites: JsonReportSuite[] = suite.suites;

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
const checkUndefined = (jsonFile: string): boolean => {
  const allTests = getAllTests(jsonFile);
  const undefinedContextsSteps: string[] = [];
  allTests.forEach((value: JsonReportTest): void => {
    if ((value.context === undefined || !value.context) && !value.pending) {
      undefinedContextsSteps.push(value.fullTitle.trim());
    }
  });

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
 * @param {string} jsonFile
 * @returns {boolean}
 */
const checkDoubles = (jsonFile: string): boolean => {
  const allTests: JsonReportTest[] = getAllTests(jsonFile);

  const reportContexts: string[] = Object.values(allTests)
    .reduce((result: string[], test: JsonReportTest) => {
      if (test.context) {
        result.push(JSON.parse(test.context ?? '').value);
      }
      return result;
    }, []);

  const contextExisting: string[] = [];
  const contextDoubles: string[] = [];
  reportContexts.forEach((value: string): void => {
    if (['loginBO', 'logoutBO', 'loginFO', 'logoutFO'].indexOf(value) !== -1) {
      return;
    }
    if (contextExisting.indexOf(value) !== -1) {
      contextDoubles.push(value);
      return;
    }
    contextExisting.push(value);
  });

  if (contextDoubles.length !== 0) {
    console.error(`Some test identifiers (${contextDoubles.length}) must be fixed (in double):\n - ${
      contextDoubles.join('\n - ')}`);
    return false;
  }
  return true;
};

const checkBaseContext = (jsonFile: string): boolean => {
  type ReportContext = {
    file: string
    baseContext: string
  };
  const reportBaseContext: string[] = [];
  const reportContexts: ReportContext[] = getAllTests(jsonFile)
    .reduce((result: ReportContext[], test: JsonReportTest) => {
      if (typeof test.context === 'string') {
        const value: string[] = JSON.parse(test.context).value.split('_');
        // Extract the base context from the context
        const baseContext: string = value.slice(0, value.length - 1).join('_');
        const {file} = test;

        if (baseContext && !reportBaseContext.includes(file)) {
          reportBaseContext.push(file);
          result.push({file, baseContext});
        }
      }
      return result;
    }, []);

  const baseContextErrors: string[] = [];
  reportContexts.forEach((context: ReportContext): void => {
    const contextFile = context.file.split('/');
    // Try to rebuild the baseContext from the filename
    const baseContextFile = contextFile
      .slice(2, contextFile.length)
      .map((part: string) => part
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
