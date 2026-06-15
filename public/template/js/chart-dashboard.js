/**
 * Dashboard Charts JS
 * File: public/js/siswa/dashboard-charts.js
 */

// Set default colors untuk Chart.js
Chart.defaults.color = '#858796';
Chart.defaults.borderColor = '#dddfeb';

// Fungsi untuk inisialisasi Pie Chart Kehadiran
function initKehadiranChart(kehadiranData) {
    const ctxPie = document.getElementById('myPieChart');
    if (!ctxPie) return;

    const myPieChart = new Chart(ctxPie.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    kehadiranData.hadir,
                    kehadiranData.izin,
                    kehadiranData.sakit,
                    kehadiranData.alpha
                ],
                backgroundColor: ['#4e73df', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#2c9faf', '#f4b619', '#d32f2f'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                }
            },
            cutout: '80%',
        }
    });

    return myPieChart;
}

// Fungsi untuk inisialisasi Bar Chart Nilai
function initNilaiChart(mapelNames, mapelScores) {
    const ctxBar = document.getElementById('myBarChart');
    if (!ctxBar) return;

    const myBarChart = new Chart(ctxBar.getContext('2d'), {
        type: 'bar',
        data: {
            labels: mapelNames,
            datasets: [{
                label: 'Nilai',
                data: mapelScores,
                backgroundColor: '#4e73df',
                hoverBackgroundColor: '#2e59d9',
                borderColor: '#4e73df',
                maxBarThickness: 50,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        callback: function(value) {
                            return value;
                        }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            return 'Nilai: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    return myBarChart;
}

// Export functions untuk digunakan di blade
window.initKehadiranChart = initKehadiranChart;
window.initNilaiChart = initNilaiChart;
