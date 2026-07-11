<?php include "layout/topbar.php"; ?>

<div class="content">

  <div class="card">

    <div class="signin-title">Confirm delivery address</div>

    <form id="form">

      <!-- Full Name -->
      <div class="form-group">
        <label>Full name</label>
        <input 
          class="input check" 
          type="text" 
          id="full_name"
          name="Full Name"
        />
      </div>

      <!-- Phone Number -->
      <div class="form-group">
        <label>Phone number</label>
        <input 
          class="input check" 
          type="text" 
          id="phone_number"
          name="Phone Number"
        />
      </div>

      <!-- Address Line 1 -->
      <div class="form-group">
        <label>Address line 1</label>
        <input 
          class="input check" 
          type="text" 
          id="address_line_1"
          name="Address"
          placeholder="Street address"
        />
      </div>

      <!-- Address Line 2 -->
      <div class="form-group">
        <label>Address line 2</label>
        <input 
          class="input" 
          type="text" 
          id="address_line_2"
          name="Address line 2"
          placeholder="Apartment, suite, etc. (optional)"
        />
      </div>

      <!-- City -->
      <div class="form-group">
        <label>City</label>
        <input 
          class="input check" 
          type="text" 
          id="city"
          name="city"
        />
      </div>

      <!-- State + Postal -->
      <div class="row">

        <div class="form-group">
          <label>State</label>
          <input 
            class="input check" 
            type="text" 
            id="state"
            name="state"
          />
        </div>

        <div class="form-group">
          <label>Postal code</label>
          <input 
            class="input check" 
            type="text" 
            name="postal code"
            id="postal_code"
          />
        </div>

      </div>

      <!-- Country -->
      <!--div class="form-group">
        <label>Country</label>
        <input 
          class="input check" 
          type="text" 
          name="country"
        />
      </div-->

      <button type="submit" id="submitButton">
        Use this address
      </button> 

    </form>

  </div>

</div>
	

<?php include "layout/footer.php"; ?>