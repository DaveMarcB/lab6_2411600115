document.addEventListener('DOMContentLoaded', function () {
    const POLLING_INTERVAL = 30000; // 30 seconds

    const elTotalProducts = document.getElementById('stat-total-products');
    const elLowStock = document.getElementById('stat-low-stock');
    const elOutOfStock = document.getElementById('stat-out-of-stock');
    const elInventoryValue = document.getElementById('stat-inventory-value');
    const elLastUpdated = document.getElementById('last-updated-time');
    const elPollingStatus = document.getElementById('polling-status');
    const elLowStockBody = document.getElementById('low-stock-table-body');
    const elRecentTxBody = document.getElementById('recent-transactions-table-body');

    // Animate element when its value changes
    function updateWithAnimation(element, newValue) {
        if (!element) return;
        const currentValue = element.textContent.trim();
        if (currentValue !== String(newValue).trim()) {
            element.style.transition = 'transform 0.25s ease, opacity 0.25s ease';
            element.style.transform = 'scale(1.15)';
            element.style.opacity = '0.6';

            setTimeout(() => {
                element.textContent = newValue;
                element.style.transform = 'scale(1)';
                element.style.opacity = '1';
            }, 200);
        }
    }

    async function refreshDashboardData() {
        try {
            const [statsRes, lowStockRes, recentTxRes] = await Promise.all([
                fetch('/api/dashboard/stats', { headers: { 'Accept': 'application/json' } }),
                fetch('/api/products/low-stock', { headers: { 'Accept': 'application/json' } }),
                fetch('/api/transactions/recent', { headers: { 'Accept': 'application/json' } })
            ]);

            if (!statsRes.ok || !lowStockRes.ok || !recentTxRes.ok) {
                throw new Error('Failed to fetch real-time dashboard updates.');
            }

            const statsData = await statsRes.json();
            const lowStockData = await lowStockRes.json();
            const recentTxData = await recentTxRes.json();

            // 1. Update Statistics Cards
            if (statsData.status === 'success') {
                const stats = statsData.data;
                updateWithAnimation(elTotalProducts, stats.total_products);
                updateWithAnimation(elLowStock, stats.low_stock);
                updateWithAnimation(elOutOfStock, stats.out_of_stock);
                updateWithAnimation(elInventoryValue, stats.formatted_inventory_value);

                if (elLastUpdated) {
                    elLastUpdated.textContent = stats.timestamp;
                }
            }

            // 2. Update Low Stock Table
            if (lowStockData.status === 'success' && elLowStockBody) {
                const items = lowStockData.data;
                if (items.length === 0) {
                    elLowStockBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">All products are well-stocked.</td>
                        </tr>`;
                } else {
                    elLowStockBody.innerHTML = items.map(item => `
                        <tr>
                            <td>
                                <a href="${item.url}" class="fw-semibold text-decoration-none text-dark">${item.name}</a>
                                <div class="small text-muted">${item.sku}</div>
                            </td>
                            <td class="fw-bold">${item.quantity}</td>
                            <td class="text-muted">${item.reorder_level}</td>
                            <td>
                                <span class="badge bg-${item.status_color}">${item.status}</span>
                            </td>
                        </tr>
                    `).join('');
                }
            }

            // 3. Update Recent Transactions Table
            if (recentTxData.status === 'success' && elRecentTxBody) {
                const txs = recentTxData.data;
                if (txs.length === 0) {
                    elRecentTxBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No recent transactions recorded.</td>
                        </tr>`;
                } else {
                    elRecentTxBody.innerHTML = txs.map(tx => {
                        const badgeClass = tx.type === 'stock_in'
                            ? 'bg-success'
                            : (tx.type === 'stock_out' ? 'bg-danger' : 'bg-info text-dark');
                        const qtyColor = tx.quantity_display.startsWith('-') ? 'text-danger' : 'text-success';

                        return `
                            <tr>
                                <td class="small text-muted">${tx.date}</td>
                                <td>
                                    <a href="${tx.product_url}" class="text-decoration-none fw-semibold text-dark">${tx.product_name}</a>
                                </td>
                                <td><span class="badge ${badgeClass}">${tx.type_label}</span></td>
                                <td class="fw-bold ${qtyColor}">${tx.quantity_display}</td>
                                <td class="small text-muted">${tx.user_name}</td>
                            </tr>
                        `;
                    }).join('');
                }
            }

            if (elPollingStatus) {
                elPollingStatus.className = 'ms-2 badge bg-success';
                elPollingStatus.textContent = 'Live (30s)';
            }
        } catch (error) {
            console.error('Dashboard polling error:', error);
            if (elPollingStatus) {
                elPollingStatus.className = 'ms-2 badge bg-danger';
                elPollingStatus.textContent = 'Offline / Retry';
            }
        }
    }

    // Poll every 30 seconds
    setInterval(refreshDashboardData, POLLING_INTERVAL);
});