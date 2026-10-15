(function () {
  function post(url, body) {
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      credentials: "same-origin",
      body: JSON.stringify(body || {}),
    }).then(function (response) {
      return response
        .json()
        .catch(function () {
          return {};
        })
        .then(function (data) {
          if (!response.ok) {
            var error = new Error(
              (data && data.message) || "Request failed"
            );
            error.status = response.status;
            error.data = data;
            throw error;
          }
          return data;
        });
    });
  }

  function getErrorMessage(error) {
    if (error && error.status === 429) {
      return "Too many attempts. Try again in a few minutes.";
    }
    return "Something went wrong. Please try again.";
  }

  window.AuthApi = { post: post, getErrorMessage: getErrorMessage };
})();
