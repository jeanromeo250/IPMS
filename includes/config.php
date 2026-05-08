<?php
// ============================================================
// config.php — IPMS Database & App Configuration
// ============================================================
// Reads from environment variables (Docker) OR falls back to localhost defaults
define('DB_HOST',    getenv('DB_HOST') ?: 'localhost');
define('DB_NAME',    getenv('DB_NAME') ?: 'ipms_db');
define('DB_USER',    getenv('DB_USER') ?: 'root');
define('DB_PASS',    getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── PDO Connection (Singleton Pattern) ───────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('
            <div style="font-family:sans-serif;background:#060C18;color:#FF3D57;
                        display:flex;align-items:center;justify-content:center;
                        min-height:100vh;flex-direction:column;gap:12px;padding:20px;text-align:center">
                <div style="font-size:40px">❌</div>
                <div style="font-size:22px;font-weight:700;color:#F0F4FF">Database Connection Failed</div>
                <div style="background:#111E35;padding:16px 24px;border-radius:10px;
                            font-family:monospace;font-size:13px;color:#FF7A00;max-width:600px">
                    '.$e->getMessage().'
                </div>
                <div style="color:#8899BB;font-size:14px;max-width:480px;line-height:1.7">
                    Open <strong style="color:#F0F4FF">includes/config.php</strong><br/>
                    Set the correct <strong style="color:#F0F4FF">DB_USER</strong> and
                    <strong style="color:#F0F4FF">DB_PASS</strong><br/>
                    and make sure MySQL is running in XAMPP.
                </div>
            </div>');
        }
    }
    return $pdo;
}

// ── Session ──────────────────────────────────────────────────
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('IPMS_SESSION');
        session_start();
    }
}
function currentUser(): ?array {
    startSession();
    return $_SESSION['ipms_user'] ?? null;
}
function requireLogin(): void {
    if (!currentUser()) {
        header('Location: index.php?page=login');
        exit;
    }
}
function requireRole(array $roles): void {
    requireLogin();
    $u = currentUser();
    if (!in_array($u['role_name'], $roles, true)) {
        header('Location: index.php?page=dashboard');
        exit;
    }
}

// ── Role-Based Page Access ───────────────────────────────────
function canAccess(string $page): bool {
    $u = currentUser();
    if (!$u) return false;
    $map = [
        'ADMIN'             => ['dashboard','imports','products','hscodes','suppliers',
                                'payments','inventory','reports','users','profile'],
        'IMPORTER'          => ['dashboard','imports','products','hscodes','suppliers','profile'],
        'FINANCE_OFFICER'   => ['dashboard','payments','reports','imports','profile'],
        'WAREHOUSE_MANAGER' => ['dashboard','inventory','imports','profile'],
        'CUSTOMER'          => ['dashboard','products','hscodes','profile'],
    ];
    return in_array($page, $map[$u['role_name']] ?? [], true);
}

// ── Tax Calculator (Strategy Pattern) ────────────────────────
function calcTax(float $val, float $duty, float $vat, float $excise): array {
    $dutyAmt   = round($val * $duty / 100, 2);
    $vatAmt    = round(($val + $dutyAmt) * $vat / 100, 2);
    $exciseAmt = round($val * $excise / 100, 2);
    $total     = $dutyAmt + $vatAmt + $exciseAmt;
    return [
        'taxable_value'    => $val,
        'import_duty_rate' => $duty,    'import_duty_amt'  => $dutyAmt,
        'vat_rate'         => $vat,     'vat_amt'          => $vatAmt,
        'excise_duty_rate' => $excise,  'excise_duty_amt'  => $exciseAmt,
        'total_tax'        => $total,   'total_payable'    => $val + $total,
    ];
}

// ── Helpers ──────────────────────────────────────────────────
function usd(float $n): string    { return '$'.number_format($n, 2); }
function fmtNum(float $n): string { return number_format($n, 2); }
function h(string $s): string     { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function flash(string $msg, string $type = 'success'): void {
    startSession();
    $_SESSION['ipms_flash'] = ['msg' => $msg, 'type' => $type];
}
function getFlash(): ?array {
    startSession();
    $f = $_SESSION['ipms_flash'] ?? null;
    unset($_SESSION['ipms_flash']);
    return $f;
}

function nextRef(): string {
    $n = (int)db()->query("SELECT COUNT(*) FROM import_records")->fetchColumn() + 1;
    return 'IMP-'.date('Y').'-'.str_pad($n, 5, '0', STR_PAD_LEFT);
}
function nextReceipt(): string {
    $n = (int)db()->query("SELECT COUNT(*) FROM payments")->fetchColumn() + 1;
    return 'RCT-'.date('Y').'-'.str_pad($n, 5, '0', STR_PAD_LEFT);
}
function logAction(string $action): void {
    $u = currentUser();
    db()->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)")
       ->execute([$u['user_id'] ?? null, $action]);
}
