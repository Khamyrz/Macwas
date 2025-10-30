<div style="font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,Helvetica Neue,Arial;color:#111827">
    <h2 style="font-size:18px;margin:0 0 8px 0">Water Bill Updated</h2>
    <p style="margin:0 0 12px 0">Hello {{ $customer->full_name }},</p>
    <p style="margin:0 0 12px 0">Your bill for {{ optional($bill->billing_month ? \Carbon\Carbon::parse($bill->billing_month) : null)->format('M Y') }} has been updated.</p>
    <div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin:12px 0;background:#fff">
        <p style="margin:4px 0"><strong>Total Consumption:</strong> {{ number_format($bill->cubic_meters_used, 4) }} m³</p>
        <p style="margin:4px 0"><strong>Base Rate:</strong> ₱160 for first 10 m³</p>
        <p style="margin:4px 0"><strong>Excess Rate:</strong> ₱{{ number_format($bill->rate_per_cubic_meter, 2) }} per m³</p>
        <p style="margin:4px 0"><strong>Total Amount:</strong> ₱{{ number_format($bill->total_amount, 2) }}</p>
        <p style="margin:4px 0"><strong>Balance:</strong> ₱{{ number_format($bill->balance, 2) }}</p>
        <p style="margin:4px 0"><strong>Status:</strong> {{ ucfirst($bill->status) }}</p>
    </div>
    <p style="margin:12px 0 0 0">Thank you.</p>
</div>













