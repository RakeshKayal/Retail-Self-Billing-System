<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Ether POS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        body { background-color: #0a0e14; color: #f1f3fc; transition: all 0.3s ease; }
        html.light body { background-color: #f9fafb; color: #1f2937; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-[#0a0e14] min-h-screen">
    @include('Every.sidebar')
    
    <div class="ml-64">
        @include('Every.topbar')

        <main class="pt-16 p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>

                    <h1 class="text-4xl font-bold mb-2 text-white">{{ auth()->user()->name }}'s Dashboard</h1>

                    
                    <p class="text-gray-400">Real-time sales and revenue tracking</p>
                </div>
                <div>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Manage Staff</a>
                    @endif
                </div>
            </div>

            <!-- Real-time Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Today's Revenue -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Today's Revenue</p>
                            <p class="text-3xl font-bold text-green-400" id="todayRevenue">₹0.00</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-green-400" style="font-size: 32px;">trending_up</span>
                    </div>
                </div>

                <!-- Month Revenue -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">This Month</p>
                            <p class="text-3xl font-bold text-blue-400" id="monthRevenue">₹0.00</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-blue-400" style="font-size: 32px;">calendar_month</span>
                    </div>
                </div>

                <!-- Online Sales -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Online/Card Sales</p>
                            <p class="text-3xl font-bold text-purple-400" id="onlineSales">₹0.00</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-purple-400" style="font-size: 32px;">credit_card</span>
                    </div>
                </div>

                <!-- Cash Sales -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Cash Sales</p>
                            <p class="text-3xl font-bold text-orange-400" id="cashSales">₹0.00</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-orange-400" style="font-size: 32px;">payments</span>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <!-- Sales Distribution Pie Chart -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <h2 class="text-xl font-bold mb-4 text-white">Sales Method Distribution</h2>
                    <div style="position: relative; height: 300px;">
                        <canvas id="salesMethodChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Trend -->
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <h2 class="text-xl font-bold mb-4 text-white">Revenue Trend</h2>
                    <div style="position: relative; height: 300px;">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">Recent Transactions</h2>
                    <button onclick="refreshPayments()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        🔄 Refresh
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-600 bg-[#0f141a]">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-300">Bill ID</th>
                                <th class="px-4 py-3 text-left text-gray-300">Bill Created By</th>
                                <th class="px-4 py-3 text-left text-gray-300">Bill Created For</th>
                                <th class="px-4 py-3 text-left text-gray-300">Amount</th>
                                <th class="px-4 py-3 text-left text-gray-300">Method</th>
                                <th class="px-4 py-3 text-left text-gray-300">Status</th>
                                <th class="px-4 py-3 text-left text-gray-300">Time</th>
                            </tr>
                        </thead>
                        <tbody id="recentPayments" class="divide-y divide-gray-600">
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-400">Loading transactions...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Charts
        let salesMethodChart = null;
        let revenueTrendChart = null;

        // Load revenue stats on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRevenueStats();
            refreshPayments();
            
            // Refresh stats every 10 seconds
            setInterval(loadRevenueStats, 10000);
            setInterval(refreshPayments, 30000);
        });

        async function loadRevenueStats() {
            try {
                const url = '{{ route("admin.revenue") }}'; // This should be /admin/revenue-stats
                console.log('Fetching revenue from:', url);

                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Revenue data received:', data);

                if (data.success) {
                    console.log('Updating dashboard with:', data);
                    const today = parseFloat(data.today || 0);
                    const thisMonth = parseFloat(data.this_month || 0);
                    const online = parseFloat(data.online_sales || 0);
                    const cash = parseFloat(data.cash_sales || 0);

                    document.getElementById('todayRevenue').textContent = '₹' + today.toFixed(2);
                    document.getElementById('monthRevenue').textContent = '₹' + thisMonth.toFixed(2);
                    document.getElementById('onlineSales').textContent = '₹' + online.toFixed(2);
                    document.getElementById('cashSales').textContent = '₹' + cash.toFixed(2);

                    updateCharts({
                        today: today,
                        this_month: thisMonth,
                        online_sales: online,
                        cash_sales: cash
                    });
                    console.log('Dashboard updated successfully!');
                } else {
                    console.error('API returned success=false:', data);
                }
            } catch (error) {
                console.error('Error loading revenue stats:', error);
            }
        }

        async function refreshPayments() {
            try {
                const url = '{{ route("admin.payments") }}';
                console.log('Fetching payments from:', url);

                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                console.log('Payments response status:', response.status);
                const data = await response.json();
                console.log('Payments data received:', data);

                const paymentsTable = document.getElementById('recentPayments');

                if (data.success && data.bills && data.bills.data) {
                    if (data.bills.data.length === 0) {
                        console.log('No bills found');
                        paymentsTable.innerHTML = '<tr><td colspan="6" class="px-4 py-3 text-center text-gray-400">No transactions yet</td></tr>';
                        return;
                    }

                    console.log('Found ' + data.bills.data.length + ' transactions');
                    let html = '';

                    data.bills.data.forEach(bill => {
                        const methodBg = bill.payment_method === 'cash'
                            ? 'bg-orange-500/20 text-orange-400'
                            : 'bg-purple-500/20 text-purple-400';

                        const creatorName = bill.user?.name || 'Unknown';
                        const creatorRole = bill.user?.role ? bill.user.role.charAt(0).toUpperCase() + bill.user.role.slice(1) : 'Customer';

                        // Determine "created for" display: prefer a store name, otherwise if creator is not a customer show 'Walk-in' or the customer's name
                        const createdFor = bill.store?.name || (bill.user && bill.user.role === 'customer' ? bill.user.name : 'Walk-in');

                        html += `<tr class="hover:bg-[#0f141a] transition">
                            <td class="px-4 py-3">
                                <span class="font-mono text-blue-400">#${bill.id}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm">${creatorName}</div>
                                    <div class="text-xs px-2 py-1 rounded bg-gray-700 text-gray-200">${creatorRole}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-300">${createdFor}</td>
                            <td class="px-4 py-3 font-bold text-green-400">₹${parseFloat(bill.total_amount).toFixed(2)}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-bold ${methodBg}">
                                    ${bill.payment_method.toUpperCase()}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-green-500/20 text-green-400">
                                    ${bill.status.toUpperCase()}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">${new Date(bill.created_at).toLocaleString()}</td>
                        </tr>`;
                    });

                    paymentsTable.innerHTML = html;
                    console.log('Transactions rendered successfully!');
                } else {
                    console.error('Invalid data structure:', data);
                    paymentsTable.innerHTML = '<tr><td colspan="6" class="px-4 py-3 text-center text-red-400">Error loading transactions</td></tr>';
                }
            } catch (error) {
                console.error('Error loading payments:', error);
                document.getElementById('recentPayments').innerHTML = '<tr><td colspan="6" class="px-4 py-3 text-center text-red-400">Error: ' + error.message + '</td></tr>';
            }
        }

        function updateCharts(data) {
            // Pie Chart - Sales Method Distribution
            const salesMethodCtx = document.getElementById('salesMethodChart').getContext('2d');
            
            if (salesMethodChart) {
                salesMethodChart.destroy();
            }
            
            salesMethodChart = new Chart(salesMethodCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Online/Card', 'Cash'],
                    datasets: [{
                        data: [data.online_sales, data.cash_sales],
                        backgroundColor: ['#9c7eff', '#ff9c4d'],
                        borderColor: ['#0a0e14', '#0a0e14'],
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#f1f3fc' }
                        }
                    }
                }
            });

            // Line Chart - Revenue Trend (Mock data for now)
            const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
            
            if (revenueTrendChart) {
                revenueTrendChart.destroy();
            }
            
            revenueTrendChart = new Chart(revenueTrendCtx, {
                type: 'line',
                data: {
                    labels: ['12 AM', '4 AM', '8 AM', '12 PM', '4 PM', '8 PM', '12 AM'],
                    datasets: [{
                        label: 'Revenue',
                        data: [data.today * 0.1, data.today * 0.2, data.today * 0.4, data.today * 0.6, data.today * 0.8, data.today * 0.9, data.today],
                        borderColor: '#00e3fd',
                        backgroundColor: 'rgba(0, 227, 253, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#f1f3fc' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#f1f3fc' },
                            grid: { color: '#1b2028' }
                        },
                        x: {
                            ticks: { color: '#f1f3fc' },
                            grid: { color: '#1b2028' }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
