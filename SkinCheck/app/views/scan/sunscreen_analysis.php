<?php require_once '../app/views/layouts/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sunscreen Coverage Analysis</h2>
            <a href="/APSkincek/SkinCheck/public/sunscreen-check" class="btn btn-warning btn-sm fw-bold"><i class="bi bi-camera"></i> New Check</a>
        </div>

        <div class="alert alert-danger border-danger shadow-sm" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> IMPORTANT DISCLAIMER</h5>
            <p class="mb-0 small">
                <strong>Estimasi coverage sunscreen berdasarkan analisis pantulan cahaya (RGB). Bukan sensor UV medis.</strong><br>
                Kamera standar tidak dapat melihat sinar UV. Alat ini hanya memberikan indikasi area dengan coverage lebih rendah berdasarkan perbedaan luminasi dan tekstur wajah. Jangan gunakan hasil ini sebagai kepastian medis perlindungan UV.
            </p>
        </div>

        <?php if (isset($analysis) && $analysis): ?>
            <?php 
                $details = json_decode($analysis['missed_areas'], true) ?? [];
                $faceDetected = $details['face_detected'] ?? false;
                $coverageStatus = $details['coverage_status'] ?? 'Unknown';
                $heatmapUrl = $details['heatmap_filename'] ?? null;
                $lowAreas = $details['low_coverage_areas'] ?? [];
                $score = $analysis['coverage_score'] ?? 0;
                
                $statusColor = $score > 75 ? 'success' : ($score > 50 ? 'warning' : 'danger');
            ?>
            
            <div class="card mb-4 shadow border-0">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Images Section -->
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h5 class="text-muted small text-uppercase fw-bold mb-3">Visual Analysis</h5>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="position-relative">
                                        <img src="/APSkincek/SkinCheck/storage/<?= htmlspecialchars($analysis['image_url']) ?>" class="img-fluid rounded shadow-sm w-100" alt="Original Image" style="object-fit: cover; aspect-ratio: 3/4;">
                                        <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white text-center small rounded-bottom">Original</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="position-relative">
                                        <?php if ($heatmapUrl): ?>
                                            <img src="/APSkincek/SkinCheck/storage/<?= htmlspecialchars($heatmapUrl) ?>" class="img-fluid rounded shadow-sm w-100" alt="Coverage Heatmap" style="object-fit: cover; aspect-ratio: 3/4;">
                                        <?php else: ?>
                                            <div class="bg-dark rounded d-flex align-items-center justify-content-center text-muted" style="aspect-ratio: 3/4; height: 100%;">No Heatmap</div>
                                        <?php endif; ?>
                                        <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-75 text-warning fw-bold text-center small rounded-bottom">Estimation Map</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Section -->
                        <div class="col-md-6">
                            <h5 class="text-muted small text-uppercase fw-bold mb-3">Analysis Results</h5>
                            
                            <div class="p-4 bg-light rounded-3 mb-4 text-center border-bottom border-4 border-<?= $statusColor ?>">
                                <h1 class="display-4 fw-bold text-<?= $statusColor ?> mb-0"><?= $score ?>%</h1>
                                <p class="text-muted mb-2">Coverage Score</p>
                                <span class="badge bg-<?= $statusColor ?> fs-6 rounded-pill px-3 py-2"><?= htmlspecialchars($coverageStatus) ?></span>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold"><i class="bi bi-crosshair text-danger me-2"></i> Indikasi Area Perhatian:</h6>
                                <?php if (!empty($lowAreas)): ?>
                                    <ul class="list-group list-group-flush border rounded shadow-sm">
                                        <?php foreach ($lowAreas as $area): ?>
                                            <li class="list-group-item bg-transparent text-danger fw-bold"><i class="bi bi-arrow-right-short me-1"></i> <?= htmlspecialchars($area) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle me-2"></i> Coverage tampak merata.</div>
                                <?php endif; ?>
                            </div>

                            <div class="bg-white border rounded p-3 shadow-sm">
                                <h6 class="fw-bold small text-muted text-uppercase border-bottom pb-2 mb-3">Technical Details</h6>
                                <div class="row text-muted small">
                                    <div class="col-5 fw-bold">Analysis Method:</div>
                                    <div class="col-7"><?= htmlspecialchars($details['analysis_method'] ?? 'N/A') ?></div>
                                    
                                    <div class="col-5 fw-bold mt-2">Hardware Type:</div>
                                    <div class="col-7 mt-2"><?= htmlspecialchars($details['hardware_type'] ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Safe Recommendations -->
                <div class="card-footer bg-light p-4 border-top">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-success me-2"></i> Safe Sunscreen Recommendations</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <i class="bi bi-2-square-fill text-primary fs-3 me-3"></i>
                                <div>
                                    <strong class="d-block">Two-Finger Rule</strong>
                                    <span class="small text-muted">Gunakan sebanyak dua jari penuh untuk wajah dan leher.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <i class="bi bi-clock-history text-warning fs-3 me-3"></i>
                                <div>
                                    <strong class="d-block">Reapply Every 2 Hours</strong>
                                    <span class="small text-muted">Sunscreen luntur karena keringat dan minyak. Reapply sangat penting.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <i class="bi bi-brightness-alt-high-fill text-danger fs-3 me-3"></i>
                                <div>
                                    <strong class="d-block">SPF 30 Minimum</strong>
                                    <span class="small text-muted">Pastikan sunscreen Anda memiliki perlindungan Broad Spectrum.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted mb-3"></i>
                <h4>No Sunscreen Scans Found</h4>
                <p class="text-muted">You haven't checked your sunscreen coverage yet.</p>
                <a href="/APSkincek/SkinCheck/public/sunscreen-check" class="btn btn-warning mt-2 fw-bold">Start Sunscreen Check</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../app/views/layouts/footer.php'; ?>
