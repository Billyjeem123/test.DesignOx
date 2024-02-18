<!-- resources/views/welcome.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
<h1>Welcome to My Website</h1>

<form method="POST" action="{{ route('postJobPayment') }}">
    @csrf
    <label for="invoice_id">Invoice ID:</label>
    <input type="text" id="invoice_id" name="invoice_id" required value="8767876567656">
    <br>
    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" required value="9000">
    <br>
    <button type="submit">Pay Now!</button>
</form>
</body>
</html>
