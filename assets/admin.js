jQuery(function ($) {
  var frame;
  var $select = $('#hdy-login-branding-select');
  var $remove = $('#hdy-login-branding-remove');
  var $id = $('#hdy-login-branding-id');
  var $preview = $('#hdy-login-branding-preview');
  var $previewWrap = $('.hdy-login-branding-preview');
  var $colorPicker = $('.hdy-login-branding-color');
  var $form = $('#hdylb-form');
  var $tabs = $('.hdylb-tabs a');
  var $panels = $('.hdylb-panel');
  var $livePreview = $('.hdylb-preview');
  var activeTab = 'shared';
  var dirty = false;
  var submitting = false;
  var ready = false;
  var pendingPreview;
  var keys = {
    login: { button: 'hdylb_button_text' },
    register: { message: 'hdylb_registration_heading', button: 'hdylb_registration_button_text' },
    lostpassword: { message: 'hdylb_lost_password_message', button: 'hdylb_lost_password_button_text' },
    resetpass: { message: 'hdylb_reset_password_message', button: 'hdylb_reset_password_button_text' }
  };

  function value(name) {
    return name ? String($form.find('[name="' + name + '"]').val() || '').trim() : '';
  }

  function color(hex) {
    if (!/^#([a-f0-9]{3}|[a-f0-9]{6})$/i.test(hex)) { return ''; }
    if (hex.length === 4) {
      return '#' + hex.slice(1).split('').map(function (c) { return c + c; }).join('');
    }
    return hex;
  }

  function luminance(hex) {
    var rgb = hex.slice(1).match(/.{2}/g).map(function (v) {
      var channel = parseInt(v, 16) / 255;
      return channel <= 0.04045 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4);
    });
    return rgb[0] * 0.2126 + rgb[1] * 0.7152 + rgb[2] * 0.0722;
  }

  function renderPreview() {
    var flow = $('#hdylb-preview-flow').val();
    var inherited = activeTab === 'shared' || $form.find('[name="hdylb_' + flow + '_shared"]:checkbox').prop('checked');
    var defaults = { background_color: '#f0f0f1', button_color: '#2271b1', button_text_color: '#ffffff' };
    var invalid = false;
    var colors = {};
    Object.keys(defaults).forEach(function (key) {
      var shared = value('hdylb_' + key);
      var custom = inherited ? '' : value('hdylb_' + flow + '_' + key);
      if ((shared && !color(shared)) || (custom && !color(custom))) { invalid = true; }
      colors[key] = color(custom) || color(shared) || defaults[key];
    });
    var copy = hdyLoginBranding.defaults[flow];
    var message = value(keys[flow].message) || copy.message;
    var customLogo = $form.find('[name="hdylb_enabled"]:checkbox').prop('checked');
    var logo = customLogo ? ($preview.attr('src') || $livePreview.attr('data-theme-logo')) : '';
    $('.hdylb-preview-logo').attr('src', logo || $livePreview.attr('data-default-logo'));
    $('.hdylb-preview-stage').css('background-color', colors.background_color);
    $('.hdylb-preview-message').text(message).prop('hidden', !message);
    $('.hdylb-preview-button').text(value(keys[flow].button) || copy.button).css({
      backgroundColor: colors.button_color, color: colors.button_text_color
    });
    $('.hdylb-preview-label').text(flow === 'resetpass' ? hdyLoginBranding.newPassword : hdyLoginBranding.username);
    $('.hdylb-preview-second').prop('hidden', flow !== 'login' && flow !== 'register');
    $('.hdylb-preview-second span').text(flow === 'register' ? hdyLoginBranding.email : hdyLoginBranding.password);
    var a = luminance(colors.button_color);
    var b = luminance(colors.button_text_color);
    var ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
    $('#hdylb-contrast').text(invalid ? hdyLoginBranding.contrastInvalid :
      ratio.toFixed(2) + ':1. ' + (ratio >= 4.5 ? hdyLoginBranding.contrastPass : hdyLoginBranding.contrastFail))
      .toggleClass('hdylb-warning', invalid || ratio < 4.5);
  }

  function changed() {
    if (!ready) { return; }
    dirty = true;
    $('#hdylb-save-state').text(hdyLoginBranding.unsaved);
    clearTimeout(pendingPreview);
    pendingPreview = setTimeout(renderPreview, 100);
  }

  if ($.fn.wpColorPicker && $colorPicker.length) {
    $colorPicker.wpColorPicker({ change: changed, clear: changed });
  }

  $('.hdylb-tabs').attr('role', 'tablist');
  $tabs.attr('role', 'tab');
  $panels.attr({ role: 'tabpanel', tabindex: '0' });
  $livePreview.prop('hidden', false);

  function activate(key, focus) {
    if (!document.getElementById('hdylb-panel-' + key)) { key = 'shared'; }
    activeTab = key;
    $tabs.each(function () {
      var selected = this.id === 'hdylb-tab-' + key;
      $(this).attr({ 'aria-selected': String(selected), 'aria-controls': this.hash.slice(1), tabindex: selected ? '0' : '-1' });
      if (selected && focus) { this.focus(); }
    });
    $panels.each(function () { this.hidden = this.id !== 'hdylb-panel-' + key; });
    var options = $('#hdylb-preview-flow option');
    options.prop('disabled', false);
    if (key === 'recovery') {
      options.filter('[value="login"],[value="register"]').prop('disabled', true);
      $('#hdylb-preview-flow').val('lostpassword');
    } else if (key !== 'shared') {
      $('#hdylb-preview-flow').val(key);
      options.not('[value="' + key + '"]').prop('disabled', true);
    }
    renderPreview();
  }
  $tabs.on('click', function (event) {
    event.preventDefault();
    var key = this.id.replace('hdylb-tab-', '');
    activate(key, true);
    history.replaceState(null, '', '#hdylb-panel-' + key);
  }).on('keydown', function (event) {
    var index = $tabs.index(this);
    var rtl = document.documentElement.dir === 'rtl';
    if (event.key === 'ArrowRight') { index += rtl ? -1 : 1; }
    else if (event.key === 'ArrowLeft') { index += rtl ? 1 : -1; }
    else if (event.key === 'Home') { index = 0; }
    else if (event.key === 'End') { index = $tabs.length - 1; }
    else { return; }
    event.preventDefault();
    $tabs.eq((index + $tabs.length) % $tabs.length).trigger('click');
  });

  function inherit() {
    $('.hdylb-inherit').each(function () {
      var target = document.getElementById(this.getAttribute('aria-controls'));
      target.hidden = this.checked;
      $(this).attr('aria-expanded', String(!this.checked));
    });
  }
  inherit();
  $('.hdylb-inherit').on('change', inherit);
  $form.on('input change', 'input, textarea', changed);
  $('#hdylb-preview-flow').on('change', renderPreview);
  $('[data-preview-flow]').on('focusin', function () {
    $('#hdylb-preview-flow').val($(this).attr('data-preview-flow'));
    renderPreview();
  });
  $form.on('submit', function () {
    // WordPress uses this field for its post-save redirect, not the current hash.
    var $referer = $form.find('[name="_wp_http_referer"]');
    $referer.val(String($referer.val() || '').split('#')[0] + '#hdylb-panel-' + activeTab);
    submitting = true;
  });
  window.addEventListener('beforeunload', function (event) {
    if (dirty && !submitting) { event.preventDefault(); event.returnValue = ''; }
  });
  activate(location.hash.replace('#hdylb-panel-', ''), false);
  ready = true;

  $select.on('click', function (event) {
    event.preventDefault();

    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: hdyLoginBranding.title,
      button: { text: hdyLoginBranding.button },
      library: { type: 'image' },
      multiple: false
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      $id.val(attachment.id);
      $preview.attr('src', attachment.url);
      $previewWrap.removeClass('is-empty').addClass('is-set');
      $remove.prop('disabled', false);
      changed();
    });

    frame.open();
  });

  $remove.on('click', function (event) {
    event.preventDefault();
    $id.val('');
    $preview.attr('src', '');
    $previewWrap.removeClass('is-set').addClass('is-empty');
    $remove.prop('disabled', true);
    changed();
  });
});
