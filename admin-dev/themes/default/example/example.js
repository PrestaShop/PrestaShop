// Extend the HTMLElement class to create the web component
class IncludeHTML extends HTMLElement {
  /**
   * Get and render external HTML
   * @param  {String} path The path to the external HTML
   */
  async getHTML(path) {
    // Get the page
    const request = await fetch(path);

    if (!request.ok) return;

    // Get the HTML
    this.innerHTML = await request.text();
  }

  /**
   * The class constructor object
   */
  constructor() {
    // Always call super first in constructor
    super();

    // Get the source HTML to load
    const path = this.getAttribute('path');

    if (!path) return;

    // Render HTML
    this.getHTML(path);
  }
}

// Define the new web component
if ('customElements' in window) {
  customElements.define('include-html', IncludeHTML);
}

// New / Old theme switch
function switchStylesheet() {
  // Get the link element with the ID 'defaultStylesheet'
  const defaultStylesheet = document.getElementById('default_stylesheet');
  const switchButton = document.getElementById('switch_btn_styles');

  switchButton.classList.toggle('active');

  // Check if the default stylesheet is currently 'default.css'
  if (
    defaultStylesheet.getAttribute('href') === 'http://127.0.0.1:1010/theme.css'
  ) {
    // If so, change it to 'alternate.css'
    defaultStylesheet.setAttribute(
      'href',
      'http://127.0.0.1:1000/default-old.css',
    );
  } else {
    // If not, change it back to 'default.css'
    defaultStylesheet.setAttribute('href', 'http://127.0.0.1:1010/theme.css');
  }
}

// Style toggle
document.getElementById('switch_btn_styles').addEventListener('click', switchStylesheet);

// Function to create summary based on H1 elements
function createSummary() {
  const headings = document.querySelectorAll('h1.custom');
  const summaryList = document.createElement('ul');

  headings.forEach((heading) => {
    const sectionId = heading.textContent.toLowerCase().replace(/\s+/g, '-');
    heading.id = sectionId;

    const anchor = document.createElement('a');
    anchor.textContent = heading.textContent;
    anchor.href = `#${sectionId}`;
    anchor.setAttribute('style', 'display: block;');

    // Add event listener to each anchor link
    anchor.addEventListener('click', () => {
      // Remove active class from all other links
      document.querySelectorAll('#summary ul a').forEach((link) => {
        link.classList.remove('active');
      });

      // Add active class to clicked link
      anchor.classList.add('active');
    });

    const listItem = document.createElement('li');
    listItem.appendChild(anchor);

    summaryList.appendChild(listItem);
  });

  // Remove previous summary if exists
  const summaryContainer = document.getElementById('summary');
  summaryContainer.innerHTML = '';

  // Append the summary list to the summary container
  summaryContainer.appendChild(summaryList);
}

// Create summary initially
createSummary();

// Create a new observer instance linked to the callback function
const observer = new MutationObserver((mutationsList) => {
  mutationsList.forEach((mutation) => {
    if (mutation.type === 'childList' && mutation.addedNodes.length) {
      // If new nodes are added, recreate summary
      createSummary();
    }
  });

  // Init Tooltips
  $(() => {
    $('[data-toggle="tooltip"]').tooltip();
  });

  // Init Popovers
  $(() => {
    $('[data-toggle="popover"]').popover();
  });
});

// Start observing the document body for changes in children
observer.observe(document.body, {childList: true});

// Timeout to init growl element
window.setTimeout(() => {
  const triggerGrowl = document.getElementById('show_growl');
  const triggerGrowlMessageOnly = document.getElementById('show_growl_message');

  triggerGrowl.addEventListener('click', () => {
    $.growl({title: 'Growl', message: 'The kitten is awake!'});
    $.growl.error({message: 'The kitten is attacking!'});
    $.growl.notice({message: 'The kitten is cute!'});
    $.growl.warning({message: 'The kitten is ugly!'});
  });

  triggerGrowlMessageOnly.addEventListener('click', () => {
    $.growl({title: '', message: 'The kitten is awake!'});
    $.growl.error({title: '', message: 'The kitten is attacking!'});
    $.growl.notice({title: '', message: 'The kitten is cute!'});
    $.growl.warning({title: '', message: 'The kitten is ugly!'});
  });
}, 2000);
