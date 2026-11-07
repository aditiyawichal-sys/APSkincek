<?php require_once '../app/views/layouts/admin_header.php'; ?>
<h2 class="mb-4">System Activity Logs</h2>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                                <td><?= htmlspecialchars($log['user_name']) ?> <small class="text-muted">(<?= htmlspecialchars($log['email']) ?>)</small></td>
                                <td><span class="fw-bold"><?= htmlspecialchars($log['action']) ?></span></td>
                                <td><?= htmlspecialchars($log['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No logs found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../app/views/layouts/admin_footer.php'; ?>
