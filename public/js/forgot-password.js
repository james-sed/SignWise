(function () {
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var RESEND_SECONDS = 60;

  var form = document.getElementById("forgot-form");
  var emailInput = document.getElementById("forgot-email");
  var submitButton = form.querySelector(".auth-submit");
  var formError = document.getElementById("forgot-form-error");

  var requestView = document.getElementById("view-request");
  var sentView = document.getElementById("view-sent");
  var sentEmail = document.getElementById("sent-email");
  var sentTitle = document.getElementById("sent-title");
  var resendButton = document.getElementById("resend-button");
  var resendLabel = document.getElementById("resend-label");
  var resendError = document.getElementById("resend-error");

  var resendTimer = null;

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

  function validateEmail() {
    var value = emailInput.value.trim();
    var message = "";

    if (!value) {
      message = "Please enter your email address.";
    } else if (!emailPattern.test(value)) {
      message = "Enter a valid email address.";
    }

    setFieldError(emailInput, message);
    return !message;
  }

  function setFormError(element, message) {
    element.textContent = message || "";
    element.hidden = !message;
  }

  function setLoading(button, loading) {
    button.disabled = loading;
    button.classList.toggle("is-loading", loading);
  }

  function getErrorMessage(error) {
    return window.AuthApi.getErrorMessage(error);
  }

  // The endpoint always answers with the same success payload, whether or not
  // an account exists for the address, so the UI cannot be used to enumerate
  // accounts.
  function requestResetLink(email) {
    return window.AuthApi.post("/forgot-password", { email: email });
  }

  function startResendCountdown() {
    var remaining = RESEND_SECONDS;
    window.clearInterval(resendTimer);
    resendButton.disabled = true;
    resendLabel.textContent = "Resend email (" + remaining + "s)";

    resendTimer = window.setInterval(function () {
      remaining -= 1;
      if (remaining <= 0) {
        window.clearInterval(resendTimer);
        resendButton.disabled = false;
        resendLabel.textContent = "Resend email";
        return;
      }
      resendLabel.textContent = "Resend email (" + remaining + "s)";
    }, 1000);
  }

  function showSuccess(email) {
    sentEmail.textContent = email;
    requestView.hidden = true;
    sentView.hidden = false;
    sentTitle.focus();
    startResendCountdown();
  }

  emailInput.addEventListener("blur", validateEmail);
  emailInput.addEventListener("input", function () {
    if (emailInput.getAttribute("aria-invalid") === "true") {
      validateEmail();
    }
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    setFormError(formError, "");

    if (!validateEmail()) {
      emailInput.focus();
      return;
    }

    var email = emailInput.value.trim();
    setLoading(submitButton, true);

    requestResetLink(email)
      .then(function () {
        setLoading(submitButton, false);
        // Always show the same result, whether or not the email exists.
        showSuccess(email);
      })
      .catch(function (error) {
        setLoading(submitButton, false);
        setFormError(formError, getErrorMessage(error));
      });
  });

  resendButton.addEventListener("click", function () {
    setFormError(resendError, "");
    setLoading(resendButton, true);

    requestResetLink(sentEmail.textContent)
      .then(function () {
        setLoading(resendButton, false);
        startResendCountdown();
      })
      .catch(function (error) {
        setLoading(resendButton, false);
        setFormError(resendError, getErrorMessage(error));
      });
  });
})();
