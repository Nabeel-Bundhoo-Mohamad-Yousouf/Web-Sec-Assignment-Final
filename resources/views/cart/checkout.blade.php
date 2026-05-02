<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Payment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; padding: 20px; }
        .container { display: flex; gap: 30px; flex-wrap: wrap; }
        .cart-summary { flex: 1; background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .payment-form { flex: 1; background: #fff; padding: 20px; border-radius: 5px; border: 1px solid #ddd; }
        input, select { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #4CAF50; color: white; padding: 12px 30px; border: none; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background: #45a049; }
        .error { color: red; font-size: 14px; margin-top: -10px; margin-bottom: 10px; display: none; }
        .success { color: green; }
        .card-details { border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .total-row { font-weight: bold; font-size: 18px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Checkout & Payment</h2>

<div class="container">
    <!-- Cart Summary -->
    <div class="cart-summary">
        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr><th>Item</th><th>Qty</th><th>Type</th><th>Price</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->type }}</td>
                    <td>Rs {{ number_format($item->price, 2) }}</td>
                    <td>Rs {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total:</td>
                    <td>Rs {{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Payment Form -->
    <div class="payment-form">
        <h3>Payment Information</h3>
        
        <form id="payment-form" method="POST" action="{{ route('payment.process') }}">
            @csrf
            
            <label>Full Name *</label>
            <input type="text" name="card_name" id="card_name" required>
            
            <label>Email *</label>
            <input type="email" name="email" id="email" required>
            
            <label>Phone *</label>
            <input type="tel" name="phone" id="phone" required pattern="[0-9]{10}">
            
            <label>Shipping Address *</label>
            <textarea name="address" id="address" rows="3" required></textarea>
            
            <div class="card-details">
                <h4>Card Details</h4>
                
                <label>Card Number *</label>
                <input type="text" name="card_number" id="card_number" maxlength="16" placeholder="1234 5678 9012 3456">
                
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label>Expiry Date *</label>
                        <input type="text" name="expiry" id="expiry" placeholder="MM/YY">
                    </div>
                    <div style="flex: 1;">
                        <label>CVV *</label>
                        <input type="password" name="cvv" id="cvv" maxlength="4" placeholder="123">
                    </div>
                </div>
            </div>
            
            <div id="payment-error" class="error"></div>
            
            <button type="submit" id="pay-btn">Pay Rs {{ number_format($total, 2) }}</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#payment-form').on('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        var cardName = $('#card_name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var address = $('#address').val();
        var cardNumber = $('#card_number').val();
        var expiry = $('#expiry').val();
        var cvv = $('#cvv').val();
        
        if (!cardName || !email || !phone || !address) {
            $('#payment-error').text('Please fill all required fields').show();
            return false;
        }
        
        if (!cardNumber || cardNumber.length < 16) {
            $('#payment-error').text('Please enter valid card number').show();
            return false;
        }
        
        if (!expiry) {
            $('#payment-error').text('Please enter expiry date').show();
            return false;
        }
        
        if (!cvv || cvv.length < 3) {
            $('#payment-error').text('Please enter valid CVV').show();
            return false;
        }
        
        $('#payment-error').hide();
        $('#pay-btn').prop('disabled', true).text('Processing...');
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Redirect to payment completed page
                    window.location.href = response.redirect;
                } else {
                    $('#payment-error').text(response.message).show();
                    $('#pay-btn').prop('disabled', false).text('Pay Rs {{ number_format($total, 2) }}');
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Payment processing failed. Please try again.';
                $('#payment-error').text(errorMsg).show();
                $('#pay-btn').prop('disabled', false).text('Pay Rs {{ number_format($total, 2) }}');
            }
        });
    });
    
    // Format card number
    $('#card_number').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        $(this).val(value);
    });
    
    // Format expiry
    $('#expiry').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0,2) + '/' + value.slice(2);
        }
        if (value.length > 5) value = value.slice(0,5);
        $(this).val(value);
    });
    
    // Format CVV
    $('#cvv').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 4) value = value.slice(0,4);
        $(this).val(value);
    });
});
</script>

</body>
</html>