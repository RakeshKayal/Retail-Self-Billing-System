<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body style="margin:0;padding:0;background:#040b1b;color:#e2e8f0;font-family:'Segoe UI',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:720px;margin:0 auto;padding:32px;">
        <tr>
            <td style="background:#111827;border-radius:24px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.25);">
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding:32px 32px 16px;">
                            <span style="display:inline-block;padding:10px 18px;background:#0f172a;border-radius:999px;font-size:13px;color:#22d3ee;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">Invoice</span>
                            <h1 style="margin:18px 0 6px;font-size:30px;color:#fff;">Thank you for your purchase!</h1>
                            <p style="margin:0;font-size:15px;color:#94a3b8;">Below is your order summary. A copy of this invoice has been sent to {{ $bill->user->email ?? 'your email' }}.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 24px;">
                            <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;">
                                <div style="min-width:180px;">
                                    <p style="margin:0;font-size:13px;color:#94a3b8;">Invoice #</p>
                                    <p style="margin:6px 0 0;font-size:16px;color:#fff;font-weight:700;">INV-{{ strtoupper(substr(sha1($bill->id . $bill->created_at), 0, 12)) }}</p>
                                </div>
                                <div style="min-width:180px;">
                                    <p style="margin:0;font-size:13px;color:#94a3b8;">Purchase Date</p>
                                    <p style="margin:6px 0 0;font-size:16px;color:#fff;font-weight:700;">{{ $bill->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                                <div style="min-width:180px;">
                                    <p style="margin:0;font-size:13px;color:#94a3b8;">Total Paid</p>
                                    <p style="margin:6px 0 0;font-size:16px;color:#22d3ee;font-weight:700;">₹{{ number_format($bill->total_amount, 2) }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#0f172a;border-radius:18px;overflow:hidden;">
                                <thead>
                                    <tr style="background:linear-gradient(90deg,#7c3aed,#06b6d4);color:#fff;">
                                        <th align="left" style="padding:16px;">Item</th>
                                        <th align="center" style="padding:16px;">Qty</th>
                                        <th align="right" style="padding:16px;">Price</th>
                                        <th align="right" style="padding:16px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bill->items as $item)
                                    <tr style="border-bottom:1px solid rgba(148,163,184,.12);">
                                        <td style="padding:16px 16px 16px 20px;font-size:15px;color:#e2e8f0;">
                                            {{ $item->product->product_name ?? 'Product #' . $item->product_id }}
                                        </td>
                                        <td align="center" style="padding:16px;font-size:15px;color:#94a3b8;">{{ $item->quantity }}</td>
                                        <td align="right" style="padding:16px;font-size:15px;color:#94a3b8;">₹{{ number_format($item->price, 2) }}</td>
                                        <td align="right" style="padding:16px 20px;font-size:15px;color:#fff;">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px;">
                            @php
                                $subtotal = $bill->items->sum(fn($item) => $item->price * $item->quantity);
                                $taxRate = 0.075;
                                $estimatedTax = round($subtotal * $taxRate, 2);
                                $calculatedTotal = round($subtotal + $estimatedTax, 2);
                                $taxAmount = $bill->total_amount !== $calculatedTotal
                                    ? round($bill->total_amount - $subtotal, 2)
                                    : $estimatedTax;
                            @endphp
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:12px 0;font-size:14px;color:#94a3b8;">Subtotal</td>
                                    <td align="right" style="padding:12px 0;font-size:14px;color:#fff;">₹{{ number_format($subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;font-size:14px;color:#94a3b8;">GST 7.5%</td>
                                    <td align="right" style="padding:12px 0;font-size:14px;color:#fff;">₹{{ number_format($taxAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;font-size:16px;color:#fff;font-weight:700;">Grand Total</td>
                                    <td align="right" style="padding:12px 0;font-size:16px;color:#22d3ee;font-weight:700;">₹{{ number_format($bill->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 32px;">
                            <p style="margin:0;font-size:14px;color:#94a3b8;line-height:1.7;">If you have any questions, reply to this email or contact support.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
