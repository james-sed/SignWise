(function () {
  var DEFAULT_SUBTEXT = "Start learning sign language today.";
  var DEFAULT_HOME = "landing.html";

  var BRAND_MARKUP = [
    '<div class="brand-decor" aria-hidden="true">',
    '  <span class="decor decor-icon decor-icon--camera">',
    '    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">',
    '      <rect x="2.5" y="7.5" width="19" height="12" rx="2.5" />',
    '      <path d="M9 7.5l1-2h4l1 2" />',
    '      <circle cx="12" cy="13.5" r="3.4" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-icon decor-icon--chat">',
    '    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">',
    '      <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" />',
    '      <circle cx="8" cy="12" r="0.9" fill="currentColor" stroke="none" />',
    '      <circle cx="12" cy="12" r="0.9" fill="currentColor" stroke="none" />',
    '      <circle cx="16" cy="12" r="0.9" fill="currentColor" stroke="none" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-icon decor-icon--hand">',
    '    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">',
    '      <path d="M18 11V6.5a1.5 1.5 0 00-3 0V11" />',
    '      <path d="M15 11V4.5a1.5 1.5 0 00-3 0V11" />',
    '      <path d="M12 11V5a1.5 1.5 0 00-3 0v6" />',
    '      <path d="M9 11V7a1.5 1.5 0 00-3 0v8a6 6 0 006 6h1a6 6 0 006-6v-4" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-icon decor-icon--cap">',
    '    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">',
    '      <path d="M22 10L12 5 2 10l10 5 10-5z" />',
    '      <path d="M6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5" />',
    '      <path d="M22 10v6" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-spiral decor-spiral--yellow">',
    '    <svg viewBox="0 0 60 60">',
    '      <path d="M30 30a3 3 0 11-3 3 6 6 0 109-4 10 10 0 11-14 12 15 15 0 1022-8" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-spiral decor-spiral--teal">',
    '    <svg viewBox="0 0 60 60">',
    '      <path d="M30 30a3 3 0 11-3 3 6 6 0 109-4 10 10 0 11-14 12 15 15 0 1022-8" />',
    "    </svg>",
    "  </span>",
    '  <span class="decor decor-dot decor-dot--coral-tl"></span>',
    '  <span class="decor decor-dot decor-dot--coral-tr"></span>',
    '  <span class="decor decor-dot decor-dot--white-tc"></span>',
    '  <span class="decor decor-dot decor-dot--white-br"></span>',
    '  <span class="decor decor-dot decor-dot--yellow-l"></span>',
    '  <span class="decor decor-dot decor-dot--yellow-bl"></span>',
    '  <span class="decor decor-arcs">',
    '    <svg viewBox="0 0 520 320">',
    '      <path d="M20 320a240 240 0 01480 0" />',
    '      <path d="M80 320a180 180 0 01360 0" />',
    '      <path d="M140 320a120 120 0 01240 0" />',
    '      <path d="M200 320a60 60 0 01120 0" />',
    "    </svg>",
    "  </span>",
    "</div>",
    '<a class="auth-logo" href="__HOME__" aria-label="SignWise home">',
    '  <span class="auth-logo__mark" aria-hidden="true">',
    '    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">',
    '      <path d="M9 11.5V5.5a1.5 1.5 0 013 0v5M12 10.5V4a1.5 1.5 0 013 0v6.5M15 10.5V6a1.5 1.5 0 013 0v8c0 3.5-2 7-6 7s-6-2.5-7-5l-1.8-3.6a1.4 1.4 0 012.4-1.4L7 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />',
    "    </svg>",
    "  </span>",
    '  <span class="auth-logo__text">SignWise</span>',
    "</a>",
    '<div class="brand-content">',
    '  <h1 class="brand-headline">',
    "    Learn sign<br />",
    "    language,<br />",
    "    one sign at<br />",
    '    <span class="brand-headline__accent">',
    "      a time.",
    '      <svg class="brand-underline" viewBox="0 0 200 14" preserveAspectRatio="none" aria-hidden="true">',
    '        <path d="M3 11C46 3 154 3 197 9" />',
    "      </svg>",
    "    </span>",
    "  </h1>",
    '  <p class="brand-subtext">__SUBTEXT__</p>',
    "</div>",
  ].join("\n");

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function renderBrandPanel(panel) {
    var subtext = panel.getAttribute("data-subtext") || DEFAULT_SUBTEXT;
    var home = panel.getAttribute("data-home") || DEFAULT_HOME;

    panel.innerHTML = BRAND_MARKUP
      .replace(/__HOME__/g, escapeHtml(home))
      .replace(/__SUBTEXT__/g, escapeHtml(subtext));
  }

  function init(root) {
    (root || document)
      .querySelectorAll("[data-auth-brand]")
      .forEach(renderBrandPanel);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      init();
    });
  } else {
    init();
  }

  window.AuthLayout = { init: init, defaultSubtext: DEFAULT_SUBTEXT };
})();
