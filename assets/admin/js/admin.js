document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarCollapse');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    }
    
    // Xóa thông báo sau 5 giây
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Preview ảnh khi tải lên
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const preview = document.getElementById(`${input.id}-preview`);
            if (preview && input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
    
    // Thêm nguyên liệu
    const addIngredientBtn = document.getElementById('add-ingredient');
    if (addIngredientBtn) {
        addIngredientBtn.addEventListener('click', function() {
            const container = document.getElementById('ingredients-container');
            const index = container.children.length;
            const template = `
                <div class="row mb-3 ingredient-row">
                    <div class="col-1 text-center">
                        <span class="badge bg-secondary">${index + 1}</span>
                    </div>
                    <div class="col-5">
                        <input type="text" name="ingredient_name[]" class="form-control" placeholder="Tên nguyên liệu" required>
                    </div>
                    <div class="col-4">
                        <input type="text" name="ingredient_amount[]" class="form-control" placeholder="Số lượng" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-outline-danger remove-ingredient">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', template);
            
            // Cập nhật số thứ tự
            updateIngredientNumbers();
            
            // Thêm event listener cho nút xóa
            const removeButtons = document.querySelectorAll('.remove-ingredient');
            removeButtons.forEach(button => {
                button.addEventListener('click', removeIngredient);
            });
        });
    }
    
    // Thêm sự kiện xóa nguyên liệu
    const removeIngredientBtns = document.querySelectorAll('.remove-ingredient');
    removeIngredientBtns.forEach(button => {
        button.addEventListener('click', removeIngredient);
    });
    
    function removeIngredient() {
        const ingredientRows = document.querySelectorAll('.ingredient-row');
        if (ingredientRows.length > 1) {
            this.closest('.ingredient-row').remove();
            updateIngredientNumbers();
        } else {
            alert('Phải có ít nhất một nguyên liệu');
        }
    }
    
    function updateIngredientNumbers() {
        const ingredientRows = document.querySelectorAll('.ingredient-row');
        ingredientRows.forEach((row, index) => {
            const badge = row.querySelector('.badge');
            badge.textContent = index + 1;
        });
    }
    
    // Thêm bước
    const addStepBtn = document.getElementById('add-step');
    if (addStepBtn) {
        addStepBtn.addEventListener('click', function() {
            const container = document.getElementById('steps-container');
            const index = container.children.length;
            const template = `
                <div class="card mb-3 step-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Bước <span class="step-number">${index + 1}</span></h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-step">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea name="step_description[]" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh minh họa (tùy chọn)</label>
                            <input type="file" name="step_image[]" class="form-control step-image" accept="image/*">
                            <img src="" class="form-image-preview mt-2 d-none" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', template);
            
            // Cập nhật số thứ tự
            updateStepNumbers();
            
            // Thêm event listener cho nút xóa và xem trước ảnh
            const removeButtons = document.querySelectorAll('.remove-step');
            removeButtons.forEach(button => {
                button.addEventListener('click', removeStep);
            });
            
            const stepImages = document.querySelectorAll('.step-image');
            stepImages.forEach(input => {
                input.addEventListener('change', previewStepImage);
            });
        });
    }
    
    // Thêm sự kiện xóa bước
    const removeStepBtns = document.querySelectorAll('.remove-step');
    removeStepBtns.forEach(button => {
        button.addEventListener('click', removeStep);
    });
    
    function removeStep() {
        const stepCards = document.querySelectorAll('.step-card');
        if (stepCards.length > 1) {
            this.closest('.step-card').remove();
            updateStepNumbers();
        } else {
            alert('Phải có ít nhất một bước thực hiện');
        }
    }
    
    function updateStepNumbers() {
        const stepCards = document.querySelectorAll('.step-card');
        stepCards.forEach((card, index) => {
            const number = card.querySelector('.step-number');
            number.textContent = index + 1;
        });
    }
    
    // Xem trước ảnh bước
    const stepImages = document.querySelectorAll('.step-image');
    stepImages.forEach(input => {
        input.addEventListener('change', previewStepImage);
    });
    
    function previewStepImage(event) {
        const input = event.target;
        const preview = input.nextElementSibling;
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
});
