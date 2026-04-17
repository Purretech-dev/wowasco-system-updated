// ==============================
// TODAY'S ANALYSIS
// ==============================

const todayRevenueCtx = document.getElementById('todayRevenueChart').getContext('2d');
new Chart(todayRevenueCtx, {
    type: 'bar',
    data: {
        labels: ['Revenue Today'],
        datasets: [{
            label: 'KES',
            data: [125400],
            backgroundColor: ['#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: true, text: "Today's Revenue" }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


// 🔥 Reusable Doughnut Config Function (Clean + Professional)
function createDoughnutChart(ctx, titleText, dataValues, colors) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                data: dataValues,
                backgroundColor: colors,
                hoverOffset: 20 // 🎯 Built-in pop-out effect
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '55%',
            plugins: {
                title: {
                    display: true,
                    text: titleText
                },
                legend: {
                    position: 'bottom'
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}


// ==============================
// TODAY METERS DOUGHNUT
// ==============================

const todayMetersCtx = document.getElementById('todayMetersChart').getContext('2d');

createDoughnutChart(
    todayMetersCtx,
    "Today's Meters Status",
    [4320, 380],
    ['#28a745', '#0056b3']
);


// ==============================
// LAST MONTH ANALYSIS
// ==============================

const lastMonthRevenueCtx = document.getElementById('lastMonthRevenueChart').getContext('2d');
new Chart(lastMonthRevenueCtx, {
    type: 'bar',
    data: {
        labels: ['Revenue Last Month'],
        datasets: [{
            label: 'KES',
            data: [2540000],
            backgroundColor: ['#007bff']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: { display: true, text: "Revenue Last Month" }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


// ==============================
// LAST MONTH METERS DOUGHNUT
// ==============================

const lastMonthMetersCtx = document.getElementById('lastMonthMetersChart').getContext('2d');

createDoughnutChart(
    lastMonthMetersCtx,
    "Last Month Meters Status",
    [4100, 400],
    ['#218838', '#0056b3']
);


// ==============================
// TREND GRAPHS
// ==============================

const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
new Chart(revenueTrendCtx, {
    type: 'line',
    data: {
        labels: ['Week1', 'Week2', 'Week3', 'Week4'],
        datasets: [{
            label: 'Revenue (KES)',
            data: [500000, 700000, 650000, 800000],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40,167,69,0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: "Revenue Trend" }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


const metersTrendCtx = document.getElementById('metersTrendChart').getContext('2d');
new Chart(metersTrendCtx, {
    type: 'line',
    data: {
        labels: ['Week1', 'Week2', 'Week3', 'Week4'],
        datasets: [
            {
                label: 'Active Meters',
                data: [4300, 4350, 4320, 4340],
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Inactive Meters',
                data: [380, 370, 380, 360],
                borderColor: '#0056b3',
                backgroundColor: 'rgba(0,86,179,0.2)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: "Meters Trend" }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});