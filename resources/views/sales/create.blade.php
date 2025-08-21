@extends('layouts.app')

@section('styles')
<style>
    .product-item {
        cursor: pointer;
        transition: all 0.3s;
    }

    .product-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #selected-products {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('New Sale') }}</span>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to Sales') }}
                    </a>
                </div>

                <div class="card-body">
                    <form id="sale-form" method="POST" action="{{ route('sales.store') }}">
                        @csrf

                        <div class="row">
                            <!-- Left Column - Product Selection -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <input type="text" id="product-search" class="form-control"
                                        placeholder="Search products...">
                                </div>

                                <div class="row" id="product-list">
                                    @foreach($products as $product)
                                    <div class="col-md-4 mb-3">
                                        <div class="card product-item" data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}" data-price="{{ $product->price }}"
                                            data-stock="{{ $product->stock_quantity }}">
                                            <div class="card-body text-center">
                                                @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" class="img-fluid mb-2"
                                                    style="height: 100px; object-fit: contain;">
                                                @else
                                                <div class="bg-light d-flex align-items-center justify-content-center mb-2"
                                                    style="height: 100px;">
                                                    <i class="fas fa-box fa-3x text-secondary"></i>
                                                </div>
                                                @endif
                                                <h6>{{ $product->name }}</h6>
                                                <p class="mb-0">{{ number_format($product->price, 2) }}</p>
                                                <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Right Column - Sale Information -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        {{ __('Sale Details') }}
                                    </div>
                                    <div class="card-body">
                                        <div id="selected-products">
                                            <div class="alert alert-info" id="no-products-message">
                                                {{ __('No products selected.') }}
                                            </div>
                                            <div id="product-rows">
                                                <!-- Selected products will be added here -->
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="mb-3">
                                            <label for="subtotal" class="form-label">{{ __('Subtotal') }}</label>
                                            <input type="text" class="form-control" id="subtotal" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="discount" class="form-label">{{ __('Discount') }}</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="discount"
                                                name="discount" value="0">
                                        </div>

                                        <div class="mb-3">
                                            <label for="tax" class="form-label">{{ __('Tax') }}</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="tax"
                                                name="tax" value="0">
                                        </div>

                                        <div class="mb-3">
                                            <label for="total_amount" class="form-label">{{ __('Total Amount')
                                                }}</label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="total_amount" name="total_amount" readonly required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="paid_amount" class="form-label">{{ __('Paid Amount') }}</label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="paid_amount" name="paid_amount" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="change_amount" class="form-label">{{ __('Change') }}</label>
                                            <input type="text" class="form-control" id="change_amount" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">{{ __('Payment Method')
                                                }}</label>
                                            <select class="form-select" id="payment_method" name="payment_method"
                                                required>
                                                <option value="cash">{{ __('Cash') }}</option>
                                                <option value="card">{{ __('Card') }}</option>
                                                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_status" class="form-label">{{ __('Payment Status')
                                                }}</label>
                                            <select class="form-select" id="payment_status" name="payment_status"
                                                required>
                                                <option value="paid">{{ __('Paid') }}</option>
                                                <option value="pending">{{ __('Pending') }}</option>
                                                <option value="partial">{{ __('Partial') }}</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="note" class="form-label">{{ __('Note') }}</label>
                                            <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success" id="complete-sale-btn"
                                                disabled>
                                                <i class="fas fa-check-circle"></i> {{ __('Complete Sale') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productList = document.getElementById('product-list');
        const productSearch = document.getElementById('product-search');
        const selectedProducts = document.getElementById('selected-products');
        const productRows = document.getElementById('product-rows');
        const noProductsMessage = document.getElementById('no-products-message');
        const subtotalEl = document.getElementById('subtotal');
        const discountEl = document.getElementById('discount');
        const taxEl = document.getElementById('tax');
        const totalAmountEl = document.getElementById('total_amount');
        const paidAmountEl = document.getElementById('paid_amount');
        const changeAmountEl = document.getElementById('change_amount');
        const completeSaleBtn = document.getElementById('complete-sale-btn');
        const saleForm = document.getElementById('sale-form');

        // Product search functionality
        productSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productItems = productList.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const productName = item.getAttribute('data-name').toLowerCase();
                if (productName.includes(searchTerm)) {
                    item.closest('.col-md-4').style.display = 'block';
                } else {
                    item.closest('.col-md-4').style.display = 'none';
                }
            });
        });

        // Add product to cart
        productList.addEventListener('click', function(e) {
            const productItem = e.target.closest('.product-item');
            if (!productItem) return;

            const productId = productItem.getAttribute('data-id');
            const productName = productItem.getAttribute('data-name');
            const productPrice = parseFloat(productItem.getAttribute('data-price'));
            const productStock = parseInt(productItem.getAttribute('data-stock'));

            // Check if product already exists in the cart
            const existingProduct = document.querySelector(`#product-row-${productId}`);

            if (existingProduct) {
                // Increment quantity if product exists
                const quantityInput = existingProduct.querySelector('.product-quantity');
                const currentQuantity = parseInt(quantityInput.value);

                if (currentQuantity < productStock) {
                    quantityInput.value = currentQuantity + 1;
                    updateProductSubtotal(existingProduct, productPrice, currentQuantity + 1);
                } else {
                    alert('Cannot add more of this product. Stock limit reached.');
                }
            } else {
                // Add new product row
                const productRow = document.createElement('div');
                productRow.id = `product-row-${productId}`;
                productRow.className = 'card mb-2 product-row';
                productRow.innerHTML = `
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">${productName}</h6>
                            <button type="button" class="btn-close remove-product" aria-label="Remove"></button>
                        </div>
                        <div class="row g-2">
                            <div class="col-5">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary decrease-qty" type="button">-</button>
                                    <input type="number" class="form-control text-center product-quantity" value="1" min="1" max="${productStock}" name="products[${productId}][quantity]" required>
                                    <button class="btn btn-outline-secondary increase-qty" type="button">+</button>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control product-price" value="${productPrice.toFixed(2)}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="form-label form-label-sm">Discount</label>
                                <input type="number" class="form-control form-control-sm product-discount" name="products[${productId}][discount]" value="0" min="0">
                                <input type="hidden" name="products[${productId}][id]" value="${productId}">
                            </div>
                            <div class="col-7">
                                <label class="form-label form-label-sm">Subtotal</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control product-subtotal" value="${productPrice.toFixed(2)}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                productRows.appendChild(productRow);

                // Hide the "no products" message
                noProductsMessage.style.display = 'none';
            }

            // Update totals
            updateTotals();
        });

        // Handle quantity changes and product removal
        selectedProducts.addEventListener('click', function(e) {
            const target = e.target;

            // Handle remove button
            if (target.classList.contains('remove-product')) {
                const productRow = target.closest('.product-row');
                productRow.remove();

                // Show "no products" message if cart is empty
                if (productRows.children.length === 0) {
                    noProductsMessage.style.display = 'block';
                    completeSaleBtn.disabled = true;
                }

                updateTotals();
                return;
            }

            // Handle decrease quantity button
            if (target.classList.contains('decrease-qty')) {
                const productRow = target.closest('.product-row');
                const quantityInput = productRow.querySelector('.product-quantity');
                const currentQuantity = parseInt(quantityInput.value);
                const productPrice = parseFloat(productRow.querySelector('.product-price').value);

                if (currentQuantity > 1) {
                    quantityInput.value = currentQuantity - 1;
                    updateProductSubtotal(productRow, productPrice, currentQuantity - 1);
                    updateTotals();
                }
                return;
            }

            // Handle increase quantity button
            if (target.classList.contains('increase-qty')) {
                const productRow = target.closest('.product-row');
                const quantityInput = productRow.querySelector('.product-quantity');
                const currentQuantity = parseInt(quantityInput.value);
                const maxQuantity = parseInt(quantityInput.getAttribute('max'));
                const productPrice = parseFloat(productRow.querySelector('.product-price').value);

                if (currentQuantity < maxQuantity) {
                    quantityInput.value = currentQuantity + 1;
                    updateProductSubtotal(productRow, productPrice, currentQuantity + 1);
                    updateTotals();
                } else {
                    alert('Cannot add more of this product. Stock limit reached.');
                }
                return;
            }
        });

        // Handle quantity input changes
        selectedProducts.addEventListener('change', function(e) {
            const target = e.target;

            // Handle quantity input changes
            if (target.classList.contains('product-quantity')) {
                const productRow = target.closest('.product-row');
                const quantityInput = target;
                const currentQuantity = parseInt(quantityInput.value);
                const maxQuantity = parseInt(quantityInput.getAttribute('max'));
                const productPrice = parseFloat(productRow.querySelector('.product-price').value);

                if (currentQuantity > maxQuantity) {
                    quantityInput.value = maxQuantity;
                    alert('Quantity adjusted to available stock.');
                } else if (currentQuantity < 1) {
                    quantityInput.value = 1;
                }

                updateProductSubtotal(productRow, productPrice, parseInt(quantityInput.value));
                updateTotals();
                return;
            }

            // Handle discount changes
            if (target.classList.contains('product-discount')) {
                updateTotals();
                return;
            }
        });

        // Update discount and tax
        discountEl.addEventListener('input', updateTotals);
        taxEl.addEventListener('input', updateTotals);

        // Update change amount when paid amount changes
        paidAmountEl.addEventListener('input', function() {
            const paidAmount = parseFloat(this.value) || 0;
            const totalAmount = parseFloat(totalAmountEl.value) || 0;
            const change = paidAmount - totalAmount;

            changeAmountEl.value = change >= 0 ? change.toFixed(2) : '0.00';
        });

        // Helper function to update product subtotal
        function updateProductSubtotal(productRow, price, quantity) {
            const subtotalInput = productRow.querySelector('.product-subtotal');
            const subtotal = price * quantity;
            subtotalInput.value = subtotal.toFixed(2);
        }

        // Helper function to update all totals
        function updateTotals() {
            // Calculate subtotal
            let subtotal = 0;
            const productRows = document.querySelectorAll('.product-row');

            productRows.forEach(row => {
                const productSubtotal = parseFloat(row.querySelector('.product-subtotal').value);
                const productDiscount = parseFloat(row.querySelector('.product-discount').value) || 0;
                subtotal += productSubtotal - productDiscount;
            });

            // Get discount and tax
            const discount = parseFloat(discountEl.value) || 0;
            const tax = parseFloat(taxEl.value) || 0;

            // Calculate total
            const total = subtotal - discount + tax;

            // Update display values
            subtotalEl.value = subtotal.toFixed(2);
            totalAmountEl.value = total.toFixed(2);

            // Calculate change
            const paidAmount = parseFloat(paidAmountEl.value) || 0;
            const change = paidAmount - total;
            changeAmountEl.value = change >= 0 ? change.toFixed(2) : '0.00';

            // Enable/disable complete sale button
            completeSaleBtn.disabled = productRows.length === 0 || total <= 0;
        }

        // Handle form submission
        saleForm.addEventListener('submit', function(e) {
            const productRows = document.querySelectorAll('.product-row');

            if (productRows.length === 0) {
                e.preventDefault();
                alert('Please add at least one product to the sale.');
                return;
            }

            const totalAmount = parseFloat(totalAmountEl.value) || 0;
            if (totalAmount <= 0) {
                e.preventDefault();
                alert('Total amount must be greater than zero.');
                return;
            }

            const paidAmount = parseFloat(paidAmountEl.value) || 0;
            const paymentStatus = document.getElementById('payment_status').value;

            if (paymentStatus === 'paid' && paidAmount < totalAmount) {
                e.preventDefault();
                alert('Paid amount must be equal to or greater than the total amount for a paid status.');
                return;
            }
        });
    });
</script>
@endsection
