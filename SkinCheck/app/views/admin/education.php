<?php require_once '../app/views/layouts/admin_header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Education Content</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEduModal"><i class="bi bi-plus-lg"></i> Add Content</button>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addEduModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="/APSkincek/SkinCheck/public/admin/education/create" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Publish Content</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3"><label>Category</label><input type="text" name="category" class="form-control" required></div>
        <div class="mb-3"><label>Content (HTML supported)</label><textarea name="content" class="form-control" rows="8" required></textarea></div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Content</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contents)): ?>
                        <?php foreach ($contents as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['title']) ?></td>
                                <td><?= htmlspecialchars($c['category']) ?></td>
                                <td><?= htmlspecialchars($c['author_name']) ?></td>
                                <td><span class="badge <?= $c['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>"><?= $c['status'] ?></span></td>
                                <td>
                                    <form action="/APSkincek/SkinCheck/public/admin/education/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this content?');">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No content found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../app/views/layouts/admin_footer.php'; ?>
