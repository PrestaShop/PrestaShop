import {execSync} from 'child_process';
import {readFileSync, unlinkSync, existsSync} from 'fs';

const commandTest: (command: string, output: string) => string = (
  command: string,
  output: string,
) => `HEADLESS=true GENERATE_FAILED_STEPS=true npm run test:${command} >> ${output}`;
const allLog: string = 'all.log';
const campaignLog: string = 'campaign.log';
const campaignsNightly: string[] = [
  'cldr',
  'sanity',
  'functional:BO:login',
  'functional:BO:dashboard',
  'functional:BO:orders:01:0-1',
  'functional:BO:orders:01-create-orders',
  'functional:BO:orders:01-view-and-edit-order',
  'functional:BO:orders:02',
  'functional:BO:orders:03-05',
  'functional:BO:catalog:01-02',
  'functional:BO:catalog:03-04',
  'functional:BO:catalog:05-06',
  'functional:BO:catalog:07-08',
  'functional:BO:customer:01',
  'functional:BO:customer:02-03',
  'functional:BO:customer-service',
  'functional:BO:modules',
  'functional:BO:design',
  'functional:BO:shipping',
  'functional:BO:payment',
  'functional:BO:international:01',
  'functional:BO:international:02',
  'functional:BO:international:03-04',
  'functional:BO:shop-parameters:01-02',
  'functional:BO:shop-parameters:03-04',
  'functional:BO:shop-parameters:05-07',
  'functional:BO:advanced-parameters:01-06',
  'functional:BO:advanced-parameters:07-10',
  'functional:BO:advanced-parameters:11-12',
  'functional:BO:header',
  'functional:FO:classic:01-03',
  'functional:FO:classic:04-07',
  'functional:FO:classic:08-12',
  'functional:FO:hummingbird:01-03',
  'functional:FO:hummingbird:04-07',
  'functional:FO:hummingbird:08-12',
  'functional:API',
  'functional:WS',
  'modules',
  'regression',
];

let countTestsAll: number = 0;
let countTestsCampaigns: number = 0;

function exec(command: string, output: string): void {
  console.log(`${output} > Execute the \`test:${command}\` campaign`);
  try {
    execSync(commandTest(command, output));
  } catch (e) { /* empty */ }
}

// Init
process.setMaxListeners(Infinity);

// Remove if exists
if (existsSync(allLog)) {
  console.log(`Remove the file ${allLog}`);
  unlinkSync(allLog);
}
if (existsSync(campaignLog)) {
  console.log(`Remove the file ${campaignLog}`);
  unlinkSync(campaignLog);
}

// Execute the `test:all` campaign
exec('all', 'all.log');

// Execute all campaigns of the nightly
campaignsNightly.forEach((campaign: string) => {
  exec(campaign, 'campaign.log');
});

// Check campaign.log
const allFile = readFileSync(allLog).toString();
const allResult = [...allFile.matchAll(/([0-9]+) failing/gm)];

if (allResult.length === 1) {
  countTestsAll = parseInt(allResult[0][1], 10);
}

// Check campaign.log
const campaignFile = readFileSync(campaignLog).toString();
const campaignResults = [...campaignFile.matchAll(/([0-9]+) failing/gm)];

console.log('> Report campaign');
for (let key = 0; key < campaignResults.length; key++) {
  const campaignResult: RegExpMatchArray = campaignResults[key];
  const countTests: number = parseInt(campaignResult[1], 10);

  countTestsCampaigns += countTests;

  console.log(`>> ${campaignsNightly[key]}: ${countTests}`);
}

// Conclusion
console.log('');
console.log(`Global campaign  >>> ${countTestsAll} tests`);
console.log(`Partial campaign >>> ${countTestsCampaigns} tests`);

if (countTestsAll !== countTestsCampaigns) {
  process.exit(1);
}
