<?php require_once '../app/views/layouts/admin_header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Products</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="bi bi-plus-lg"></i> Add Product</button>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/APSkincek/SkinCheck/public/admin/products/create" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label>Brand</label><input type="text" name="brand" class="form-control" required></div>
        <div class="mb-3"><label>Category</label><input type="text" name="category" class="form-control" required></div>
        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Product</button>
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
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['brand']) ?></td>
                                <td><?= htmlspecialchars($p['category']) ?></td>
                                <td><span class="badge <?= $p['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= $p['status'] ?></span></td>
                                <td>
                                    <form action="/APSkincek/SkinCheck/public/admin/products/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No products found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../app/views/layouts/admin_footer.php'; ?>
