<?php
require_once __DIR__.'/includes/config.php';
startSession();

$page   = $_GET['page']  ?? 'login';
$action = $_POST['action'] ?? '';

// ── POST ACTIONS ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // LOGIN
    if ($action === 'login') {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $pwd   = $_POST['password'] ?? '';
        $stmt  = db()->prepare("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id WHERE LOWER(u.email)=? AND u.is_active=1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $loginOk = false;
        if ($user) {
            // Try bcrypt verify first
            if (password_verify($pwd, $user['password_hash'])) {
                $loginOk = true;
            }
            // If bcrypt fails, check if stored hash IS the plain password (fallback)
            elseif ($user['password_hash'] === $pwd) {
                $loginOk = true;
                // Upgrade to bcrypt immediately
                $newHash = password_hash($pwd, PASSWORD_BCRYPT, ['cost' => 10]);
                db()->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([$newHash, $user['user_id']]);
            }
            // Also try MD5 (some old systems)
            elseif ($user['password_hash'] === md5($pwd)) {
                $loginOk = true;
                $newHash = password_hash($pwd, PASSWORD_BCRYPT, ['cost' => 10]);
                db()->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([$newHash, $user['user_id']]);
            }
        }

        if ($loginOk) {
            // Refresh user after possible hash upgrade
            $stmt2 = db()->prepare("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id WHERE u.user_id=?");
            $stmt2->execute([$user['user_id']]);
            $user = $stmt2->fetch();
            $_SESSION['ipms_user'] = $user;
            db()->prepare("UPDATE users SET last_login=NOW() WHERE user_id=?")->execute([$user['user_id']]);
            logAction('Logged in');
            header('Location: index.php?page=dashboard'); exit;
        }
        flash('Invalid email or password. Please try again.', 'error');
        header('Location: index.php?page=login'); exit;
    }

    // LOGOUT
    if ($action === 'logout') {
        logAction('Logged out');
        session_destroy();
        header('Location: index.php?page=login'); exit;
    }

    requireLogin();

    // ADD IMPORT
    if ($action === 'add_import') {
        $u   = currentUser();
        $ref = nextRef();
        $stmt = db()->prepare("INSERT INTO import_records (reference_no,product_id,importer_id,quantity,unit_price,country_of_origin,import_date,border_post,notes) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$ref, $_POST['product_id'], $u['user_id'], $_POST['quantity'], $_POST['unit_price'], $_POST['origin'], $_POST['import_date'], $_POST['border_post'], $_POST['notes'] ?? '']);
        $impId = (int)db()->lastInsertId();
        // Auto-calculate tax
        $hs = db()->prepare("SELECT h.* FROM products p JOIN hs_codes h ON p.hs_code_id=h.hs_code_id WHERE p.product_id=?");
        $hs->execute([$_POST['product_id']]);
        $hs = $hs->fetch();
        $val = (float)$_POST['quantity'] * (float)$_POST['unit_price'];
        $tax = calcTax($val, (float)$hs['import_duty_rate'], (float)$hs['vat_rate'], (float)$hs['excise_duty_rate']);
        $ts  = db()->prepare("INSERT INTO tax_calculations (import_id,taxable_value,import_duty_rate,import_duty_amt,vat_rate,vat_amt,excise_duty_rate,excise_duty_amt,total_tax,total_payable) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $ts->execute([$impId,$tax['taxable_value'],$tax['import_duty_rate'],$tax['import_duty_amt'],$tax['vat_rate'],$tax['vat_amt'],$tax['excise_duty_rate'],$tax['excise_duty_amt'],$tax['total_tax'],$tax['total_payable']]);
        logAction("Added import $ref");
        flash("Import $ref submitted successfully!");
        header('Location: index.php?page=imports'); exit;
    }

    // UPDATE IMPORT STATUS
    if ($action === 'update_status') {
        requireRole(['ADMIN','FINANCE_OFFICER']);
        $valid = ['PENDING','VERIFIED','APPROVED','REJECTED','CLEARED'];
        if (in_array($_POST['status'], $valid, true)) {
            db()->prepare("UPDATE import_records SET status=? WHERE import_id=?")->execute([$_POST['status'], $_POST['import_id']]);
            logAction("Updated import #{$_POST['import_id']} to {$_POST['status']}");
            flash('Import status updated successfully!');
        }
        header('Location: index.php?page=imports'); exit;
    }

    // ADD PAYMENT
    if ($action === 'add_payment') {
        requireRole(['ADMIN','FINANCE_OFFICER']);
        $u   = currentUser();
        $rct = nextReceipt();
        $stmt = db()->prepare("INSERT INTO payments (import_id,receipt_no,amount_paid,payment_method,payment_status,payment_date,bank_reference,verified_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['import_id'], $rct, $_POST['amount'], $_POST['method'], 'COMPLETED', $_POST['payment_date'], $_POST['bank_ref'] ?? '', $u['user_id']]);
        db()->prepare("UPDATE import_records SET status='CLEARED' WHERE import_id=?")->execute([$_POST['import_id']]);
        logAction("Payment $rct recorded for import #{$_POST['import_id']}");
        flash("Payment recorded! Receipt No: $rct");
        header('Location: index.php?page=payments'); exit;
    }

    // ADD PRODUCT
    if ($action === 'add_product') {
        requireRole(['ADMIN','IMPORTER']);
        $stmt = db()->prepare("INSERT INTO products (product_name,hs_code_id,supplier_id,unit_of_measure,description) VALUES (?,?,?,?,?)");
        $stmt->execute([$_POST['product_name'], $_POST['hs_code_id'], $_POST['supplier_id'] ?: null, $_POST['uom'], $_POST['description'] ?? '']);
        logAction("Added product: {$_POST['product_name']}");
        flash('Product added successfully!');
        header('Location: index.php?page=products'); exit;
    }

    // ADD HS CODE
    if ($action === 'add_hscode') {
        requireRole(['ADMIN']);
        $stmt = db()->prepare("INSERT INTO hs_codes (code,description,import_duty_rate,vat_rate,excise_duty_rate,category) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$_POST['code'], $_POST['description'], $_POST['duty'], $_POST['vat'], $_POST['excise'], $_POST['category']]);
        flash('HS Code added successfully!');
        header('Location: index.php?page=hscodes'); exit;
    }

    // EDIT HS CODE
    if ($action === 'edit_hscode') {
        requireRole(['ADMIN']);
        $stmt = db()->prepare("UPDATE hs_codes SET code=?,description=?,import_duty_rate=?,vat_rate=?,excise_duty_rate=?,category=? WHERE hs_code_id=?");
        $stmt->execute([$_POST['code'], $_POST['description'], $_POST['duty'], $_POST['vat'], $_POST['excise'], $_POST['category'], $_POST['hs_code_id']]);
        flash('HS Code updated successfully!');
        header('Location: index.php?page=hscodes'); exit;
    }

    // ADD SUPPLIER
    if ($action === 'add_supplier') {
        requireRole(['ADMIN','IMPORTER']);
        $stmt = db()->prepare("INSERT INTO suppliers (company_name,contact_person,email,phone,country) VALUES (?,?,?,?,?)");
        $stmt->execute([$_POST['company_name'], $_POST['contact'] ?? '', $_POST['email'] ?? '', $_POST['phone'] ?? '', $_POST['country']]);
        flash('Supplier added successfully!');
        header('Location: index.php?page=suppliers'); exit;
    }

    // UPDATE STOCK
    if ($action === 'update_stock') {
        requireRole(['ADMIN','WAREHOUSE_MANAGER']);
        $stmt = db()->prepare("INSERT INTO inventory (product_id,stock_quantity,reorder_level,warehouse_location) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE stock_quantity=VALUES(stock_quantity),reorder_level=VALUES(reorder_level),warehouse_location=VALUES(warehouse_location)");
        $stmt->execute([$_POST['product_id'], $_POST['qty'], $_POST['reorder'], $_POST['location']]);
        logAction("Updated stock for product #{$_POST['product_id']}");
        flash('Stock updated successfully!');
        header('Location: index.php?page=inventory'); exit;
    }

    // ADD USER
    if ($action === 'add_user') {
        requireRole(['ADMIN']);
        $existing = db()->prepare("SELECT user_id FROM users WHERE email=?");
        $existing->execute([$_POST['email']]);
        if ($existing->fetch()) {
            flash('That email address already exists.', 'error');
            header('Location: index.php?page=users'); exit;
        }
        $hash = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = db()->prepare("INSERT INTO users (full_name,email,password_hash,role_id) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['full_name'], $_POST['email'], $hash, $_POST['role_id']]);
        logAction("Created user: {$_POST['email']}");
        flash('User created successfully!');
        header('Location: index.php?page=users'); exit;
    }

    // EDIT USER
    if ($action === 'edit_user') {
        requireRole(['ADMIN']);
        $dup = db()->prepare("SELECT user_id FROM users WHERE email=? AND user_id!=?");
        $dup->execute([$_POST['email'], $_POST['user_id']]);
        if ($dup->fetch()) {
            flash('That email is already used by another user.', 'error');
            header('Location: index.php?page=users'); exit;
        }
        $stmt = db()->prepare("UPDATE users SET full_name=?,email=?,role_id=? WHERE user_id=?");
        $stmt->execute([$_POST['full_name'], $_POST['email'], $_POST['role_id'], $_POST['user_id']]);
        logAction("Edited user #{$_POST['user_id']}");
        flash('User updated successfully!');
        header('Location: index.php?page=users'); exit;
    }

    // TOGGLE USER ACTIVE/INACTIVE
    if ($action === 'toggle_user') {
        requireRole(['ADMIN']);
        $u = currentUser();
        if ((int)$_POST['user_id'] !== (int)$u['user_id']) {
            db()->prepare("UPDATE users SET is_active = 1 - is_active WHERE user_id=?")->execute([$_POST['user_id']]);
            flash('User status updated successfully!');
        } else {
            flash('You cannot deactivate your own account.', 'error');
        }
        header('Location: index.php?page=users'); exit;
    }

    // RESET PASSWORD (admin resets another user)
    if ($action === 'reset_password') {
        requireRole(['ADMIN']);
        if ($_POST['pwd1'] !== $_POST['pwd2']) {
            flash('Passwords do not match.', 'error');
        } elseif (strlen($_POST['pwd1']) < 6) {
            flash('Password must be at least 6 characters.', 'error');
        } else {
            $hash = password_hash($_POST['pwd1'], PASSWORD_BCRYPT, ['cost' => 10]);
            db()->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([$hash, $_POST['user_id']]);
            logAction("Reset password for user #{$_POST['user_id']}");
            flash('Password reset successfully!');
        }
        header('Location: index.php?page=users'); exit;
    }

    // UPDATE MY PROFILE
    if ($action === 'update_profile') {
        $u   = currentUser();
        $dup = db()->prepare("SELECT user_id FROM users WHERE email=? AND user_id!=?");
        $dup->execute([$_POST['email'], $u['user_id']]);
        if ($dup->fetch()) {
            flash('That email is already used by another account.', 'error');
            header('Location: index.php?page=profile'); exit;
        }
        db()->prepare("UPDATE users SET full_name=?,email=? WHERE user_id=?")->execute([$_POST['full_name'], $_POST['email'], $u['user_id']]);
        // Refresh session
        $fresh = db()->prepare("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id WHERE u.user_id=?");
        $fresh->execute([$u['user_id']]);
        $_SESSION['ipms_user'] = $fresh->fetch();
        flash('Profile updated successfully!');
        header('Location: index.php?page=profile'); exit;
    }

    // CHANGE MY PASSWORD
    if ($action === 'change_password') {
        $u    = currentUser();
        $row  = db()->prepare("SELECT password_hash FROM users WHERE user_id=?");
        $row->execute([$u['user_id']]);
        $row  = $row->fetch();
        if (!password_verify($_POST['current_pwd'], $row['password_hash'])) {
            flash('Current password is incorrect.', 'error');
        } elseif ($_POST['new_pwd'] !== $_POST['confirm_pwd']) {
            flash('New passwords do not match.', 'error');
        } elseif (strlen($_POST['new_pwd']) < 6) {
            flash('New password must be at least 6 characters.', 'error');
        } else {
            $hash = password_hash($_POST['new_pwd'], PASSWORD_BCRYPT, ['cost' => 10]);
            db()->prepare("UPDATE users SET password_hash=? WHERE user_id=?")->execute([$hash, $u['user_id']]);
            logAction('Changed own password');
            flash('Password changed successfully!');
        }
        header('Location: index.php?page=profile'); exit;
    }

    // TAX PREVIEW (AJAX)
    if ($action === 'tax_preview') {
        requireLogin();
        header('Content-Type: application/json');
        $stmt = db()->prepare("SELECT h.* FROM products p JOIN hs_codes h ON p.hs_code_id=h.hs_code_id WHERE p.product_id=?");
        $stmt->execute([$_POST['product_id']]);
        $hs = $stmt->fetch();
        if (!$hs) { echo json_encode(['error' => 'Product not found']); exit; }
        $val = (float)$_POST['qty'] * (float)$_POST['price'];
        echo json_encode(calcTax($val, (float)$hs['import_duty_rate'], (float)$hs['vat_rate'], (float)$hs['excise_duty_rate']));
        exit;
    }
}

// ── GUARD: redirect unauthenticated users ────────────────────
if ($page !== 'login') {
    requireLogin();
    if (!canAccess($page)) {
        header('Location: index.php?page=dashboard'); exit;
    }
}

// ── RENDER ───────────────────────────────────────────────────
include __DIR__.'/includes/layout.php';
