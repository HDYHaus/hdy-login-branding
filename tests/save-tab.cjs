const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../assets/admin.js'), 'utf8');
const handler = source.match(/\$form\.on\('submit', function \(\) \{([\s\S]*?)\n  \}\);/);
assert.ok(handler, 'The settings form registers a submit handler');

for (const tab of ['shared', 'login', 'register', 'recovery']) {
  for (const fragment of ['', '#hdylb-panel-shared', '#hdylb-panel-login']) {
    const base = '/wp-admin/options-general.php?page=hdy-login-branding&settings-updated=true';
    let referer = base + fragment;
    const context = {
      activeTab: tab,
      submitting: false,
      $form: {
        find(selector) {
          assert.equal(selector, '[name="_wp_http_referer"]');
          return { val(value) { if (value === undefined) return referer; referer = value; } };
        }
      }
    };
    vm.runInNewContext(handler[1], context);
    assert.equal(referer, base + '#hdylb-panel-' + tab);
    assert.equal(context.submitting, true);
    // Initialization selects the panel from the fragment after WordPress redirects.
    assert.equal(new URL(referer, 'https://example.test').hash.replace('#hdylb-panel-', ''), tab);
  }
}
console.log('PASS: all four tabs survive the save return URL; existing fragments are replaced and query parameters retained.');
