// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Tampilkan angka apa adanya (tanpa format ribuan/desimal)
function number_format(n){ return n; }

// Data pembagian (total keseluruhan = 1000)
const labels = ["RPL", "TKJ", "AKL", "TKRO", "TP"];

// Laki-laki & Perempuan per jurusan
const maleData   = [130, 120, 100, 200, 200]; // contoh
const femaleData = [ 90,  60, 100,   0,  0]; // TKRO & TP = 0

// Hitung total otomatis per jurusan
const totalData = maleData.map((v, i) => v + femaleData[i]); // => [220, 180, 200, 200, 200] (sum = 1000)

var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [
      {
        label: "Laki-laki",
        backgroundColor: "#4e73df",
        hoverBackgroundColor: "#2e59d9",
        borderColor: "#4e73df",
        data: maleData,
      },
      {
        label: "Perempuan",
        backgroundColor: "#e74a3b",
        hoverBackgroundColor: "#c0392b",
        borderColor: "#e74a3b",
        data: femaleData,
      },
      {
        label: "Total Siswa",
        backgroundColor: "#36b9cc",
        hoverBackgroundColor: "#2c9faf",
        borderColor: "#36b9cc",
        data: totalData,
      }
    ],
  },
  options: {
    maintainAspectRatio: false,
    layout: { padding: { left:10, right:25, top:25, bottom:0 } },
    scales: {
      xAxes: [{
        gridLines: { display:false, drawBorder:false },
        ticks: { maxTicksLimit: 6 },
        maxBarThickness: 25,
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true,
          // atur batas atas biar enak dilihat
          suggestedMax: Math.max(...totalData) + 25,
          callback: function(value){ return value + ' Siswa'; }
        },
        gridLines: {
          color:"rgb(234, 236, 244)",
          zeroLineColor:"rgb(234, 236, 244)",
          drawBorder:false,
          borderDash:[2],
          zeroLineBorderDash:[2]
        }
      }],
    },
    legend: { display: true },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, data) {
          var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ' : ' + tooltipItem.yLabel + ' Siswa';
        }
      }
    }
  }
});
