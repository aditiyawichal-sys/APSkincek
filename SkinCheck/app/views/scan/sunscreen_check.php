<?php require_once '../app/views/layouts/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Sunscreen Check</h2>
        
        <div id="alertBox" class="alert d-none" role="alert"></div>

        <div class="alert alert-info border-info shadow-sm" role="alert">
            <h5 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i> How it works</h5>
            <p class="mb-0 small">This tool uses your standard camera to estimate sunscreen coverage by analyzing skin luminance and reflection. <strong>This is not a UV camera</strong>, but it provides a helpful indication of potentially missed areas.</p>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body text-center p-0 overflow-hidden position-relative" style="background-color: #1a1a1a; min-height: 400px; display: flex; align-items: center; justify-content: center; border-radius: 10px 10px 0 0;">
                
                <video id="cameraFeed" autoplay playsinline style="width: 100%; max-height: 500px; display: none; object-fit: cover;"></video>
                <canvas id="captureCanvas" style="display: none;"></canvas>
                <img id="imagePreview" class="img-fluid" style="display: none; width: 100%; max-height: 500px; object-fit: cover;" alt="Captured Face">
                
                <div id="initialState" class="p-5 w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="position: absolute; top:0; left:0; z-index: 1;">
                    <i class="bi bi-brightness-high text-warning" style="font-size: 5rem; margin-bottom: 20px;"></i>
                    <p class="text-white-50 mb-0">Allow camera access to check sunscreen coverage</p>
                </div>
            </div>
            <div class="card-footer bg-white p-3 text-center border-0 rounded-bottom">
                <button id="btnStart" class="btn btn-warning px-4 rounded-pill fw-bold"><i class="bi bi-play-circle me-2"></i> Start Camera</button>
                <button id="btnCapture" class="btn btn-success px-4 rounded-pill fw-bold" style="display: none;"><i class="bi bi-camera-fill me-2"></i> Capture</button>
                <button id="btnRetake" class="btn btn-secondary px-4 rounded-pill fw-bold" style="display: none;"><i class="bi bi-arrow-counterclockwise me-2"></i> Retake</button>
                <button id="btnSubmit" class="btn btn-primary px-4 rounded-pill fw-bold" style="display: none;"><i class="bi bi-magic me-2"></i> Analyze Coverage</button>
            </div>
        </div>
        
        <div id="loadingOverlay" class="d-none text-center mt-3">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mt-3 text-dark">Analyzing Coverage...</h5>
            <p class="text-muted small">Generating heatmap estimation based on luminance...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('cameraFeed');
    const canvas = document.getElementById('captureCanvas');
    const imagePreview = document.getElementById('imagePreview');
    const initialState = document.getElementById('initialState');
    
    const btnStart = document.getElementById('btnStart');
    const btnCapture = document.getElementById('btnCapture');
    const btnRetake = document.getElementById('btnRetake');
    const btnSubmit = document.getElementById('btnSubmit');
    const alertBox = document.getElementById('alertBox');
    const loadingOverlay = document.getElementById('loadingOverlay');

    let stream = null;
    let capturedImageBase64 = null;

    function showAlert(message, type) {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
    }

    function hideAlert() {
        alertBox.className = 'alert d-none';
    }

    btnStart.addEventListener('click', async () => {
        hideAlert();
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = stream;
                
                initialState.style.display = 'none';
                video.style.display = 'block';
                btnStart.style.display = 'none';
                btnCapture.style.display = 'inline-block';
            } catch (err) {
                console.error("Camera error:", err);
                showAlert("Camera permission denied or camera unavailable.", "danger");
            }
        } else {
            showAlert("WebRTC is not supported in this browser.", "danger");
        }
    });

    btnCapture.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        capturedImageBase64 = canvas.toDataURL('image/jpeg');
        
        imagePreview.src = capturedImageBase64;
        video.style.display = 'none';
        imagePreview.style.display = 'block';
        
        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-block';
        btnSubmit.style.display = 'inline-block';
    });

    btnRetake.addEventListener('click', () => {
        imagePreview.style.display = 'none';
        video.style.display = 'block';
        capturedImageBase64 = null;
        
        btnRetake.style.display = 'none';
        btnSubmit.style.display = 'none';
        btnCapture.style.display = 'inline-block';
        hideAlert();
    });

    btnSubmit.addEventListener('click', async () => {
        if (!capturedImageBase64) return;
        
        btnRetake.disabled = true;
        btnSubmit.disabled = true;
        loadingOverlay.classList.remove('d-none');
        hideAlert();

        try {
            const response = await fetch('/APSkincek/SkinCheck/public/api/sunscreen/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image: capturedImageBase64 })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(result.message, "success");
                
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                setTimeout(() => {
                    window.location.href = '/APSkincek/SkinCheck/public/sunscreen-analysis';
                }, 1500);
            } else {
                showAlert(result.message, "danger");
                btnRetake.disabled = false;
                btnSubmit.disabled = false;
                loadingOverlay.classList.add('d-none');
            }
        } catch (error) {
            console.error("Submission error:", error);
            showAlert("An error occurred while submitting the scan.", "danger");
            btnRetake.disabled = false;
            btnSubmit.disabled = false;
            loadingOverlay.classList.add('d-none');
        }
    });
});
</script>
<?php require_once '../app/views/layouts/footer.php'; ?>
