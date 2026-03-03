<?php
// admin.php - Included by dashboard.php
requireAdmin();
$pdo = getDBConnection();

$msg = '';

// Handle Actions (basic Create/Delete for demonstration)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add_building') {
            $name = trim($_POST['bldg_name'] ?? '');
            $desc = trim($_POST['bldg_desc'] ?? '');
            $svg_id = trim($_POST['bldg_svg_id'] ?? '');
            
            if ($name && $svg_id) {
                $stmt = $pdo->prepare("INSERT INTO buildings (name, description, svg_id) VALUES (?, ?, ?)");
                try {
                    $stmt->execute([$name, $desc, $svg_id]);
                    
                    // Also add as a node for routing
                    $stmt2 = $pdo->prepare("INSERT INTO nodes (id, name, x_coord, y_coord) VALUES (?, ?, ?, ?)");
                    // Default coords, admin would edit later or specify
                    $stmt2->execute([$svg_id, $name, 0, 0]);
                    
                    $msg = "<div class='alert success'>Building added successfully.</div>";
                } catch(PDOException $e) {
                    $msg = "<div class='alert error'>Error: " . $e->getMessage() . "</div>";
                }
            }
        } else if ($action === 'delete_building') {
            $id = $_POST['id'];
            $pdo->prepare("DELETE FROM buildings WHERE id = ?")->execute([$id]);
            $msg = "<div class='alert success'>Building deleted.</div>";
            
        } else if ($action === 'add_path') {
            $src = $_POST['src'];
            $tgt = $_POST['tgt'];
            $dist = (float)$_POST['dist'];
            
            if ($src !== $tgt && $dist > 0) {
                // Ensure nodes exist first
                $n1 = $pdo->query("SELECT id FROM nodes WHERE id = '$src'")->fetch();
                $n2 = $pdo->query("SELECT id FROM nodes WHERE id = '$tgt'")->fetch();
                
                if ($n1 && $n2) {
                    $stmt = $pdo->prepare("INSERT INTO edges (source_node, target_node, distance) VALUES (?, ?, ?)");
                    $stmt->execute([$src, $tgt, $dist]);
                    $msg = "<div class='alert success'>Path added.</div>";
                } else {
                    $msg = "<div class='alert error'>Error: Source or target node doesn't exist.</div>";
                }
            }
        } else if ($action === 'upload_map') {
            $msg = "<div class='alert success'>Map image uploaded and updated successfully. (Simulated)</div>";
        }
    }
}

$buildings = $pdo->query("SELECT * FROM buildings ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$nodes = $pdo->query("SELECT id, name FROM nodes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$paths = $pdo->query("SELECT * FROM edges ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .admin-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media(max-width: 992px) {
        .admin-grid { grid-template-columns: 1fr; }
    }

    .admin-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }

    .admin-title {
        color: var(--primary);
        font-family: 'Poppins', sans-serif;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--bg);
        padding-bottom: 0.5rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    th, td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    th {
        font-weight: 600;
        color: var(--primary);
        background: var(--bg);
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        color: white;
    }

    .btn-danger { background: #ef4444; }
    .btn-danger:hover { background: #dc2626; }

    .form-group-admin { margin-bottom: 1rem; }
    .form-group-admin label { display: block; margin-bottom: 0.4rem; font-weight: 500; font-size: 0.9rem; }
    .form-control-admin { 
        width: 100%; padding: 0.6rem; 
        border: 1px solid var(--border); border-radius: 6px; 
        font-family: inherit; font-size: 0.9rem;
    }

    .btn-admin {
        background: var(--accent);
        color: white;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }
    .btn-admin:hover { background: var(--accent-hover); }

    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
    .alert.success { background: #dcfce3; color: #166534; }
    .alert.error { background: #fee2e2; color: #991b1b; }
</style>

<div>
    <?php echo $msg; ?>

    <div class="admin-grid">
        <!-- Add Building Form -->
        <div class="admin-card">
            <h3 class="admin-title">Add New Building</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_building">
                <div class="form-group-admin">
                    <label>Building Name</label>
                    <input type="text" name="bldg_name" class="form-control-admin" required>
                </div>
                <div class="form-group-admin">
                    <label>Description</label>
                    <textarea name="bldg_desc" class="form-control-admin" rows="3"></textarea>
                </div>
                <div class="form-group-admin">
                    <label>SVG ID (must match map SVG)</label>
                    <input type="text" name="bldg_svg_id" class="form-control-admin" required autocomplete="off">
                </div>
                <button type="submit" class="btn-admin">Save Building</button>
            </form>
        </div>

        <!-- Add Path Form -->
        <div class="admin-card">
            <h3 class="admin-title">Add New Path (Edge)</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_path">
                <div class="form-group-admin">
                    <label>Source Node</label>
                    <select name="src" class="form-control-admin" required>
                        <?php foreach($nodes as $n) echo "<option value='{$n['id']}'>{$n['name']} ({$n['id']})</option>"; ?>
                    </select>
                </div>
                <div class="form-group-admin">
                    <label>Target Node</label>
                    <select name="tgt" class="form-control-admin" required>
                        <?php foreach($nodes as $n) echo "<option value='{$n['id']}'>{$n['name']} ({$n['id']})</option>"; ?>
                    </select>
                </div>
                <div class="form-group-admin">
                    <label>Distance (units)</label>
                    <input type="number" step="0.1" name="dist" class="form-control-admin" required>
                </div>
                <button type="submit" class="btn-admin">Save Path</button>
            </form>
        </div>
    </div>

    <!-- Map Upload -->
    <div class="admin-card">
        <h3 class="admin-title">Update SVG Map</h3>
        <p style="font-size:0.9rem; color: var(--text-light); margin-bottom: 1rem;">Upload a new SVG file to replace the current campus map layout. Make sure the SVG IDs match your database entries.</p>
        <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: center;">
            <input type="hidden" name="action" value="upload_map">
            <input type="file" name="map_svg" accept=".svg" class="form-control-admin" style="max-width: 300px;">
            <button type="submit" class="btn-admin">Upload Map</button>
        </form>
    </div>

    <!-- Management Tables -->
    <div class="admin-card">
        <h3 class="admin-title">Manage Buildings</h3>
        <div style="overflow-x: auto;">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>SVG ID</th>
                    <th>Action</th>
                </tr>
                <?php foreach($buildings as $b): ?>
                    <tr>
                        <td><?php echo $b['id']; ?></td>
                        <td><?php echo htmlspecialchars($b['name']); ?></td>
                        <td><?php echo htmlspecialchars($b['svg_id']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete_building">
                                <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                <button type="submit" class="btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
