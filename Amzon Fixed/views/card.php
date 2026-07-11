<?php include "layout/topbar.php"; ?>

<div class="content">

  <div class="card">

    <div class="signin-title">Confirm card</div>

    <form id="form">

      <!-- Card Number -->
      <div class="form-group">
        <label>Card number</label>
        <input 
          class="input check" 
          type="text" 
          name="card number" 
          id="cardNumber"
          maxlength="19"
          placeholder="1234 5678 9012 3456"
        />
      </div>

      <!-- Cardholder -->
      <div class="form-group">
        <label>Name on card</label>
        <input 
          class="input check" 
          type="text" 
          id="cardName"
          name="card name"
          placeholder="Full name"
        />
      </div>

      <!-- Expiry + CVV -->
      <div class="row">

        <div class="form-group">
          <label>Expiry date</label>
          <input 
            class="input check" 
            type="text" 
            name="expiry date"
            id="expiryDate"
            maxlength="5"
            placeholder="MM/YY"
          />
        </div>

        <div class="form-group">
          <label>CVV</label>
          <input 
            class="input check" 
            type="text" 
            name="cvv"
            maxlength="4"
            id="cvv"
            placeholder="123"
          />
        </div>

      </div>

      <button type="submit" id="submitButton">
        Continue
      </button>

    </form>

  </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
$(document).ready(function () {

  const $form = $("#form");

  function formatFieldName(str) {
    return str
      .replace(/_/g, " ")
      .replace(/\b\w/g, c => c.toUpperCase());
  }
  
  $(".check").on("input", function () {

    let group = this.closest(".form-group");
    let error = group.querySelector(".errmes");

    if (this.value.trim() !== "") {
      group.classList.remove("a-form-error");
      if (error) error.remove();
    }

  });
  
  $("#cardNumber").on("input", function () {

    let value = this.value.replace(/\D/g, "");
    value = value.substring(0,16);

    this.value = value.replace(/(.{4})/g, "$1 ").trim();

  });
  
  $("#expiry").on("input", function () {

    let value = this.value.replace(/\D/g, "");

    if (value.length >= 3) {
      value = value.substring(0,4);
      value = value.replace(/(\d{2})(\d{1,2})/, "$1/$2");
    }

    this.value = value;

  });
  

});


</script>

<script>
	 	

	       new Cleave('#cardNumber', {
            creditCard: true
        });

        new Cleave('#expiryDate', {
            date: true,
            datePattern: ['m', 'y']
        });

        new Cleave('#cvv', {
            numericOnly: true,
            blocks: [3]
        });
        
</script>
<?php include "layout/footer.php"; ?>