let chartInstance = null;

// Initialisation
document.getElementById('questionSelect').addEventListener('change', function() {
    document.getElementById('chartTypeContainer').classList.remove('d-none');
    loadChartData();
});

document.getElementById('chartType').addEventListener('change', loadChartData);

async function loadChartData() {
    try {
        const formData = new FormData(document.getElementById('analysisForm'));
        const response = await fetch(document.getElementById('analysisForm').action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error('Server error');

        const data = await response.json();

        document.getElementById('chartContainer').classList.remove('d-none');
        document.getElementById('resultsTable').classList.remove('d-none');
        document.getElementById('errorAlert').classList.add('d-none');

        updateTable(data);
        renderChart(data);

    } catch (error) {
        document.getElementById('errorAlert').textContent = error.message;
        document.getElementById('errorAlert').classList.remove('d-none');
    }
}

function updateTable(data) {
    const tbody = document.querySelector('#resultsTable tbody');
    tbody.innerHTML = data.rows.map(row => `
        <tr>
            <td>${row.value}</td>
            <td>${row.count}</td>
            <td>${row.ratio}%</td>
        </tr>
    `).join('');

    document.getElementById('totalResponses').textContent = data.total;
}

function renderChart(data) {
    const ctx = document.getElementById('analysisChart').getContext('2d');

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: document.getElementById('chartType').value,
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: [
                    '#4dc9f6', '#f67019', '#f53794', '#537bc4',
                    '#acc236', '#166a8f', '#00a950', '#58595b',
                    '#8549ba', '#4dc9f6'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                tooltip: {
                    callbacks: {
                        label: context =>
                            `${context.label}: ${context.raw} (${data.ratios[context.dataIndex]}%)`
                    }
                }
            }
        }
    });
}

