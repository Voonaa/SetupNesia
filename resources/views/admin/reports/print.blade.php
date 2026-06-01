<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report['title'] }} - SetupNesia</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-weight: 600;
        }
        .logo {
            font-size: 20px;
            font-weight: 800;
            color: #7c3aed;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .metrics-grid {
            display: grid;
            grid-template-cols: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .metric-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            background-color: #f8fafc;
        }
        .metric-card span {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        .metric-card strong {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            display: block;
        }
        .table-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 15px 0;
            border-left: 4px solid #7c3aed;
            padding-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .item-row {
            font-size: 12px;
            color: #475569;
            margin-bottom: 2px;
        }
        .signatures {
            margin-top: 60px;
            display: grid;
            grid-template-cols: repeat(2, 1fr);
            gap: 40px;
            text-align: center;
        }
        .signature-box {
            padding-top: 60px;
            border-top: 1px dashed #cbd5e1;
            width: 200px;
            margin: 0 auto;
            color: #475569;
            font-weight: 600;
        }
        .signature-box span {
            font-size: 11px;
            color: #94a3b8;
            display: block;
            margin-top: 4px;
        }
        @media print {
            body {
                padding: 0;
            }
            .metric-card {
                background-color: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
            }
            th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>{{ $report['title'] }}</h1>
            <p>Generated on {{ date('d F Y, H:i') }} | SetupNesia Sales Tracking</p>
        </div>
        <div class="logo">
            SetupNesia
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <span>Total Sales Orders</span>
            <strong>{{ $report['total_orders'] }} Orders</strong>
        </div>
        <div class="metric-card">
            <span>Total Net Revenue</span>
            <strong>Rp {{ number_format($report['total_revenue'], 0, ',', '.') }}</strong>
        </div>
        <div class="metric-card">
            <span>Total Items Sold</span>
            <strong>{{ $report['total_items_sold'] }} Units</strong>
        </div>
        <div class="metric-card">
            <span>Average Order Value</span>
            <strong>Rp {{ number_format($report['average_order_value'], 0, ',', '.') }}</strong>
        </div>
    </div>

    <!-- Transactions List -->
    <h3 class="table-title">Transaction Details Log</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Order Number</th>
                <th style="width: 20%">Date Placed</th>
                <th style="width: 20%">Customer Details</th>
                <th style="width: 25%">Ordered items</th>
                <th style="width: 10%">Shipping</th>
                <th style="width: 10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['orders'] as $order)
                <tr>
                    <td style="font-weight: 700; color: #0f172a;">{{ $order->order_number }}</td>
                    <td style="color: #64748b; font-weight: 500;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <span style="font-weight: 600; color: #334155; display: block;">{{ $order->user->name }}</span>
                        <span style="font-size: 11px; color: #64748b;">{{ $order->user->email }}</span>
                    </td>
                    <td>
                        @foreach($order->items as $item)
                            <div class="item-row">
                                • {{ $item->product ? $item->product->name : 'N/A' }}
                                <span style="font-weight: 700; color: #64748b;">(x{{ $item->quantity }})</span>
                            </div>
                        @endforeach
                    </td>
                    <td style="font-weight: 600; color: #475569;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    <td style="font-weight: 700; color: #7c3aed;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 30px; font-style: italic;">
                        No orders recorded during this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Formal Signatures Footer -->
    <div class="signatures">
        <div>
            <div class="signature-box">
                Prepared By,
                <span>Administrator Staff</span>
            </div>
        </div>
        <div>
            <div class="signature-box">
                Approved By,
                <span>Finance & Store Owner</span>
            </div>
        </div>
    </div>

    <!-- JavaScript Auto-trigger Print Dialog -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
