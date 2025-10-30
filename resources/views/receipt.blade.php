<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madridejos Community Waterworks System - Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .bill-container {
            width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #4a90e2, #7bb3f0);
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }
        
        .logo::before {
            content: "💧";
            font-size: 30px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }
        
        .due-date {
            text-align: right;
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .customer-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .customer-left {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 10px;
            align-items: start;
        }
        
        .customer-right {
            text-align: right;
        }
        
        .label {
            font-size: 14px;
            color: #666;
        }
        
        .value {
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }
        
        .meter-number {
            color: #4a90e2;
            font-size: 16px;
            font-weight: bold;
        }
        
        .consumption-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid #333;
        }
        
        .consumption-table th,
        .consumption-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        
        .consumption-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        .charges-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }
        
        .charges-left {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: start;
        }
        
        .charges-right {
            text-align: right;
        }
        
        .charge-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .total-section {
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: right;
            margin-bottom: 20px;
        }
        
        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
        
        .signature {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
        
        .horizontal-line {
            border-top: 2px solid #333;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <div class="horizontal-line"></div>
        
        <div class="header">
            <div class="logo"></div>
            <div class="company-name">MADRIDEJOS COMMUNITY WATERWORKS SYSTEM</div>
            <div class="company-address">
                MUNICIPALITY OF MADRIDEJOS<br>
                MADRIDEJOS, CEBU
            </div>
        </div>
        
        <div class="due-date">
            <strong>RECEIPT DATE : {{ optional($payment->created_at)->format('M d, Y h:i A') }}</strong>
        </div>
        
        <div class="customer-info">
            <div class="customer-left">
                <div class="label">NAME :</div>
                <div class="value">{{ optional($payment->waterBill->customer)->full_name }}</div>
                
                <div class="label">ADDRESS :</div>
                <div class="value">{{ optional($payment->waterBill->customer)->address }}</div>
                
                <div class="label">ACCOUNT NO</div>
                <div class="value">{{ optional($payment->waterBill->customer)->customer_number ?? 'CUST-'.$payment->customer_id }}</div>
                
                <div class="label">REGISTRATION NO.</div>
                <div class="value">{{ optional($payment->waterBill->customer)->registration_no ?? 'N/A' }}</div>
                
                <div class="label">METER NO.</div>
                <div class="value meter-number">{{ optional($payment->waterBill->customer)->meter_no ?? 'N/A' }}</div>
                
                <div class="label">TYPE</div>
                <div class="value">RESIDENTIAL</div>
            </div>
            
            <div class="customer-right">
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 24px; font-weight: bold; color: #4a90e2;">₱{{ number_format($payment->amount_paid, 2) }}</div>
                </div>
                
                <div class="charge-item">
                    <span>First 10 cu.m.</span>
                    <span>P160.00</span>
                    <span style="margin-left: 20px;">160.00</span>
                </div>
                <div class="charge-item">
                    <span></span>
                    <span>P16.00/cu.m.</span>
                    <span>-</span>
                </div>
                <div class="charge-item">
                    <span></span>
                    <span>P18.00/cu.m.</span>
                    <span>-</span>
                </div>
                
                <div style="margin: 15px 0;">
                    <div class="charge-item">
                        <span><strong>TOTAL CONSUMPTION COST:</strong></span>
                        <span><strong>₱{{ number_format(optional($payment->waterBill)->total_amount ?? 0, 2) }}</strong></span>
                    </div>
                    <div class="charge-item">
                        <span>Additional Charges</span>
                        <span></span>
                    </div>
                    <div class="charge-item">
                        <span>Adjustment</span>
                        <span></span>
                    </div>
                </div>
                
                <div class="total-section">
                    <div class="charge-item">
                        <span><strong>TOTAL CURRENT CHARGES:</strong></span>
                        <span><strong>₱{{ number_format(optional($payment->waterBill)->total_amount ?? 0, 2) }}</strong></span>
                    </div>
                    <div class="charge-item">
                        <span><em>Billing Month: {{ optional(optional($payment->waterBill)->billing_month)->format('M Y') }}</em></span>
                        <span><strong>Remaining Balance: ₱{{ number_format(optional($payment->waterBill)->balance ?? 0, 2) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        
        <table class="consumption-table">
            <thead>
                <tr>
                    <th rowspan="2">MONTH</th>
                    <th colspan="2">READING</th>
                    <th rowspan="2">USED (cu.m.)</th>
                </tr>
                <tr>
                    <th>PRESENT</th>
                    <th>PREVIOUS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ optional(optional($payment->waterBill)->billing_month)->format('M Y') }}</strong></td>
                    <td>(Present reading not available on receipt)</td>
                    <td>(Previous reading not available on receipt)</td>
                    <td>{{ number_format(optional($payment->waterBill)->cubic_meters_used ?? 0, 2) }} cu.m.</td>
                </tr>
            </tbody>
        </table>
        
        <div class="total-amount">
            AMOUNT PAID : ₱{{ number_format($payment->amount_paid, 2) }}    
        </div>
        
        <div class="footer">
            Paying this bill after due date will be charge P20.00. Failure to pay ( 15 ) days after the due date is subject for disconnection without prior notice.
        </div>
        
        <div class="signature">
            (SGD.) {{ optional($payment->accountant)->full_name ?? 'Accountant' }}<br>
            MACWAS
        </div>
        
        <div class="horizontal-line"></div>
    </div>
</body>
</html>