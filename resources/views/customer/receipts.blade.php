<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt History - LUXE Store</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #f5f1e8 0%, #efe6d9 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 600;
        }

        .back-link {
            background: #d4a574;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
        }

        .back-link:hover {
            background: #c09660;
        }

        .receipts-grid {
            display: grid;
            gap: 20px;
        }

        .receipt-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .receipt-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0d5c7;
        }

        .receipt-id {
            color: #d4a574;
            font-weight: 600;
            font-size: 16px;
        }

        .receipt-date {
            color: #999;
            font-size: 14px;
        }

        .receipt-total {
            text-align: right;
        }

        .total-label {
            color: #999;
            font-size: 12px;
        }

        .total-amount {
            color: #1a1a1a;
            font-size: 24px;
            font-weight: 700;
        }

        .receipt-items {
            margin-bottom: 20px;
        }

        .receipt-items h4 {
            color: #1a1a1a;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666;
            font-size: 13px;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
        }

        .item-qty {
            text-align: center;
            margin: 0 15px;
            color: #999;
        }

        .item-price {
            text-align: right;
            color: #1a1a1a;
            font-weight: 600;
        }

        .receipt-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-view {
            background: #d4a574;
            color: white;
        }

        .btn-view:hover {
            background: #c09660;
        }

        .btn-download {
            background: #f0f0f0;
            color: #1a1a1a;
        }

        .btn-download:hover {
            background: #e0e0e0;
        }

        .empty-state {
            background: white;
            padding: 80px 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-text {
            color: #999;
            margin-bottom: 10px;
        }

        .empty-subtext {
            color: #ccc;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>📋 Receipt History</h1>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="back-link">← Back to Dashboard</a>
        </div>

        @php
            $customerBills = [];
            foreach($bills as $bill) {
                if(isset($bill->user_id) && $bill->user_id == auth()->id()) {
                    $customerBills[] = $bill;
                }
            }
        @endphp

        @if(count($customerBills) > 0)
            <div class="receipts-grid">
                @foreach($customerBills as $bill)
                    <div class="receipt-card">
                        <div class="receipt-header">
                            <div>
                                <div class="receipt-id">Bill #{{ $bill->id }}</div>
                                <div class="receipt-date">{{ $bill->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="receipt-total">
                                <div class="total-label">Total Amount</div>
                                <div class="total-amount">₹{{ number_format($bill->total_amount, 2) }}</div>
                            </div>
                        </div>

                        @if($bill->items->count() > 0)
                            <div class="receipt-items">
                                <h4>Items Purchased</h4>
                                @foreach($bill->items as $item)
                                    <div class="item-row">
                                        <div class="item-name">{{ $item->product->product_name ?? 'Product' }}</div>
                                        <div class="item-qty">x{{ $item->quantity }}</div>
                                        <div class="item-price">₹{{ number_format($item->price * $item->quantity, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="receipt-actions">
                            <button class="btn btn-view" onclick="printReceipt({{ $bill->id }})">Print</button>
                            <button class="btn btn-download" onclick="downloadReceipt({{ $bill->id }})">Download</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <div class="empty-text">No receipts yet</div>
                <div class="empty-subtext">Your purchase history will appear here</div>
            </div>
        @endif
    </div>

    <script>
        function printReceipt(billId) {
            window.print();
        }

        function downloadReceipt(billId) {
            alert('Download feature will be available soon!');
        }
    </script>
</body>
</html>