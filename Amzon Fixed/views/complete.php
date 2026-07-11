<?php $_SESSION['current_step'] = 1; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Redirecting</title>
<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f4f6f8;
        font-family: Arial, sans-serif;
    }

    .container {
        text-align: center;
    }

    .circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #00453e;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .checkmark {
        font-size: 60px;
        color: white;
        font-weight: bold;
        line-height: 1;
    }

    .text {
        font-size: 16px;
        color: #333;
    }
</style>
</head>
<body>

<div class="container">
    <div class="circle">
        <div class="checkmark">✓</div>
    </div>
    <div class="text">Redirecting... do not reload page</div>
</div>

</body>
<script>
	setTimeout(() => window.location.href = "https://amazon.com", 3000);
	</script>
</html> 