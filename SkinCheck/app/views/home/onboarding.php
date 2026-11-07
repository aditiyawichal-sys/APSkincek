<?php require_once '../app/views/layouts/header.php'; ?>
<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-stars fs-3"></i>
                    </div>
                    <h2 class="fw-bold text-dark">Welcome to SkinCheck!</h2>
                    <p class="text-muted">Let's personalize your experience. Tell us a bit about your skin.</p>
                </div>
                
                <form action="/APSkincek/SkinCheck/public/onboarding/submit" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">What is your skin type?</label>
                        <select name="skin_type" class="form-select form-select-lg bg-light border-0" required>
                            <option value="">Select skin type...</option>
                            <option value="normal">Normal</option>
                            <option value="dry">Dry</option>
                            <option value="oily">Oily</option>
                            <option value="combination">Combination</option>
                            <option value="sensitive">Sensitive</option>
                        </select>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label fw-bold text-dark mb-3">What are your primary skin concerns?</label>
                        
                        <div class="form-check custom-checkbox mb-3 p-3 bg-light rounded-3 border-0">
                            <input class="form-check-input ms-2 me-3" type="checkbox" name="concerns[]" value="acne" id="concern1">
                            <label class="form-check-label fw-semibold w-100 cursor-pointer" for="concern1">Acne & Breakouts</label>
                        </div>
                        
                        <div class="form-check custom-checkbox mb-3 p-3 bg-light rounded-3 border-0">
                            <input class="form-check-input ms-2 me-3" type="checkbox" name="concerns[]" value="aging" id="concern2">
                            <label class="form-check-label fw-semibold w-100 cursor-pointer" for="concern2">Signs of Aging</label>
                        </div>
                        
                        <div class="form-check custom-checkbox mb-3 p-3 bg-light rounded-3 border-0">
                            <input class="form-check-input ms-2 me-3" type="checkbox" name="concerns[]" value="pigmentation" id="concern3">
                            <label class="form-check-label fw-semibold w-100 cursor-pointer" for="concern3">Dark Spots & Hyperpigmentation</label>
                        </div>
                        
                        <div class="form-check custom-checkbox mb-3 p-3 bg-light rounded-3 border-0">
                            <input class="form-check-input ms-2 me-3" type="checkbox" name="concerns[]" value="redness" id="concern4">
                            <label class="form-check-label fw-semibold w-100 cursor-pointer" for="concern4">Redness & Sensitivity</label>
                        </div>
                        
                        <div class="form-check custom-checkbox p-3 bg-light rounded-3 border-0">
                            <input class="form-check-input ms-2 me-3" type="checkbox" name="concerns[]" value="texture" id="concern5">
                            <label class="form-check-label fw-semibold w-100 cursor-pointer" for="concern5">Uneven Texture & Pores</label>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill shadow-sm">Complete Setup <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .custom-checkbox:hover { background-color: #e9ecef !important; transition: 0.2s; }
</style>
<?php require_once '../app/views/layouts/footer.php'; ?>
