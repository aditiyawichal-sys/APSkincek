<?php require_once '../app/views/layouts/admin_header.php'; ?>
<h2 class="mb-4">All Scans</h2>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($scans)): ?>
                        <?php foreach ($scans as $s): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i', strtotime($s['created_at'])) ?></td>
                                <td><?= htmlspecialchars($s['user_name']) ?></td>
                                <td><?= htmlspecialchars($s['user_email']) ?></td>
                                <td><span class="badge <?= $s['scan_type'] === 'face' ? 'bg-primary' : 'bg-warning text-dark' ?>"><?= $s['scan_type'] ?></span></td>
                                <td><span class="badge bg-success"><?= $s['status'] ?></span></td>
                                <td><a href="/APSkincek/SkinCheck/storage/<?= htmlspecialchars($s['image_url']) ?>" target="_blank" class="btn btn-sm btn-info text-white">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No scans found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../app/views/layouts/admin_footer.php'; ?>
