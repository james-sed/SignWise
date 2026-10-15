(function () {
  var RESET_MIN = 8;
  var RESET_MAX = 128;

  var token = null;
  try {
    var params = new URLSearchParams(window.location.search);
    token = params.get("token");
  } catch (e) {
    token = null;
  }

  // Remove the token from the URL so it doesn't stay in the address bar or
  // leak through Referer. Keep it in component state only.
  if (window.history && window.history.replaceState) {
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  var views = {
    loading: document.getElementById("view-loading"),
    form: document.getElementById("view-form"),
    expired: document.getElementById("view-expired"),
    success: document.getElementById("view-success"),
  };

  var form = document.getElementById("reset-form");
  var passwordInput = document.getElementById("reset-password");
  var confirmInput = document.getElementById("reset-confirm");
  var submitButton = form.querySelector(".auth-submit");
  var formError = document.getElementById("reset-form-error");

  var validateError = document.getElementById("validate-error");
  var retryButton = document.getElementById("retry-button");

  var expiredTitle = document.getElementById("expired-title");
  var successTitle = document.getElementById("success-title");

  function showView(name) {
    Object.keys(views).forEach(function (key) {
      if (!views[key]) {
        return;
      }
      views[key].hidden = key !== name;
    });
  }

  function errorElementFor(input) {
    var id = input.getAttribute("aria-describedby");
    return id ? document.getElementById(id) : null;
  }

  function setFieldError(input, message) {
    var errorEl = errorElementFor(input);
    input.setAttribute("aria-invalid", message ? "true" : "false");
    if (errorEl) {
      errorEl.textContent = message || "";
      errorEl.hidden = !message;
    }
  }

  function validatePassword() {
    var value = passwordInput.value;
    var message = "";

    if (value.length < RESET_MIN) {
      message = "Password must be at least " + RESET_MIN + " characters.";
    } else if (value.length > RESET_MAX) {
      message = "Password must be no more than " + RESET_MAX + " characters.";
    }

    setFieldError(passwordInput, message);
    return !message;
  }

  function validateConfirm() {
    var message = confirmInput.value === passwordInput.value ? "" : "Passwords do not match.";
    setFieldError(confirmInput, message);
    return !message;
  }

  function setFormError(message) {
    formError.textContent = message || "";
    formError.hidden = !message;
  }

  function setLoading(button, loading) {
    button.disabled = loading;
    button.classList.toggle("is-loading", loading);
  }

  function showExpired() {
    showView("expired");
    expiredTitle.focus();
  }

  function showSuccess() {
    passwordInput.value = "";
    confirmInput.value = "";
    setFieldError(passwordInput, "");
    setFieldError(confirmInput, "");
    setFormError("");
    showView("success");
    successTitle.focus();
  }

  function checkToken() {
    validateError.hidden = true;
    retryButton.hidden = true;
    showView("loading");

    if (!token) {
      showExpired();
      return;
    }

    window.AuthApi.post("/reset-password/validate", { token: token })
      .then(function (data) {
        if (data && data.valid) {
          showView("form");
          passwordInput.focus();
        } else {
          showExpired();
        }
      })
      .catch(function () {
        validateError.textContent = "Something went wrong. Please try again.";
        validateError.hidden = false;
        retryButton.hidden = false;
        showView("loading");
      });
  }

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

  passwordInput.addEventListener("blur", validatePassword);
  confirmInput.addEventListener("blur", validateConfirm);

  passwordInput.addEventListener("input", function () {
    if (passwordInput.getAttribute("aria-invalid") === "true") {
      validatePassword();
    }
    if (confirmInput.value !== "") {
      validateConfirm();
    }
  });

  confirmInput.addEventListener("input", function () {
    if (confirmInput.getAttribute("aria-invalid") === "true") {
      validateConfirm();
    }
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    setFormError("");

    var okPassword = validatePassword();
    var okConfirm = validateConfirm();

    if (!okPassword || !okConfirm) {
      (okPassword ? confirmInput : passwordInput).focus();
      return;
    }

    setLoading(submitButton, true);

    window.AuthApi.post("/reset-password", {
      token: token,
      password: passwordInput.value,
      password_confirmation: confirmInput.value,
    })
      .then(function () {
        setLoading(submitButton, false);
        showSuccess();
      })
      .catch(function (error) {
        setLoading(submitButton, false);

        if (error && error.status === 422 && error.data) {
          if (error.data.reason === "token" || (error.data.errors && error.data.errors.token)) {
            showExpired();
            return;
          }
          var errors = error.data.errors || {};
          if (errors.password && errors.password.length) {
            setFieldError(passwordInput, errors.password[0]);
          }
          if (errors.password_confirmation && errors.password_confirmation.length) {
            setFieldError(confirmInput, errors.password_confirmation[0]);
          }
          if (!errors.password && !errors.password_confirmation) {
            setFormError(error.data.message || "This link has expired or the data is invalid.");
          }
          return;
        }

        setFormError(window.AuthApi.getErrorMessage(error));
      });
  });

  retryButton.addEventListener("click", checkToken);

  checkToken();
})();
