<?php require_once '../app/views/layouts/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Scan History & Progress</h2>
        </div>

        <!-- Trend Indicators -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Skin Analysis Trend</h6>
                        <?php if (isset($analysisTrend) && $analysisTrend !== null): ?>
                            <?php $color = $analysisTrend > 0 ? 'success' : ($analysisTrend < 0 ? 'danger' : 'secondary'); ?>
                            <?php $icon = $analysisTrend > 0 ? 'bi-arrow-up-right' : ($analysisTrend < 0 ? 'bi-arrow-down-right' : 'bi-dash'); ?>
                            <h2 class="text-<?= $color ?> fw-bold mb-0">
                                <i class="bi <?= $icon ?>"></i> <?= $analysisTrend > 0 ? '+' : '' ?><?= $analysisTrend ?>%
                            </h2>
                            <p class="small text-muted mt-1">Change from previous scan</p>
                        <?php else: ?>
                            <h4 class="text-muted fw-bold mb-0">N/A</h4>
                            <p class="small text-muted mt-1">Not enough data to calculate trend</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Sunscreen Coverage Trend</h6>
                        <?php if (isset($sunscreenTrend) && $sunscreenTrend !== null): ?>
                            <?php $color = $sunscreenTrend > 0 ? 'success' : ($sunscreenTrend < 0 ? 'warning' : 'secondary'); ?>
                            <?php $icon = $sunscreenTrend > 0 ? 'bi-arrow-up-right' : ($sunscreenTrend < 0 ? 'bi-arrow-down-right' : 'bi-dash'); ?>
                            <h2 class="text-<?= $color ?> fw-bold mb-0">
                                <i class="bi <?= $icon ?>"></i> <?= $sunscreenTrend > 0 ? '+' : '' ?><?= $sunscreenTrend ?>%
                            </h2>
                            <p class="small text-muted mt-1">Change from previous scan</p>
                        <?php else: ?>
                            <h4 class="text-muted fw-bold mb-0">N/A</h4>
                            <p class="small text-muted mt-1">Not enough data to calculate trend</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Charts -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-graph-up text-primary me-2"></i> Progress Tracking</h5>
                
                <ul class="nav nav-tabs mb-4" id="chartTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="skin-tab" data-bs-toggle="tab" data-bs-target="#skin-chart-pane" type="button" role="tab">Skin Analysis Score</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="sunscreen-tab" data-bs-toggle="tab" data-bs-target="#sunscreen-chart-pane" type="button" role="tab">Sunscreen Coverage Score</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="chartTabsContent">
                    <div class="tab-pane fade show active" id="skin-chart-pane" role="tabpanel" style="height: 300px;">
                        <canvas id="skinChart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="sunscreen-chart-pane" role="tabpanel" style="height: 300px;">
                        <canvas id="sunscreenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-4 border-bottom-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-list-ul text-primary me-2"></i> Scan History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Scan Type</th>
                            <th>Indicator / Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $scan): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark"><?= date('M d, Y', strtotime($scan['created_at'])) ?></span><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($scan['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($scan['scan_type'] === 'face'): ?>
                                            <span class="badge bg-primary rounded-pill px-3 py-2"><i class="bi bi-person me-1"></i> Skin Analysis</span>
                                        <?php elseif ($scan['scan_type'] === 'sunscreen'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-brightness-high me-1"></i> Sunscreen Check</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($scan['scan_type']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($scan['scan_type'] === 'face' && isset($scan['analysis_score'])): ?>
                                            <h6 class="mb-0 fw-bold"><?= $scan['analysis_score'] ?>%</h6>
                                            <small class="text-muted">Overall Score</small>
                                        <?php elseif ($scan['scan_type'] === 'sunscreen' && isset($scan['sunscreen_score'])): ?>
                                            <h6 class="mb-0 fw-bold"><?= $scan['sunscreen_score'] ?>%</h6>
                                            <?php
                                                $details = json_decode($scan['sunscreen_details'], true);
                                                $status = $details['coverage_status'] ?? 'Unknown';
                                            ?>
                                            <small class="text-muted"><?= htmlspecialchars($status) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($scan['scan_type'] === 'face'): ?>
                                            <a href="/APSkincek/SkinCheck/public/skin-analysis" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                                        <?php elseif ($scan['scan_type'] === 'sunscreen'): ?>
                                            <a href="/APSkincek/SkinCheck/public/sunscreen-analysis" class="btn btn-sm btn-outline-warning rounded-pill px-3">View</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No scan history found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inject PHP data into JS
    const analysisData = <?= json_encode($analysisChartData ?? []) ?>;
    const sunscreenData = <?= json_encode($sunscreenChartData ?? []) ?>;

    // Formatting for Chart.js
    const formatData = (data, scoreKey) => {
        return {
            labels: data.map(item => {
                const date = new Date(item.created_at);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            scores: data.map(item => item[scoreKey])
        };
    };

    const analysisParsed = formatData(analysisData, 'overall_score');
    const sunscreenParsed = formatData(sunscreenData, 'coverage_score');

    // Chart Options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: {
                    borderDash: [5, 5]
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // Initialize Skin Analysis Chart
    if (document.getElementById('skinChart') && analysisParsed.scores.length > 0) {
        new Chart(document.getElementById('skinChart'), {
            type: 'line',
            data: {
                labels: analysisParsed.labels,
                datasets: [{
                    label: 'Overall Score',
                    data: analysisParsed.scores,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: commonOptions
        });
    }

    // Initialize Sunscreen Chart
    if (document.getElementById('sunscreenChart') && sunscreenParsed.scores.length > 0) {
        new Chart(document.getElementById('sunscreenChart'), {
            type: 'line',
            data: {
                labels: sunscreenParsed.labels,
                datasets: [{
                    label: 'Coverage Score',
                    data: sunscreenParsed.scores,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffc107',
                    pointRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: commonOptions
        });
    }
});
</script>
<?php require_once '../app/views/layouts/footer.php'; ?>
