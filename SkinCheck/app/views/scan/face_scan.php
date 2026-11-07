<?php require_once '../app/views/layouts/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">Face Scan</h2>
        
        <div id="alertBox" class="alert d-none" role="alert"></div>

        <div class="card mb-4">
            <div class="card-body text-center p-0 overflow-hidden position-relative" style="background-color: #000; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                
                <!-- Video feed (default hidden until started) -->
                <video id="cameraFeed" autoplay playsinline style="width: 100%; max-height: 500px; display: none; object-fit: cover;"></video>
                
                <!-- Canvas to capture the image (always hidden) -->
                <canvas id="captureCanvas" style="display: none;"></canvas>
                
                <!-- Captured Image Preview (default hidden) -->
                <img id="imagePreview" class="img-fluid" style="display: none; width: 100%; max-height: 500px; object-fit: cover;" alt="Captured Face">
                
                <!-- Initial State / Placeholder -->
                <div id="initialState" class="p-5 text-white w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="position: absolute; top:0; left:0; z-index: 1;">
                    <i class="bi bi-camera text-white-50" style="font-size: 5rem; margin-bottom: 20px;"></i>
                    <p class="text-white-50 mb-0">Allow camera access to start scanning</p>
                </div>
            </div>
            <div class="card-footer bg-white p-3 text-center">
                <!-- Action Buttons -->
                <button id="btnStart" class="btn btn-primary px-4"><i class="bi bi-play-circle me-2"></i> Start Camera</button>
                <button id="btnCapture" class="btn btn-success px-4" style="display: none;"><i class="bi bi-camera-fill me-2"></i> Capture</button>
                <button id="btnRetake" class="btn btn-secondary px-4" style="display: none;"><i class="bi bi-arrow-counterclockwise me-2"></i> Retake</button>
                <button id="btnSubmit" class="btn btn-primary px-4" style="display: none;"><i class="bi bi-cloud-upload me-2"></i> Submit Scan</button>
            </div>
        </div>
        
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="d-none text-center mt-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Processing your scan...</p>
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

    // 1. Start Camera
    btnStart.addEventListener('click', async () => {
        hideAlert();
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = stream;
                
                // Update UI
                initialState.style.display = 'none';
                video.style.display = 'block';
                btnStart.style.display = 'none';
                btnCapture.style.display = 'inline-block';
            } catch (err) {
                console.error("Camera error:", err);
                showAlert("Camera permission denied or camera unavailable. Please check your browser settings.", "danger");
            }
        } else {
            showAlert("WebRTC is not supported in this browser.", "danger");
        }
    });

    // 2. Capture Image
    btnCapture.addEventListener('click', () => {
        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Get image data
        capturedImageBase64 = canvas.toDataURL('image/jpeg');
        
        // Show preview
        imagePreview.src = capturedImageBase64;
        video.style.display = 'none';
        imagePreview.style.display = 'block';
        
        // Update buttons
        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-block';
        btnSubmit.style.display = 'inline-block';
    });

    // 3. Retake Image
    btnRetake.addEventListener('click', () => {
        imagePreview.style.display = 'none';
        video.style.display = 'block';
        capturedImageBase64 = null;
        
        // Update buttons
        btnRetake.style.display = 'none';
        btnSubmit.style.display = 'none';
        btnCapture.style.display = 'inline-block';
        hideAlert();
    });

    // 4. Submit Scan
    btnSubmit.addEventListener('click', async () => {
        if (!capturedImageBase64) return;
        
        // UI Loading State
        btnRetake.disabled = true;
        btnSubmit.disabled = true;
        loadingOverlay.classList.remove('d-none');
        hideAlert();

        try {
            const response = await fetch('/APSkincek/SkinCheck/public/api/scan/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image: capturedImageBase64 })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert(result.message, "success");
                
                // Stop camera stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                // Redirect to skin analysis after success
                setTimeout(() => {
                    window.location.href = result.redirect || '/APSkincek/SkinCheck/public/skin-analysis';
                }, 1000);
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
