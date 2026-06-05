<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $data['bill_id'] }} — LUXE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #f7f4ef;
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 24px 16px 48px;
        }

        .receipt-card {
            background: #fff;
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
            overflow: hidden;
        }

        /* ── Header ── */
        .receipt-top {
            background: #0f0f0f;
            padding: 32px 28px 24px;
            text-align: center;
        }

        .brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: #c9aa71;
            letter-spacing: 0.18em;
        }

        .tagline {
            font-size: 0.68rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #5a5752;
            margin-top: 4px;
        }

        .receipt-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            font-size: 0.72rem;
            color: #9a9691;
        }

        /* ── Body ── */
        .receipt-body {
            padding: 24px 24px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items thead th {
            font-size: 0.66rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #aaa;
            padding: 0 4px 10px;
            border-bottom: 1px solid #f0ece4;
            text-align: left;
        }

        table.items thead th:last-child { text-align: right; }

        table.items tbody td {
            padding: 11px 4px;
            font-size: 0.88rem;
            color: #1a1a1a;
            border-bottom: 1px solid #f7f4ef;
            vertical-align: middle;
        }

        table.items tbody td:last-child { text-align: right; }

        .item-name { font-weight: 500; }

        .item-barcode {
            font-size: 0.68rem;
            color: #bbb;
            margin-top: 2px;
            letter-spacing: 0.04em;
        }

        /* ── Total ── */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            border-top: 2px dashed #f0ece4;
            margin-top: 4px;
        }

        .total-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            color: #333;
            letter-spacing: 0.06em;
        }

        .total-amount {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #c9aa71;
        }

        /* ── Footer ── */
        .receipt-footer {
            background: #faf8f4;
            border-top: 1px dashed #ece8e0;
            padding: 20px 24px;
            text-align: center;
        }

        .footer-text {
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #bbb;
        }

        .expiry-note {
            margin-top: 10px;
            font-size: 0.7rem;
            color: #ccc;
        }

        /* ── Download button ── */
        .btn-dl {
            display: block;
            width: calc(100% - 48px);
            margin: 0 24px 24px;
            padding: 14px;
            background: #c9aa71;
            color: #0f0f0f;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-dl:hover { opacity: 0.85; }
    </style>
</head>
<body>

<div class="receipt-card">

    {{-- Header --}}
    <div class="receipt-top">
        <div class="brand">LUXE</div>
        <div class="tagline">Thank you for your purchase</div>
        <div class="receipt-meta">
            <span>Bill #{{ $data['bill_id'] }}</span>
            <span>{{ $data['created_at'] }}</span>
        </div>
    </div>

    {{-- Items --}}
    <div class="receipt-body">
        <table class="items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['items'] as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item['name'] }}</div>
                        @if(!empty($item['barcode']))
                            <div class="item-barcode">{{ $item['barcode'] }}</div>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $item['quantity'] }}</td>
                    <td style="text-align:right;">₹{{ number_format($item['price'], 2) }}</td>
                    <td style="text-align:right;">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Total --}}
    <div class="total-row">
        <div class="total-label">Total Payable</div>
        <div class="total-amount">₹{{ number_format($data['total'], 2) }}</div>
    </div>

    {{-- Download PDF button --}}
    <a class="btn-dl" id="btnDownloadPdf">⬇ Download Receipt PDF</a>

    {{-- Footer --}}
    <div class="receipt-footer">
        <div class="footer-text">Visit us again ✦ luxe.store</div>
        <div class="expiry-note">This link expires 15 minutes after billing</div>
    </div>

</div>

{{-- PDF generation on mobile --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    document.getElementById('btnDownloadPdf').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const card = document.querySelector('.receipt-card');

        html2canvas(card, { scale: 2, useCORS: true }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 190;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            pdf.save(`LUXE_Bill_{{ $data['bill_id'] }}.pdf`);
        });
    });
</script>

</body>
</html>