document.addEventListener('DOMContentLoaded', () => {
  const editBtn = document.getElementById('editBtn');
  const saveBtn = document.getElementById('saveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const changePhotoBtn = document.getElementById('changePhotoBtn');
  const formInputs = document.querySelectorAll('#profile-form input');
  const photoInput = document.getElementById('photo');
  const preview = document.getElementById('preview');

  if (!editBtn || !saveBtn || !cancelBtn) return; // biar tidak error kalau belum ada elemen

  editBtn.addEventListener('click', () => {
    formInputs.forEach(input => input.removeAttribute('readonly'));
    changePhotoBtn.classList.remove('d-none');
    editBtn.classList.add('d-none');
    saveBtn.classList.remove('d-none');
    cancelBtn.classList.remove('d-none');
    document.querySelector('.profile-card').classList.add('editing');
  });

  cancelBtn.addEventListener('click', () => window.location.reload());

  changePhotoBtn.addEventListener('click', () => photoInput.click());
  photoInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) preview.src = URL.createObjectURL(file);
  });
});
