document.addEventListener('DOMContentLoaded', function() {
    const alert = document.querySelector('.floating-alert');
    if (alert) {
        // Tambahkan progress bar
        const progressBar = document.createElement('div');
        progressBar.className = 'alert-progress';
        alert.appendChild(progressBar);

        // Tambahkan efek shake ringan saat muncul
        setTimeout(() => {
            alert.classList.add('shake');
            setTimeout(() => alert.classList.remove('shake'), 400);
        }, 200);

        // Auto close setelah 5 detik
        setTimeout(() => closeAlert(alert), 5000);
    }
});

function closeAlert(el) {
    if (!el) return;
    el.style.animation = 'slideOutRight 0.3s ease-in-out forwards';
    setTimeout(() => el.remove(), 100);
}
