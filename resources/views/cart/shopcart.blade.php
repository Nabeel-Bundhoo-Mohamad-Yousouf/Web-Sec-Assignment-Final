<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select { padding: 5px; margin: 5px; }
        button { padding: 8px 15px; cursor: pointer; }
        .btn-update { background-color: #4CAF50; color: white; border: none; }
        .btn-remove { background-color: #ff4444; color: white; border: none; }
        .btn-checkout { background-color: #008CBA; color: white; border: none; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f2f2f2; }
        .loading { display: none; color: blue; margin: 10px; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .cart-total { font-size: 18px; font-weight: bold; text-align: right; margin: 20px 0; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Shopping Cart</h2>

<div id="message" class="message" style="display: none;"></div>
<div id="loading" class="loading">Updating cart...</div>

@if(empty($items))
    <p>Your cart is empty.</p>
    <a href="{{ url('/') }}">Continue Shopping</a>
@else

<form id="cart-form" method="POST" action="{{ route('cart.update') }}">
    @csrf
    @method('PUT')
    
    <table id="cart-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Type</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr data-id="{{ $item->book_ID }}" id="row-{{ $item->book_ID }}">
                <td>{{ $item->title }}</td>
                <td>{{ $item->author }}</td>
                <td>Rs {{ number_format($item->price, 2) }}</td>
                <td>
                    <input type="number" name="qty[{{ $item->book_ID }}]" 
                           value="{{ $item->quantity }}" min="1" 
                           style="width: 70px;" class="qty-input">
                </td>
                <td>
                    <select name="type[{{ $item->book_ID }}]" class="type-select">
                        <option value="Buy" {{ $item->type == 'Buy' ? 'selected' : '' }}>Buy</option>
                        <option value="Rent" {{ $item->type == 'Rent' ? 'selected' : '' }}>Rent</option>
                    </select>
                </td>
                <td class="subtotal">Rs {{ number_format($item->subtotal, 2) }}</td>
                <td>
                    <button type="button" class="btn-remove" data-id="{{ $item->book_ID }}">Remove</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="cart-total">
        Total: <span id="cart-total">Rs {{ number_format($total, 2) }}</span>
    </div>
    
    <button type="submit" class="btn-update">Update Cart</button>
    <button type="button" id="clear-cart" class="btn-remove">Clear Cart</button>
    <a href="{{ route('checkout') }}">
        <button type="button" class="btn-checkout">Proceed to Checkout</button>
    </a>
</form>

@endif

<script>
$(document).ready(function() {
    
    // Function to show message
    function showMessage(msg, type) {
        var messageDiv = $('#message');
        messageDiv.removeClass('success error').addClass(type);
        messageDiv.html(msg).fadeIn();
        setTimeout(function() {
            messageDiv.fadeOut();
        }, 3000);
    }
    
    // Update single item via AJAX
    function updateCartItem(bookId, quantity, type) {
        $('#loading').show();
        
        $.ajax({
            url: '{{ route("cart.update") }}',
            type: 'PUT',
            data: {
                book_id: bookId,
                quantity: quantity,
                type: type,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                
                if (response.success) {
                    // Update subtotal for this row
                    var newSubtotal = response.items.find(item => item.book_ID == bookId);
                    if (newSubtotal) {
                        $('#row-' + bookId + ' .subtotal').text('Rs ' + newSubtotal.subtotal.toFixed(2));
                    } else {
                        $('#row-' + bookId).fadeOut(function() { $(this).remove(); });
                    }
                    
                    // Update total
                    $('#cart-total').text(response.total);
                    
                    showMessage('Cart updated successfully!', 'success');
                    
                    // Update cart count badge if exists
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                }
            },
            error: function() {
                $('#loading').hide();
                showMessage('Error updating cart', 'error');
            }
        });
    }
    
    // Handle quantity or type change
    $('.qty-input, .type-select').on('change', function() {
        var row = $(this).closest('tr');
        var bookId = row.data('id');
        var quantity = row.find('.qty-input').val();
        var type = row.find('.type-select').val();
        
        if (quantity > 0) {
            updateCartItem(bookId, quantity, type);
        }
    });
    
    // Remove item
    $('.btn-remove').click(function() {
        var bookId = $(this).data('id');
        updateCartItem(bookId, 0, 'Buy');
    });
    
    // Clear entire cart
    $('#clear-cart').click(function() {
        if (confirm('Are you sure you want to clear your entire cart?')) {
            $('#loading').show();
            
            $.ajax({
                url: '{{ route("cart.clear") }}',
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = '{{ route("cart.index") }}';
                }
            });
        }
    });
    
    // Form submit (bulk update)
    $('#cart-form').on('submit', function(e) {
        e.preventDefault();
        
        $('#loading').show();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                $('#loading').hide();
                showMessage('Error updating cart', 'error');
            }
        });
    });
});
</script>

</body>
</html>