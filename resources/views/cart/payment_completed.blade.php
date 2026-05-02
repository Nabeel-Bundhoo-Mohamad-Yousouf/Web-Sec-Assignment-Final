<!DOCTYPE html>
<html>
<head>
    <title>Payment Completed</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        .success-container { 
            max-width: 500px; 
            margin: 0 auto; 
            padding: 40px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { color: #4CAF50; }
        .checkmark { font-size: 80px; color: #4CAF50; margin-bottom: 20px; }
        .order-details { text-align: left; margin: 30px 0; padding: 20px; background: white; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 12px 30px; border: none; cursor: pointer; margin-top: 20px; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>

<div class="success-container">
    <div class="checkmark">✓</div>
    <h2>Payment Completed Successfully!</h2>
    <p>Thank you for your purchase. Your order has been placed.</p>
    
    <div class="order-details">
        <p><strong>Order Number:</strong> {{ $orderNumber ?? '#' . rand(10000, 99999) }}</p>
        <p><strong>Total Amount:</strong> Rs {{ number_format($total ?? 0, 2) }}</p>
        <p><strong>Payment Status:</strong> <span style="color: green;">Paid</span></p>
        <p><strong>Estimated Delivery:</strong> 3-5 business days</p>
    </div>
    
    <p>A confirmation email has been sent to your email address.</p>
    
    <a href="{{ url('/') }}">
        <button>Return to Shop</button>
    </a>
</div>

</body>
</html>