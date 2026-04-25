<?php
// admin_dashboard.php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'cancel') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM appointments";
$params = [];
if ($search) {
    $query .= " WHERE patient_name LIKE ? OR phone LIKE ? OR email LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}
$query .= " ORDER BY appointment_date DESC, appointment_time ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Manage Appointments</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-header {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-card i {
            font-size: 2rem;
            color: #2563eb;
        }
        .appointment-table {
            background: white;
            border-radius: 20px;
            overflow-x: auto;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            font-weight: 600;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-Pending { background: #fef3c7; color: #92400e; }
        .status-Approved { background: #d1fae5; color: #065f46; }
        .status-Cancelled { background: #fee2e2; color: #991b1b; }
        .action-btn {
            padding: 5px 10px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.8rem;
            margin: 0 2px;
            display: inline-block;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-cancel { background: #f59e0b; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .search-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }
        .print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
        }
        @media print {
            .no-print, .admin-header, .search-bar, .stats-cards, .action-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header no-print">
        <h2><i class="fas fa-calendar-check"></i> Admin Dashboard</h2>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']); ?></span>
            <a href="logout.php" class="btn-primary" style="margin-left: 1rem; padding: 6px 16px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="dashboard-container">
        <div class="stats-cards no-print">
            <div class="stat-card"><i class="fas fa-calendar-week"></i><h3>Total</h3><p><?php echo count($appointments); ?></p></div>
            <div class="stat-card"><i class="fas fa-hourglass-half"></i><h3>Pending</h3><p><?php echo count(array_filter($appointments, fn($a)=>$a['status']=='Pending')); ?></p></div>
            <div class="stat-card"><i class="fas fa-check-circle"></i><h3>Approved</h3><p><?php echo count(array_filter($appointments, fn($a)=>$a['status']=='Approved')); ?></p></div>
        </div>
        
        <div class="search-bar no-print">
            <form method="GET" style="flex:1; display:flex; gap:10px;">
                <input type="text" name="search" placeholder="Search by name, phone, email" value="<?php echo htmlspecialchars($search); ?>" style="flex:1; padding:10px; border-radius:30px; border:1px solid #cbd5e1;">
                <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Search</button>
            </form>
            <button onclick="window.print()" class="print-btn"><i class="fas fa-print"></i> Print</button>
        </div>
        
        <div class="appointment-table">
            <table>
                <thead>
                    <tr><th>ID</th><th>Patient</th><th>Age/Gender</th><th>Contact</th><th>Date & Time</th><th>Dept</th><th>Status</th><th class="no-print">Actions</th></tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $app): ?>
                        <tr>
                            <td><?php echo $app['id']; ?></td>
                            <td><?php echo htmlspecialchars($app['patient_name']); ?></td>
                            <td><?php echo $app['age']; ?> / <?php echo $app['gender']; ?></td>
                            <td><?php echo htmlspecialchars($app['phone']); ?><br><small><?php echo htmlspecialchars($app['email']); ?></small></td>
                            <td><?php echo date('d M Y', strtotime($app['appointment_date'])); ?><br><?php echo date('h:i A', strtotime($app['appointment_time'])); ?></td>
                            <td><?php echo $app['department']; ?></td>
                            <td><span class="status-badge status-<?php echo $app['status']; ?>"><?php echo $app['status']; ?></span></td>
                            <td class="no-print">
                                <?php if ($app['status'] == 'Pending'): ?>
                                    <a href="?action=approve&id=<?php echo $app['id']; ?>" class="action-btn btn-approve" onclick="return confirm('Approve this appointment?')"><i class="fas fa-check"></i> Approve</a>
                                <?php endif; ?>
                                <?php if ($app['status'] != 'Cancelled' && $app['status'] != 'Approved'): ?>
                                    <a href="?action=cancel&id=<?php echo $app['id']; ?>" class="action-btn btn-cancel" onclick="return confirm('Cancel this appointment?')"><i class="fas fa-times"></i> Cancel</a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $app['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Permanently delete?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">No appointments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>