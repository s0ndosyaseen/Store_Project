<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/db.php';
$pdo = getDB();

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_employee') {
        $user = trim($_POST['new_user'] ?? '');
        $pass = $_POST['new_pass'] ?? '';
        $check = $pdo->prepare("SELECT id FROM accounts WHERE username = ?");
        $check->execute([$user]);
        if ($check->fetch()) {
            $msg = '❌ اسم المستخدم موجود مسبقاً!';
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO accounts (username, password, role) VALUES (?, ?, 'employee')")
                ->execute([$user, $hashed]);
            $msg = "✅ تم إنشاء حساب ($user) بنجاح";
        }
    }
    elseif ($action === 'edit_password') {
        $user = $_POST['target_user'] ?? '';
        $new_pass = $_POST['new_pass'] ?? '';
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE accounts SET password = ? WHERE username = ? AND role = 'employee'")
            ->execute([$hashed, $user]);
        $msg = "✅ تم تحديث كلمة مرور الموظف ($user)";
    }
    elseif ($action === 'delete_emp') {
        $user = $_POST['target_user'] ?? '';
        $pdo->prepare("DELETE FROM accounts WHERE username=? AND role='employee'")
            ->execute([$user]);
        $msg = "✅ تم حذف الحساب بنجاح";
    }
}

$employees = $pdo->query("SELECT * FROM accounts WHERE role = 'employee' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الموظفين - البوصلة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cs.css">
    <style>
        .main { max-width: 95%; margin: 20px auto; }

        .flex-container {
            display: flex;
            gap: 25px;
            align-items: stretch;
            flex-wrap: wrap;
        }

        .flex-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            flex: 1;
            min-width: 350px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .flex-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(135, 103, 35, 0.15);
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #876723;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: right;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            background: #fff;
            box-sizing: border-box;
            font-size: 14px;
            transition: 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #c4a35a;
            box-shadow: 0 0 0 3px rgba(196, 163, 90, 0.1);
        }

        .btn-gold-action {
            background: #c4a35a;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(196, 163, 90, 0.3);
            transition: 0.3s;
        }

        .btn-gold-action:hover {
            background: #ab8e4b;
            transform: scale(1.02);
        }


        .security-note {
            background: #fffcf5;
            border: 1px solid #f8eec8;
            padding: 12px;
            border-radius: 10px;
            font-size: 12px;
            color: #856404;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { color: #876723; border-bottom: 2px solid #f0f0f0; padding: 12px;text-align: center;}
        td { padding: 15px;text-align: center ;border-bottom: 1px solid #fafafa; }

        .actions-cell {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            height: 100%;
            vertical-align: middle;
        }        .btn-mini-edit { background: #3498db; color: white; border: none; padding: 8px; border-radius: 8px; cursor: pointer; }
        .btn-mini-del { background: #e74c3c; color: white; border: none; padding: 8px; border-radius: 8px; cursor: pointer; }

        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center;
        }
        .modal-content { background: white; padding: 25px; border-radius: 15px; width: 380px; }
    </style>
</head>
<body>

<?php include '_nav.php'; ?>

<header>
    <div>
        <button class="open-btn" onclick="openNav()" style="margin-left: 30px;"><i class="fas fa-bars"></i></button>
    </div>
    <h1><i class="fas fa-users-cog"></i> إدارة الموظفين</h1>
    <img src="../images/logo.png" alt="Logo" style="height: 60px;">
</header>

<div class="main">
    <?php if ($msg): ?>
        <p style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; border: 1px solid #c3e6cb; margin-bottom: 20px; text-align: center;"><?= $msg ?></p>
    <?php endif; ?>

    <div class="flex-container">
        <div class="flex-card">
            <h3 style="margin-bottom: 25px; color: #1a1a2e; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <i class="fas fa-user-plus" style="color:#c4a35a"></i> إضافة حساب موظف
            </h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_employee">

                <div class="form-group">
                    <label>اسم المستخدم الجديد</label>
                    <input type="text" name="new_user" placeholder="user name" required>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>كلمة المرور (6 أحرف على الأقل)</label>
                    <input type="password" name="new_pass" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-gold-action" style="margin-top: 25px;">
                    <i class="fas fa-user-check"></i> تفعيل الحساب الجديد
                </button>

                <div class="security-note">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>تنبيه أمني: تأكد من تزويد الموظف بكلمة المرور في مكان آمن.</span>
                </div>
            </form>
        </div>

        <!-- الجدول التفاعلي -->
        <div class="flex-card">
            <h3 style="margin-bottom: 25px; color: #1a1a2e; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <i class="fas fa-users" style="color:#c4a35a"></i> الموظفون الحاليون
            </h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>المستخدم</th>
                        <th>الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td>#<?= $emp['id'] ?></td>
                            <td><strong><?= htmlspecialchars($emp['username']) ?></strong></td>
                            <td class="actions-cell">
                                <button class="btn-mini-edit" title="تغيير كلمة السر" onclick="openEditModal('<?= $emp['username'] ?>')">
                                    <i class="fas fa-key"> تغيير كلمة السر </i>
                                </button>

                                <form method="POST" onsubmit="return confirm('حذف الموظف؟')" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_emp">
                                    <input type="hidden" name="target_user" value="<?= $emp['username'] ?>">
                                    <button type="submit" class="btn-mini-del" title="حذف الحساب">
                                        <i class="fas fa-trash-alt"> حذف </i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- مودال التعديل -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h3 id="modalUserTitle" style="color: #876723; margin-bottom: 20px;"></h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_password">
            <input type="hidden" name="target_user" id="target_user_input">
            <div class="form-group">
                <label>كلمة السر الجديدة</label>
                <input type="password" name="new_pass" required>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-gold-action" style="flex: 2;">تحديث</button>
                <button type="button" onclick="closeEditModal()" style="flex: 1; background: #eee; border: none; border-radius: 12px; cursor:pointer;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNav() { document.getElementById("mySidebar").style.width = "250px"; }
    function openEditModal(username) {
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('modalUserTitle').innerText = 'تعديل كلمة سر: ' + username;
        document.getElementById('target_user_input').value = username;
    }
    function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
    window.onclick = function(e) { if (e.target == document.getElementById('editModal')) closeEditModal(); }
</script>

</body>
</html>