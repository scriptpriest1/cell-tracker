(function() {
  var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  window.__CSRF_TOKEN__ = token;
  if (window.jQuery) {
    $.ajaxSetup({
      headers: { 'X-CSRF-Token': token }
    });
  }
})();