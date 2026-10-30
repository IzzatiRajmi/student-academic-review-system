/* Student Academic Review System — small UI behaviours (no framework) */
(function () {
  'use strict';

  var lastFocus = null;

  function openModal(modal) {
    if (!modal) return;
    lastFocus = document.activeElement;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    var focusable = modal.querySelector('button, a, input, textarea');
    if (focusable) focusable.focus();
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
    if (lastFocus) lastFocus.focus();
  }

  function setText(modal, key, value) {
    var el = modal.querySelector('[data-v="' + key + '"]');
    if (el) el.textContent = value || '-';
  }

  var STATUS_LABEL = { pending: 'Pending', approved: 'Approved', rejected: 'Rejected' };

  document.addEventListener('click', function (e) {
    var t = e.target;

    // Open a modal by id
    var opener = t.closest('[data-open-modal]');
    if (opener) {
      openModal(document.getElementById(opener.getAttribute('data-open-modal')));
      return;
    }

    // Close buttons
    if (t.closest('[data-close-modal]')) {
      closeModal(t.closest('.modal'));
      return;
    }

    // Click on the dark backdrop closes the modal
    if (t.classList && t.classList.contains('modal')) {
      closeModal(t);
      return;
    }

    // View request modal
    var viewBtn = t.closest('[data-view]');
    if (viewBtn) {
      var data;
      try { data = JSON.parse(viewBtn.getAttribute('data-view')); } catch (err) { return; }
      var m = document.getElementById('view-modal');
      ['student_name', 'subject_code', 'justification', 'submitted', 'comment'].forEach(function (k) {
        setText(m, k, data[k]);
      });
      var decided = data.status === 'pending' ? 'Not decided yet' : data.decided;
      setText(m, 'decided', decided);
      var st = m.querySelector('[data-v="status"]');
      st.innerHTML = '';
      var b = document.createElement('span');
      b.className = 'badge badge-' + data.status;
      b.textContent = STATUS_LABEL[data.status] || data.status;
      st.appendChild(b);
      openModal(m);
      return;
    }

    // Delete confirmation
    var delBtn = t.closest('button[data-delete-id]');
    if (delBtn) {
      var dm = document.getElementById('delete-modal');
      dm.querySelector('[data-delete-target]').value = delBtn.getAttribute('data-delete-id');
      var url = delBtn.getAttribute('data-delete-url');
      if (url) dm.querySelector('form').setAttribute('action', url);
      dm.querySelector('[data-delete-label]').textContent = delBtn.getAttribute('data-delete-subject');
      openModal(dm);
      return;
    }

    // Dismiss flash alert
    if (t.closest('[data-dismiss]')) {
      t.closest('.alert').remove();
      return;
    }

    // Mobile menu
    if (t.closest('[data-menu-toggle]')) {
      var open = document.body.classList.toggle('menu-open');
      t.closest('[data-menu-toggle]').setAttribute('aria-expanded', open ? 'true' : 'false');
      return;
    }
    if (t.closest('[data-menu-close]')) {
      document.body.classList.remove('menu-open');
    }

    // Show / hide password
    var pw = t.closest('[data-pw-toggle]');
    if (pw) {
      var input = pw.parentNode.querySelector('input');
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      pw.setAttribute('aria-pressed', show ? 'true' : 'false');
      pw.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    }
  });

  // Esc closes the open modal / menu
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var open = document.querySelector('.modal:not([hidden])');
    if (open) closeModal(open);
    document.body.classList.remove('menu-open');
  });

  // Loading state on submit (prevents double submit)
  document.querySelectorAll('form[data-loading-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('[data-loading-text]');
      if (!btn) return;
      if (btn.getAttribute('aria-busy') === 'true') return;
      btn.setAttribute('aria-busy', 'true');
      btn.textContent = btn.getAttribute('data-loading-text');
      setTimeout(function () { btn.disabled = true; }, 0);
    });
  });

  // Uppercase subject code as you type
  document.querySelectorAll('[data-uppercase]').forEach(function (el) {
    el.addEventListener('input', function () {
      var pos = el.selectionStart;
      el.value = el.value.toUpperCase().replace(/\s+/g, '');
      try { el.setSelectionRange(pos, pos); } catch (err) { /* ignore */ }
    });
  });

  // Character counter
  document.querySelectorAll('[data-counter]').forEach(function (el) {
    var out = document.getElementById(el.getAttribute('data-counter'));
    var max = el.getAttribute('maxlength') || 1000;
    function update() { out.textContent = el.value.length + ' / ' + max; }
    el.addEventListener('input', update);
    update();
  });
})();
