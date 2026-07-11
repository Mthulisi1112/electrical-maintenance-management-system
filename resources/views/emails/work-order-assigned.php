<!DOCTYPE html>
<html>
<head>
    <title>New Work Order Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; }
        .content { padding: 20px; background: #f9fafb; }
        .button { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Work Order Assigned</h1>
        </div>
        <div class="content">
            <p>Hello {{ $workOrder->technician->name }},</p>
            
            <p>You have been assigned a new work order:</p>
            
            <h3>{{ $workOrder->title }}</h3>
            <p><strong>Order #:</strong> {{ $workOrder->work_order_number }}</p>
            <p><strong>Asset:</strong> {{ $workOrder->asset->name ?? 'N/A' }}</p>
            <p><strong>Type:</strong> {{ ucfirst($workOrder->type) }}</p>
            <p><strong>Scheduled Date:</strong> {{ $workOrder->scheduled_date->format('M d, Y') }}</p>
            <p><strong>Priority:</strong> {{ ucfirst($workOrder->priority ?? 'medium') }}</p>
            
            <p>
                <a href="{{ route('work-orders.show', $workOrder) }}" class="button" style="color: white;">
                    View Work Order
                </a>
            </p>
        </div>
    </div>
</body>
</html>
