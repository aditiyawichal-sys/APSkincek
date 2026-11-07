<?php require_once '../app/views/layouts/header.php'; ?>

<?php
$isRegister = ($type === 'register');
$remainingSeconds = max(0, $expires_at - time());
?>

<div class="row justify-content-center my-4">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-shield-lock-fill fs-2"></i>
                </div>
                <h4 class="fw-bold mb-1">Verifikasi Kode OTP</h4>
                <p class="text-muted small mb-0">
                    <?= $isRegister ? 'Konfirmasi Pendaftaran Akun APSkincek' : 'Verifikasi Masuk via WhatsApp' ?>
                </p>
            </div>

            <div class="card-body p-4 pt-2">
                <!-- Info Nomor WhatsApp Tujuan -->
                <div class="bg-light p-3 rounded-3 text-center mb-3 border">
                    <span class="text-muted small d-block mb-1">Kode OTP 6-digit telah dikirim ke WhatsApp:</span>
                    <div class="fw-bold text-success fs-5 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($phone) ?>
                    </div>
                </div>

                <!-- Alert Feedback -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-check-circle-fill me-1"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($otp_dev_hint)): ?>
                    <div class="alert alert-warning py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i> <?= htmlspecialchars($otp_dev_hint) ?>
                    </div>
                <?php endif; ?>

                <!-- Form Masukkan Kode OTP -->
                <form action="/APSkincek/SkinCheck/public/verify-otp?type=<?= htmlspecialchars($type) ?>" method="POST" autocomplete="off" id="otpForm">
                    <div class="mb-3 text-center">
                        <label for="otp" class="form-label small fw-semibold text-secondary mb-2">
                            Masukkan 6 Digit Kode OTP
                        </label>
                        <input type="text" 
                               name="otp" 
                               id="otp" 
                               class="form-control form-control-lg text-center fw-bold rounded-3 letter-spacing-lg" 
                               placeholder="------" 
                               maxlength="6" 
                               pattern="[0-9]{6}" 
                               inputmode="numeric" 
                               autocomplete="one-time-code" 
                               autofocus 
                               required 
                               style="font-size: 1.8rem; letter-spacing: 12px; font-family: monospace;">
                        <div class="form-text small text-muted mt-2">
                            Kode ini berlaku selama <strong>5 menit</strong>. Jangan bagikan kode ini kepada siapa pun.
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="text-center mb-4">
                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small" id="timerBadge">
                            <i class="bi bi-stopwatch me-1 text-danger"></i> Sisa Waktu: <strong id="countdown" class="text-danger">--:--</strong>
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm" id="btnSubmitOtp">
                        <i class="bi bi-check2-circle me-1"></i> Verifikasi & Lanjutkan
                    </button>
                </form>

                <!-- Form Kirim Ulang OTP -->
                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-2">Tidak menerima kode di WhatsApp?</p>
                    <form action="/APSkincek/SkinCheck/public/resend-otp" method="POST" id="resendForm">
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none fw-semibold p-0" id="btnResendOtp">
                            <i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang Kode OTP
                        </button>
                        <span id="resendCooldownText" class="small text-muted d-none ms-1"></span>
                    </form>

                    <div class="mt-3">
                        <?php if ($isRegister): ?>
                            <a href="/APSkincek/SkinCheck/public/register" class="small text-secondary text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Ganti Nomor / Kembali ke Registrasi
                            </a>
                        <?php else: ?>
                            <a href="/APSkincek/SkinCheck/public/login" class="small text-secondary text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus & sanitize OTP input
    const otpInput = document.getElementById('otp');
    otpInput.focus();
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        if (this.value.length === 6) {
            // Optional auto-submit when 6 digits are typed
            // document.getElementById('otpForm').submit();
        }
    });

    // 5-minute Countdown Timer
    let remainingTime = <?= $remainingSeconds ?>;
    const countdownEl = document.getElementById('countdown');
    const timerBadge = document.getElementById('timerBadge');

    function updateCountdown() {
        if (remainingTime <= 0) {
            countdownEl.textContent = 'Kedaluwarsa';
            timerBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small';
            return;
        }

        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        countdownEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        remainingTime--;
        setTimeout(updateCountdown, 1000);
    }
    updateCountdown();

    // Resend Cooldown (60 seconds)
    const btnResend = document.getElementById('btnResendOtp');
    const resendCooldownText = document.getElementById('resendCooldownText');
    let cooldownSeconds = 60;

    function startCooldown() {
        btnResend.classList.add('disabled', 'text-muted');
        btnResend.setAttribute('disabled', 'disabled');
        resendCooldownText.classList.remove('d-none');

        const interval = setInterval(() => {
            if (cooldownSeconds <= 0) {
                clearInterval(interval);
                btnResend.classList.remove('disabled', 'text-muted');
                btnResend.removeAttribute('disabled');
                resendCooldownText.classList.add('d-none');
                btnResend.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang Kode OTP';
            } else {
                resendCooldownText.textContent = `(Tunggu ${cooldownSeconds}s)`;
                cooldownSeconds--;
            }
        }, 1000);
    }
    startCooldown();
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
