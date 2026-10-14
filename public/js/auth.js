(function () {
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function errorElementFor(input) {
    var id = input.getAttribute("aria-describedby");
    return id ? document.getElementById(id) : null;
  }

  function setError(input, message) {
    var errorEl = errorElementFor(input);
    input.setAttribute("aria-invalid", message ? "true" : "false");
    if (errorEl) {
      errorEl.textContent = message || "";
      errorEl.hidden = !message;
    }
  }

  function validate(input) {
    var form = input.form;
    var type = input.dataset.validate;
    var value = input.value;
    var message = "";

    if (type === "name") {
      message = value.trim() ? "" : "Please enter your name.";
    } else if (type === "email") {
      var email = value.trim();
      if (!email) {
        message = "Please enter your email address.";
      } else if (!emailPattern.test(email)) {
        message = "Enter a valid email address.";
      }
    } else if (type === "password") {
      message = value.length >= 8 ? "" : "Password must be at least 8 characters.";
    } else if (type === "confirm") {
      var password = form.querySelector('[data-validate="password"]');
      message = value === password.value ? "" : "Passwords do not match.";
    }

    setError(input, message);
    return !message;
  }

  document.querySelectorAll("[data-validate]").forEach(function (input) {
    input.addEventListener("blur", function () {
      validate(input);
    });
    input.addEventListener("input", function () {
      if (input.getAttribute("aria-invalid") === "true") {
        validate(input);
      }
    });
  });

  document.querySelectorAll(".auth-form").forEach(function (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var valid = true;
      form.querySelectorAll("[data-validate]").forEach(function (input) {
        if (!validate(input)) {
          valid = false;
        }
      });

      if (!valid) {
        var firstInvalid = form.querySelector('[aria-invalid="true"]');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      var button = form.querySelector(".auth-submit");
      button.disabled = true;
      button.classList.add("is-loading");
      // TODO: replace with the real API call once the backend exists.
      window.setTimeout(function () {
        window.location.href = "lesson.html";
      }, 600);
    });
  });

  document.querySelectorAll(".field-toggle").forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      var input = document.getElementById(toggle.dataset.toggle);
      if (!input) return;
      var reveal = input.type === "password";
      input.type = reveal ? "text" : "password";
      toggle.textContent = reveal ? "Hide" : "Show";
      toggle.setAttribute("aria-pressed", String(reveal));
      toggle.setAttribute("aria-label", (reveal ? "Hide" : "Show") + " password");
    });
  });

  document.querySelectorAll("[data-view]").forEach(function (button) {
    button.addEventListener("click", function () {
      var target = button.dataset.view;
      document.getElementById("view-register").hidden = target !== "register";
      document.getElementById("view-login").hidden = target !== "login";
    });
  });
})();
