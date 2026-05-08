<?php
$u      = currentUser();
$flash  = getFlash();
$isLogin = ($page === 'login');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>IPMS · Import Product Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
/* ══ TOKENS ══════════════════════════════════════════════════ */
:root{
  --bg:#060C18;--s1:#0D1626;--s2:#111E35;--s3:#172240;
  --b1:rgba(255,255,255,.07);--b2:rgba(255,255,255,.12);
  --cyan:#00D4FF;--cyan2:#0099CC;--green:#00E5A0;
  --orange:#FF7A00;--red:#FF3D57;--gold:#FFB800;--purple:#8B5CF6;
  --text:#F0F4FF;--t2:#8899BB;
  --fw:'Outfit',sans-serif;--mono:'JetBrains Mono',monospace;
  --sw:256px;--hh:60px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--fw);background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
button,input,select,textarea{font-family:var(--fw);outline:none}
button{cursor:pointer;border:none}
::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-thumb{background:var(--s3);border-radius:4px}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(ellipse 80% 60% at 10% 20%,rgba(0,212,255,.06) 0%,transparent 60%),
             radial-gradient(ellipse 60% 80% at 90% 80%,rgba(0,229,160,.04) 0%,transparent 60%)}

/* ══ LOGIN ═══════════════════════════════════════════════════ */
.login-screen{display:flex;align-items:center;justify-content:center;min-height:100vh;position:relative;z-index:10;padding:20px}
.login-wrap{display:grid;grid-template-columns:1fr 1fr;width:900px;min-height:520px;border-radius:24px;overflow:hidden;border:1px solid var(--b2);box-shadow:0 40px 100px rgba(0,0,0,.6);animation:loginIn .7s cubic-bezier(.22,1,.36,1)}
@keyframes loginIn{from{opacity:0;transform:translateY(24px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.login-left{background:linear-gradient(135deg,#0D1626,#0A1A38,#091429);padding:52px 48px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden}
.login-left::before{content:'';position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,.12),transparent 70%);pointer-events:none}
.login-left::after{content:'';position:absolute;bottom:-60px;left:-60px;width:250px;height:250px;border-radius:50%;background:radial-gradient(circle,rgba(0,229,160,.08),transparent 70%);pointer-events:none}
.lb-brand{position:relative;z-index:1}
.lb-icon{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:18px;box-shadow:0 8px 24px rgba(0,212,255,.25)}
.lb-brand h1{font-size:28px;font-weight:800;letter-spacing:-.5px}
.lb-brand h1 span{color:var(--cyan)}
.lb-brand p{color:var(--t2);font-size:13px;margin-top:5px}
.lb-tag{position:relative;z-index:1}
.lb-rra{display:inline-flex;align-items:center;gap:8px;padding:7px 14px;border-radius:8px;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);font-size:12px;color:var(--cyan);font-weight:600;margin-bottom:14px}
.lb-tag h2{font-size:19px;font-weight:700;line-height:1.4}
.lb-tag p{font-size:13px;color:var(--t2);margin-top:7px;line-height:1.6}
.login-right{background:var(--s1);padding:52px 48px;display:flex;flex-direction:column;justify-content:center}
.login-right h3{font-size:22px;font-weight:700;margin-bottom:5px}
.login-right>p{font-size:13px;color:var(--t2);margin-bottom:30px}
.fl{display:flex;flex-direction:column;gap:7px;margin-bottom:18px}
.fl label{font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--t2)}
.fl input{padding:13px 16px;background:var(--s2);border:1.5px solid var(--b2);border-radius:10px;color:var(--text);font-size:14px;transition:.2s}
.fl input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,212,255,.08)}
.btn-login{width:100%;padding:14px;background:linear-gradient(135deg,var(--cyan2),var(--cyan));color:#000;font-size:15px;font-weight:700;border-radius:10px;cursor:pointer;transition:.2s;border:none;font-family:var(--fw)}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(0,212,255,.4)}
.login-err{padding:12px 15px;border-radius:9px;margin-bottom:16px;background:rgba(255,61,87,.1);border:1px solid rgba(255,61,87,.25);color:var(--red);font-size:13px;font-weight:500}
.login-ok{padding:12px 15px;border-radius:9px;margin-bottom:16px;background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.25);color:var(--green);font-size:13px;font-weight:500}

/* ══ APP SHELL ═══════════════════════════════════════════════ */
.app{display:flex;height:100vh;overflow:hidden;position:relative;z-index:1}

/* SIDEBAR */
.sidebar{width:var(--sw);background:var(--s1);border-right:1px solid var(--b1);display:flex;flex-direction:column;height:100vh;position:fixed;left:0;top:0;z-index:200;transition:transform .3s}
.sb-head{padding:18px 16px 14px;border-bottom:1px solid var(--b1)}
.sb-brand{display:flex;align-items:center;gap:11px}
.sb-ico{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sb-nm{font-size:16px;font-weight:800;letter-spacing:-.3px}
.sb-nm span{color:var(--cyan)}
.sb-sub{font-size:10px;color:var(--t2);letter-spacing:.4px;margin-top:1px}
.sb-nav{flex:1;overflow-y:auto;padding-bottom:8px}
.nav-sec{padding:14px 16px 5px;font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--t2)}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:1px 7px;border-radius:10px;cursor:pointer;color:var(--t2);font-size:13.5px;font-weight:500;transition:.15s;position:relative;text-decoration:none}
.nav-item:hover{background:var(--s2);color:var(--text)}
.nav-item.active{background:rgba(0,212,255,.1);color:var(--cyan);border:1px solid rgba(0,212,255,.15)}
.nav-item.active::before{content:'';position:absolute;left:-7px;top:50%;transform:translateY(-50%);width:3px;height:60%;background:var(--cyan);border-radius:0 3px 3px 0}
.nav-ico{font-size:15px;width:20px;text-align:center;flex-shrink:0}
.nav-bdg{margin-left:auto;background:var(--orange);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:20px}
.sb-foot{padding:12px;border-top:1px solid var(--b1)}
.usr-card{display:flex;align-items:center;gap:9px;padding:9px 11px;border-radius:10px;background:var(--s2);border:1px solid var(--b1);cursor:pointer;transition:.15s;text-decoration:none;margin-bottom:7px}
.usr-card:hover{border-color:var(--b2)}
.usr-av{width:33px;height:33px;border-radius:8px;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#000;flex-shrink:0}
.usr-info{flex:1;min-width:0}
.usr-nm{font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.usr-rl{font-size:10px;color:var(--t2);margin-top:1px}
.logout-btn{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:rgba(255,61,87,.08);border:1px solid rgba(255,61,87,.15);border-radius:9px;color:var(--red);font-size:13px;font-weight:600;cursor:pointer;transition:.15s;font-family:var(--fw)}
.logout-btn:hover{background:rgba(255,61,87,.18);border-color:rgba(255,61,87,.3)}

/* HAMBURGER */
.hamburger{display:none;position:fixed;top:12px;left:12px;z-index:300;width:38px;height:38px;background:var(--s1);border:1px solid var(--b2);border-radius:9px;align-items:center;justify-content:center;font-size:18px;cursor:pointer;color:var(--text)}
.overlay-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:150;backdrop-filter:blur(3px)}

/* MAIN */
.main{margin-left:var(--sw);display:flex;flex-direction:column;height:100vh;overflow:hidden;flex:1}
.topbar{height:var(--hh);background:rgba(6,12,24,.88);backdrop-filter:blur(12px);border-bottom:1px solid var(--b1);display:flex;align-items:center;padding:0 24px;gap:14px;position:sticky;top:0;z-index:100;flex-shrink:0}
.tb-left{flex:1}
.tb-left h2{font-size:17px;font-weight:700;letter-spacing:-.3px}
.tb-left p{font-size:12px;color:var(--t2);margin-top:1px}
.tb-right{display:flex;align-items:center;gap:9px}
.content{padding:22px 24px;overflow-y:auto;flex:1}

/* ══ BUTTONS ═════════════════════════════════════════════════ */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 17px;border-radius:9px;font-size:13px;font-weight:600;transition:.15s;cursor:pointer;text-decoration:none;border:none;white-space:nowrap;font-family:var(--fw)}
.btn-primary{background:linear-gradient(135deg,var(--cyan2),var(--cyan));color:#000;box-shadow:0 2px 12px rgba(0,212,255,.2)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 5px 18px rgba(0,212,255,.35)}
.btn-ghost{background:var(--s2);color:var(--t2);border:1px solid var(--b2)}
.btn-ghost:hover{color:var(--text);background:var(--s3)}
.btn-success{background:rgba(0,229,160,.12);color:var(--green);border:1px solid rgba(0,229,160,.2)}
.btn-success:hover{background:rgba(0,229,160,.22)}
.btn-danger{background:rgba(255,61,87,.1);color:var(--red);border:1px solid rgba(255,61,87,.2)}
.btn-danger:hover{background:rgba(255,61,87,.2)}
.btn-warning{background:rgba(255,122,0,.1);color:var(--orange);border:1px solid rgba(255,122,0,.2)}
.btn-warning:hover{background:rgba(255,122,0,.2)}
.btn-sm{padding:6px 12px;font-size:12px;border-radius:7px}
.btn-xs{padding:4px 9px;font-size:11px;border-radius:6px}

/* ══ CARDS ═══════════════════════════════════════════════════ */
.card{background:var(--s1);border:1px solid var(--b1);border-radius:16px;margin-bottom:16px}
.card-head{padding:16px 20px;border-bottom:1px solid var(--b1);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.card-title{font-size:15px;font-weight:700;letter-spacing:-.2px}
.card-body{padding:20px}
.card-body-p0{padding:0}

/* ══ STATS ═══════════════════════════════════════════════════ */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.stat{background:var(--s1);border:1px solid var(--b1);border-radius:16px;padding:20px;position:relative;overflow:hidden}
.stat::after{content:'';position:absolute;width:90px;height:90px;border-radius:50%;top:-25px;right:-25px;opacity:.12;pointer-events:none}
.stat.sc::after{background:var(--cyan)}.stat.sg::after{background:var(--green)}
.stat.so::after{background:var(--orange)}.stat.sp::after{background:var(--purple)}
.stat-ico{font-size:22px;margin-bottom:12px}
.stat-val{font-size:26px;font-weight:800;letter-spacing:-1px;line-height:1}
.stat-val.c{color:var(--cyan)}.stat-val.g{color:var(--green)}.stat-val.o{color:var(--orange)}.stat-val.p{color:var(--purple)}
.stat-lbl{font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--t2);margin-top:5px}
.stat-tr{font-size:11px;color:var(--green);font-weight:600;margin-top:7px}

/* ══ TABLE ═══════════════════════════════════════════════════ */
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{padding:10px 13px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--t2);border-bottom:1px solid var(--b1);white-space:nowrap}
tbody tr{border-bottom:1px solid var(--b1);transition:background .12s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:rgba(255,255,255,.02)}
tbody td{padding:12px 13px;vertical-align:middle}
.mono{font-family:var(--mono);font-size:12px;font-weight:500}

/* ══ BADGES ══════════════════════════════════════════════════ */
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.3px;white-space:nowrap}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0}
.b-pending{background:rgba(255,122,0,.12);color:var(--orange)}
.b-verified{background:rgba(139,92,246,.12);color:var(--purple)}
.b-approved{background:rgba(0,212,255,.1);color:var(--cyan)}
.b-cleared{background:rgba(0,229,160,.1);color:var(--green)}
.b-rejected{background:rgba(255,61,87,.1);color:var(--red)}
.b-completed{background:rgba(0,229,160,.1);color:var(--green)}
.b-failed{background:rgba(255,61,87,.1);color:var(--red)}
.b-info{background:rgba(0,212,255,.08);color:var(--cyan)}
.b-active{background:rgba(0,229,160,.1);color:var(--green)}
.b-inactive{background:rgba(255,61,87,.1);color:var(--red)}
.hs-chip{font-family:var(--mono);font-size:11px;font-weight:600;padding:3px 9px;border-radius:6px;background:rgba(0,212,255,.08);color:var(--cyan);border:1px solid rgba(0,212,255,.15)}

/* ══ FORMS ═══════════════════════════════════════════════════ */
.fg{display:flex;flex-direction:column;gap:6px}
.fg label{font-size:11px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--t2)}
.fg input,.fg select,.fg textarea{padding:10px 13px;background:var(--s2);border:1.5px solid var(--b2);border-radius:9px;color:var(--text);font-size:14px;transition:.2s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,212,255,.07)}
.fg select option{background:var(--s2)}
.fg textarea{resize:vertical;min-height:75px}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:13px}
.fgrid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:13px}
.mb14{margin-bottom:14px}.mt14{margin-top:14px}.mt20{margin-top:20px}

/* FILTER ROW */
.filter-row{display:flex;align-items:center;gap:9px;margin-bottom:14px;flex-wrap:wrap}
.sw{flex:1;position:relative;min-width:180px}
.sw input{width:100%;padding:9px 13px 9px 36px;background:var(--s2);border:1.5px solid var(--b2);border-radius:9px;color:var(--text);font-size:13px;transition:.2s}
.sw input:focus{border-color:var(--cyan)}
.sw-ico{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--t2);font-size:13px}
.filter-row select{padding:9px 13px;background:var(--s2);border:1.5px solid var(--b2);border-radius:9px;color:var(--text);font-size:13px}
.filter-row select option{background:var(--s2)}

/* ALERTS */
.alert{padding:11px 15px;border-radius:10px;font-size:13px;display:flex;align-items:flex-start;gap:9px;margin-bottom:14px;line-height:1.5}
.alert-cyan{background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.15);color:var(--cyan)}
.alert-green{background:rgba(0,229,160,.08);border:1px solid rgba(0,229,160,.15);color:var(--green)}
.alert-orange{background:rgba(255,122,0,.08);border:1px solid rgba(255,122,0,.15);color:var(--orange)}
.alert-red{background:rgba(255,61,87,.08);border:1px solid rgba(255,61,87,.15);color:var(--red)}

/* TAX BOX */
.tax-box{background:var(--s2);border:1px solid var(--b2);border-radius:12px;padding:16px;margin-top:14px}
.tax-box-hd{font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--cyan);margin-bottom:11px}
.tax-row-item{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--b1);font-size:13px}
.tax-row-item:last-child{border:none;font-weight:700;font-size:15px;padding-top:10px;color:var(--cyan)}
.tax-row-item .lbl{color:var(--t2)}

/* BAR CHART */
.bar-list{display:flex;flex-direction:column;gap:11px}
.bar-row{display:flex;align-items:center;gap:11px;font-size:12px}
.bar-lbl{width:105px;color:var(--t2);text-align:right;font-size:11px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bar-track{flex:1;height:8px;background:var(--s2);border-radius:20px;overflow:hidden;border:1px solid var(--b1)}
.bar-fill{height:100%;border-radius:20px}
.bar-val{width:80px;font-weight:600;font-size:11px;color:var(--t2)}

/* GRID LAYOUTS */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.dash-grid{display:grid;grid-template-columns:3fr 2fr;gap:14px}
.ip{display:flex;flex-direction:column;gap:3px}
.ip .lbl{font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--t2)}
.ip .val{font-size:14px;font-weight:500}

/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.72);backdrop-filter:blur(5px);z-index:500;display:none;align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex;animation:fIn .2s ease}
@keyframes fIn{from{opacity:0}to{opacity:1}}
.modal{background:var(--s1);border:1px solid var(--b2);border-radius:18px;width:100%;max-width:660px;max-height:90vh;overflow-y:auto;box-shadow:0 40px 80px rgba(0,0,0,.6);animation:mIn .25s cubic-bezier(.22,1,.36,1)}
@keyframes mIn{from{opacity:0;transform:translateY(16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.modal-hd{padding:18px 22px;border-bottom:1px solid var(--b1);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--s1);z-index:10}
.modal-hd h3{font-size:16px;font-weight:700;letter-spacing:-.2px}
.modal-close{width:30px;height:30px;border-radius:7px;background:var(--s2);border:1px solid var(--b1);color:var(--t2);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:.15s}
.modal-close:hover{background:rgba(255,61,87,.15);color:var(--red)}
.modal-bd{padding:22px}
.modal-ft{padding:14px 22px;border-top:1px solid var(--b1);display:flex;justify-content:flex-end;gap:9px}

/* TOAST */
.toast-wrap{position:fixed;bottom:22px;right:22px;z-index:9999;pointer-events:none}
.toast{padding:13px 18px;border-radius:12px;background:var(--s1);border:1px solid var(--b2);box-shadow:0 10px 30px rgba(0,0,0,.4);font-size:13px;font-weight:500;animation:tIn .3s cubic-bezier(.22,1,.36,1);max-width:320px;line-height:1.5;pointer-events:all}
@keyframes tIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.toast.tg{border-left:3px solid var(--green)}.toast.tr{border-left:3px solid var(--red)}.toast.to{border-left:3px solid var(--orange)}

/* PROFILE TABS */
.pav{width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,var(--cyan2),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#000;margin:0 auto 8px}
.tab-bar{display:flex;gap:4px;margin-bottom:20px;background:var(--s2);padding:4px;border-radius:10px;border:1px solid var(--b1)}
.tab-btn{flex:1;padding:8px;border-radius:7px;border:none;font-family:var(--fw);font-size:13px;font-weight:600;cursor:pointer;transition:.15s;color:var(--t2);background:none}
.tab-btn.active{background:var(--s3);color:var(--text)}
.tab-panel{display:none}.tab-panel.active{display:block}

/* ══ RESPONSIVE ══════════════════════════════════════════════ */
@media(max-width:900px){
  .stats-row{grid-template-columns:repeat(2,1fr)}
  .dash-grid{grid-template-columns:1fr}
  .fgrid{grid-template-columns:1fr}
  .fgrid3{grid-template-columns:1fr 1fr}
  .grid2{grid-template-columns:1fr}
  .login-wrap{grid-template-columns:1fr;width:100%;max-width:460px;min-height:auto}
  .login-left{display:none}
}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .main{margin-left:0}
  .hamburger{display:flex}
  .overlay-bg.open{display:block}
  .topbar{padding:0 14px 0 56px}
  .content{padding:16px 14px}
  .stats-row{grid-template-columns:1fr 1fr}
  .fgrid3{grid-template-columns:1fr}
  .filter-row{flex-direction:column;align-items:stretch}
  .sw{min-width:unset}
}
@media(max-width:480px){
  .stats-row{grid-template-columns:1fr}
  .stat-val{font-size:22px}
  .login-wrap{border-radius:16px}
}
</style>
</head>
<body>

<?php if($isLogin): ?>
<!-- ══ LOGIN ═══════════════════════════════════════════════ -->
<div class="login-screen">
<div class="login-wrap">
  <div class="login-left">
    <div class="lb-brand">
      <div class="lb-icon">🛃</div>
      <h1>IP<span>MS</span></h1>
      <p>Import Product Management System</p>
    </div>
    <div class="lb-tag">
      <div class="lb-rra">🇷🇼 Rwanda Revenue Authority</div>
      <h2>Customs &amp; Border Management Division</h2>
      <p>Digital platform for managing imported goods, automated tax calculation, payment processing and inventory tracking.</p>
    </div>
  </div>
  <div class="login-right">
    <h3>Welcome back</h3>
    <p>Sign in to access your portal</p>
    <?php if($flash): ?>
      <div class="<?= $flash['type']==='error'?'login-err':'login-ok' ?>">
        <?= $flash['type']==='error'?'❌':'✅' ?> <?= h($flash['msg']) ?>
      </div>
    <?php endif; ?>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="login"/>
      <div class="fl">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="your@rra.gov.rw" required autofocus
               value="<?= h($_POST['email'] ?? '') ?>"/>
      </div>
      <div class="fl">
        <label>Password</label>
        <input type="text" name="password" placeholder="Enter your password" required/>
      </div>
      <button type="submit" class="btn-login">Sign In →</button>
    </form>
  </div>
</div>
</div>

<?php else: ?>
<!-- ══ MAIN APP ════════════════════════════════════════════ -->
<?php
$pendingCount = 0;
try {
    $pendingCount = (int)db()->query("SELECT COUNT(*) FROM import_records WHERE status='PENDING'")->fetchColumn();
} catch(Exception $e){}

$navItems = [
    ['dashboard', '◉', 'Dashboard',      'OVERVIEW'],
    ['imports',   '◫', 'Import Records',  'OPERATIONS'],
    ['products',  '◈', 'Products',        null],
    ['hscodes',   '⊞', 'HS Codes',        null],
    ['suppliers', '◎', 'Suppliers',       null],
    ['payments',  '◌', 'Payments',        'FINANCE'],
    ['reports',   '◐', 'Reports',         null],
    ['inventory', '◧', 'Inventory',       'WAREHOUSE'],
    ['users',     '◉', 'User Management', 'ADMIN'],
    ['profile',   '👤','My Profile',      null],
];
$lastSection = '';
?>
<button class="hamburger" onclick="toggleSidebar()">☰</button>
<div class="overlay-bg" id="overlay-bg" onclick="toggleSidebar()"></div>

<div class="app">
 <nav class="sidebar" id="sidebar">
  <div class="sb-head">
    <div class="sb-brand">
      <div class="sb-ico">🛃</div>
      <div>
        <div class="sb-nm">IP<span>MS</span></div>
        <div class="sb-sub">RRA · Customs Division</div>
      </div>
    </div>
  </div>
  <div class="sb-nav">
    <?php foreach($navItems as [$pid,$icon,$label,$section]): ?>
      <?php if(!canAccess($pid)) continue; ?>
      <?php if($section && $section !== $lastSection): $lastSection=$section; ?>
        <div class="nav-sec"><?= $section ?></div>
      <?php endif; ?>
      <a href="index.php?page=<?= $pid ?>" class="nav-item <?= $page===$pid?'active':'' ?>">
        <span class="nav-ico"><?= $icon ?></span>
        <?= $label ?>
        <?php if($pid==='imports' && $pendingCount>0): ?>
          <span class="nav-bdg"><?= $pendingCount ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
  <div class="sb-foot">
    <a href="index.php?page=profile" class="usr-card">
      <div class="usr-av"><?= strtoupper($u['full_name'][0]) ?></div>
      <div class="usr-info">
        <div class="usr-nm"><?= h($u['full_name']) ?></div>
        <div class="usr-rl"><?= str_replace('_',' ',$u['role_name']) ?></div>
      </div>
      <span style="font-size:11px;color:var(--t2)">✏️</span>
    </a>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="logout"/>
      <button type="submit" class="logout-btn">⏏ Sign Out</button>
    </form>
  </div>
 </nav>

 <div class="main">
  <header class="topbar">
    <div class="tb-left">
      <h2 id="pg-title"><?= ucfirst($page) ?></h2>
      <p><?= h($u['full_name']) ?> · <?= str_replace('_',' ',$u['role_name']) ?></p>
    </div>
    <div class="tb-right"></div>
  </header>

  <?php if($flash): ?>
  <div class="toast-wrap" id="tw">
    <div class="toast <?= $flash['type']==='error'?'tr':($flash['type']==='warning'?'to':'tg') ?>">
      <?= $flash['type']==='error'?'❌':($flash['type']==='warning'?'⚠️':'✅') ?>
      <?= h($flash['msg']) ?>
    </div>
  </div>
  <script>setTimeout(()=>{const w=document.getElementById('tw');if(w){w.style.opacity='0';w.style.transition='opacity .4s';setTimeout(()=>w.remove(),400)}},3500)</script>
  <?php endif; ?>

  <div class="content">

  <?php
  // ══════════════════════════════════════════════════════════
  // DASHBOARD
  // ══════════════════════════════════════════════════════════
  if($page === 'dashboard'):
    $totalImports = db()->query("SELECT COUNT(*) FROM import_records")->fetchColumn();
    $pendingImp   = db()->query("SELECT COUNT(*) FROM import_records WHERE status='PENDING'")->fetchColumn();
    $totalTax     = db()->query("SELECT COALESCE(SUM(amount_paid),0) FROM payments WHERE payment_status='COMPLETED'")->fetchColumn();
    $clearedImp   = db()->query("SELECT COUNT(*) FROM import_records WHERE status='CLEARED'")->fetchColumn();
    $recentImps   = db()->query("SELECT ir.*,p.product_name,u.full_name AS importer_name FROM import_records ir JOIN products p ON ir.product_id=p.product_id JOIN users u ON ir.importer_id=u.user_id ORDER BY ir.created_at DESC LIMIT 6")->fetchAll();
    $catTax       = db()->query("SELECT h.category,COALESCE(SUM(tc.total_tax),0) AS tax FROM hs_codes h LEFT JOIN products p ON p.hs_code_id=h.hs_code_id LEFT JOIN import_records ir ON ir.product_id=p.product_id LEFT JOIN tax_calculations tc ON tc.import_id=ir.import_id GROUP BY h.category ORDER BY tax DESC")->fetchAll();
    $lowStock     = db()->query("SELECT i.*,p.product_name,p.unit_of_measure FROM inventory i JOIN products p ON i.product_id=p.product_id WHERE i.stock_quantity<=i.reorder_level")->fetchAll();
    $maxTax       = max(array_column($catTax,'tax') ?: [1]);
  ?>
  <div class="stats-row">
    <div class="stat sc"><div class="stat-ico">📦</div><div class="stat-val c"><?= $totalImports ?></div><div class="stat-lbl">Total Imports</div><div class="stat-tr">↑ Active quarter</div></div>
    <div class="stat so"><div class="stat-ico">⏳</div><div class="stat-val o"><?= $pendingImp ?></div><div class="stat-lbl">Pending Review</div></div>
    <div class="stat sg"><div class="stat-ico">💰</div><div class="stat-val g"><?= usd($totalTax) ?></div><div class="stat-lbl">Tax Collected</div><div class="stat-tr">↑ Revenue on track</div></div>
    <div class="stat sp"><div class="stat-ico">✅</div><div class="stat-val p"><?= $clearedImp ?></div><div class="stat-lbl">Cleared Imports</div></div>
  </div>
  <div class="dash-grid">
    <div class="card">
      <div class="card-head"><span class="card-title">Recent Import Records</span><a href="index.php?page=imports" class="btn btn-ghost btn-sm">View all →</a></div>
      <div class="card-body-p0"><div class="tbl-wrap"><table>
        <thead><tr><th>Reference</th><th>Product</th><th>Importer</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach($recentImps as $ri): ?>
        <tr>
          <td><span class="mono" style="color:var(--cyan)"><?= h($ri['reference_no']) ?></span></td>
          <td style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= h($ri['product_name']) ?></td>
          <td style="font-size:12px;color:var(--t2)"><?= h($ri['importer_name']) ?></td>
          <td style="font-size:12px;color:var(--t2)"><?= $ri['import_date'] ?></td>
          <td><span class="badge b-<?= strtolower($ri['status']) ?>"><?= $ri['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$recentImps): ?><tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t2)">No records yet</td></tr><?php endif; ?>
        </tbody>
      </table></div></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:14px">
      <div class="card">
        <div class="card-head"><span class="card-title">Tax Revenue by Category</span></div>
        <div class="card-body">
          <div class="bar-list">
          <?php foreach($catTax as $ct): if($ct['tax']<=0) continue; ?>
            <div class="bar-row">
              <div class="bar-lbl"><?= h($ct['category']) ?></div>
              <div class="bar-track"><div class="bar-fill" style="width:<?= $maxTax>0?round($ct['tax']/$maxTax*100):0 ?>%;background:linear-gradient(90deg,var(--cyan2),var(--cyan))"></div></div>
              <div class="bar-val"><?= usd($ct['tax']) ?></div>
            </div>
          <?php endforeach; ?>
          </div>
          <div style="margin-top:14px;padding:12px;background:var(--s2);border:1px solid var(--b1);border-radius:10px;text-align:center">
            <div style="font-size:10px;color:var(--t2);letter-spacing:.7px;text-transform:uppercase">Total Tax Revenue</div>
            <div style="font-size:24px;font-weight:800;color:var(--cyan);margin-top:3px"><?= usd($totalTax) ?></div>
          </div>
        </div>
      </div>
      <?php if($lowStock): ?>
      <div class="card">
        <div class="card-head"><span class="card-title" style="color:var(--orange)">⚠ Low Stock Alert</span><span style="font-size:12px;color:var(--orange);font-weight:600"><?= count($lowStock) ?> items</span></div>
        <div class="card-body-p0">
          <?php foreach($lowStock as $ls): ?>
          <div style="padding:11px 16px;border-bottom:1px solid var(--b1);display:flex;justify-content:space-between;align-items:center">
            <div><div style="font-size:13px;font-weight:600"><?= h($ls['product_name']) ?></div><div style="font-size:11px;color:var(--t2)"><?= h($ls['warehouse_location']??'') ?></div></div>
            <div style="text-align:right"><div style="font-size:14px;font-weight:700;color:var(--red)"><?= fmtNum($ls['stock_quantity']) ?></div><div style="font-size:10px;color:var(--t2)">min <?= fmtNum($ls['reorder_level']) ?></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php
  // ══════════════════════════════════════════════════════════
  // IMPORTS
  // ══════════════════════════════════════════════════════════
  elseif($page === 'imports'):
    $statusF = $_GET['status'] ?? '';
    $search  = $_GET['q'] ?? '';
    $where   = ['1=1']; $params = [];
    if($u['role_name']==='IMPORTER')          { $where[]='ir.importer_id=?'; $params[]=$u['user_id']; }
    elseif($u['role_name']==='WAREHOUSE_MANAGER') { $where[]="ir.status IN('CLEARED','APPROVED')"; }
    elseif($u['role_name']==='FINANCE_OFFICER')   { $where[]="ir.status IN('APPROVED','CLEARED','VERIFIED','PENDING')"; }
    if($statusF)  { $where[]='ir.status=?'; $params[]=$statusF; }
    if($search)   { $where[]="(ir.reference_no LIKE ? OR p.product_name LIKE ? OR ir.country_of_origin LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; $params[]="%$search%"; }
    $sql  = "SELECT ir.*,(ir.quantity*ir.unit_price) AS total_value,p.product_name,p.unit_of_measure,h.code AS hs_code,u.full_name AS importer_name FROM import_records ir JOIN products p ON ir.product_id=p.product_id JOIN hs_codes h ON p.hs_code_id=h.hs_code_id JOIN users u ON ir.importer_id=u.user_id WHERE ".implode(' AND ',$where)." ORDER BY ir.created_at DESC";
    $stmt = db()->prepare($sql); $stmt->execute($params); $imports = $stmt->fetchAll();
    $products = db()->query("SELECT product_id,product_name FROM products WHERE is_active=1 ORDER BY product_name")->fetchAll();
  ?>
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px">
    <button class="btn btn-primary" onclick="openModal('import-modal')">＋ New Import</button>
  </div>
  <?php endif; ?>
  <div class="card">
    <div class="card-head"><span class="card-title">Import Records (<?= count($imports) ?>)</span></div>
    <div class="card-body" style="padding:16px">
      <form method="GET" action="index.php">
        <input type="hidden" name="page" value="imports"/>
        <div class="filter-row">
          <div class="sw"><span class="sw-ico">🔍</span><input type="text" name="q" value="<?= h($search) ?>" placeholder="Search reference, product, origin…"/></div>
          <select name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <?php foreach(['PENDING','VERIFIED','APPROVED','CLEARED','REJECTED'] as $s): ?>
            <option <?= $statusF===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-ghost btn-sm">Search</button>
          <a href="index.php?page=imports" class="btn btn-ghost btn-sm">Clear</a>
        </div>
      </form>
      <div class="tbl-wrap"><table>
        <thead><tr><th>Reference</th><th>Product</th><th>HS Code</th><th>Qty</th><th>Unit Price</th><th>Total Value</th><th>Origin</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($imports as $imp): ?>
        <tr>
          <td><span class="mono" style="color:var(--cyan)"><?= h($imp['reference_no']) ?></span></td>
          <td style="font-weight:600;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= h($imp['product_name']) ?></td>
          <td><span class="hs-chip"><?= h($imp['hs_code']) ?></span></td>
          <td><?= fmtNum($imp['quantity']) ?> <span style="font-size:11px;color:var(--t2)"><?= h($imp['unit_of_measure']) ?></span></td>
          <td><?= usd($imp['unit_price']) ?></td>
          <td style="font-weight:700"><?= usd($imp['total_value']) ?></td>
          <td style="font-size:12px;color:var(--t2)"><?= h($imp['country_of_origin']) ?></td>
          <td style="font-size:12px;color:var(--t2)"><?= $imp['import_date'] ?></td>
          <td><span class="badge b-<?= strtolower($imp['status']) ?>"><?= $imp['status'] ?></span></td>
          <td>
            <div style="display:flex;gap:5px;flex-wrap:wrap">
              <button class="btn btn-ghost btn-xs" onclick="viewImport(<?= htmlspecialchars(json_encode($imp),ENT_QUOTES) ?>)">View</button>
              <?php if(in_array($u['role_name'],['ADMIN','FINANCE_OFFICER']) && !in_array($imp['status'],['CLEARED','REJECTED'])): ?>
              <button class="btn btn-warning btn-xs" onclick="openStatusModal(<?= $imp['import_id'] ?>,'<?= $imp['status'] ?>')">↑ Status</button>
              <?php endif; ?>
              <?php if(in_array($u['role_name'],['ADMIN','FINANCE_OFFICER']) && $imp['status']==='APPROVED'): ?>
              <button class="btn btn-success btn-xs" onclick="openPayModal(<?= $imp['import_id'] ?>,'<?= h($imp['reference_no']) ?>','<?= $imp['total_value'] ?>')">💳 Pay</button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$imports): ?><tr><td colspan="10" style="text-align:center;padding:30px;color:var(--t2)">No records found</td></tr><?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>

  <!-- Add Import Modal -->
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div class="modal-overlay" id="import-modal" onclick="if(event.target===this)closeModal('import-modal')">
  <div class="modal">
    <div class="modal-hd"><h3>➕ New Import Record</h3><button class="modal-close" onclick="closeModal('import-modal')">✕</button></div>
    <div class="modal-bd">
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="add_import"/>
        <div class="fgrid mb14">
          <div class="fg"><label>Product</label><select name="product_id" required onchange="fetchTax()"><option value="">Select product…</option><?php foreach($products as $pr): ?><option value="<?= $pr['product_id'] ?>"><?= h($pr['product_name']) ?></option><?php endforeach; ?></select></div>
          <div class="fg"><label>Country of Origin</label><input type="text" name="origin" placeholder="e.g. China" required/></div>
          <div class="fg"><label>Quantity</label><input type="number" name="quantity" step="0.01" placeholder="100" required id="imp-qty" onchange="fetchTax()"/></div>
          <div class="fg"><label>Unit Price (USD)</label><input type="number" name="unit_price" step="0.01" placeholder="850.00" required id="imp-price" onchange="fetchTax()"/></div>
          <div class="fg"><label>Import Date</label><input type="date" name="import_date" value="<?= date('Y-m-d') ?>" required/></div>
          <div class="fg"><label>Border Post</label><select name="border_post"><?php foreach(['Kigali Airport','Rubavu','Rusumo','Kagitumba','Cyanika','Nemba'] as $bp): ?><option><?= $bp ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="fg mb14"><label>Notes (Optional)</label><textarea name="notes" placeholder="Any additional notes…"></textarea></div>
        <div id="tax-preview"></div>
        <div class="modal-ft" style="padding:0;margin-top:14px">
          <button type="button" class="btn btn-ghost" onclick="closeModal('import-modal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Declaration</button>
        </div>
      </form>
    </div>
  </div></div>
  <?php endif; ?>

  <!-- View Import Modal -->
  <div class="modal-overlay" id="view-import-modal" onclick="if(event.target===this)closeModal('view-import-modal')">
  <div class="modal"><div class="modal-hd"><h3 id="vi-title">Import Details</h3><button class="modal-close" onclick="closeModal('view-import-modal')">✕</button></div>
  <div class="modal-bd" id="vi-body"></div>
  <div class="modal-ft"><button class="btn btn-ghost" onclick="closeModal('view-import-modal')">Close</button></div></div></div>

  <!-- Status Modal -->
  <div class="modal-overlay" id="status-modal" onclick="if(event.target===this)closeModal('status-modal')">
  <div class="modal" style="max-width:400px">
    <div class="modal-hd"><h3>Update Import Status</h3><button class="modal-close" onclick="closeModal('status-modal')">✕</button></div>
    <div class="modal-bd">
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="update_status"/>
        <input type="hidden" name="import_id" id="st-id"/>
        <div class="fg mb14"><label>New Status</label><select name="status" id="st-sel"><?php foreach(['PENDING','VERIFIED','APPROVED','REJECTED','CLEARED'] as $s): ?><option><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('status-modal')">Cancel</button><button type="submit" class="btn btn-primary">Update Status</button></div>
      </form>
    </div>
  </div></div>

  <!-- Pay Modal -->
  <div class="modal-overlay" id="pay-modal" onclick="if(event.target===this)closeModal('pay-modal')">
  <div class="modal" style="max-width:460px">
    <div class="modal-hd"><h3>💳 Record Payment</h3><button class="modal-close" onclick="closeModal('pay-modal')">✕</button></div>
    <div class="modal-bd">
      <div class="alert alert-cyan mb14" id="pay-info"></div>
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="add_payment"/>
        <input type="hidden" name="import_id" id="pay-iid"/>
        <div class="fgrid mb14">
          <div class="fg"><label>Amount (USD)</label><input type="number" name="amount" step="0.01" id="pay-amt" required/></div>
          <div class="fg"><label>Payment Method</label><select name="method"><?php foreach(['BANK_TRANSFER','MTN_MOBILE','AIRTEL_MONEY','CASH','CHEQUE'] as $m): ?><option><?= $m ?></option><?php endforeach; ?></select></div>
          <div class="fg"><label>Payment Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required/></div>
          <div class="fg"><label>Bank / Transaction Ref</label><input type="text" name="bank_ref" placeholder="e.g. BK-TXN-12345"/></div>
        </div>
        <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('pay-modal')">Cancel</button><button type="submit" class="btn btn-success">Confirm Payment</button></div>
      </form>
    </div>
  </div></div>

  <?php
  // ══════════════════════════════════════════════════════════
  // PRODUCTS
  // ══════════════════════════════════════════════════════════
  elseif($page === 'products'):
    $products  = db()->query("SELECT p.*,h.code AS hs_code,h.category,h.import_duty_rate,h.vat_rate,h.excise_duty_rate,s.company_name AS supplier_name FROM products p JOIN hs_codes h ON p.hs_code_id=h.hs_code_id LEFT JOIN suppliers s ON p.supplier_id=s.supplier_id ORDER BY p.product_name")->fetchAll();
    $hsCodes   = db()->query("SELECT * FROM hs_codes WHERE is_active=1 ORDER BY code")->fetchAll();
    $suppliers = db()->query("SELECT * FROM suppliers WHERE is_active=1 ORDER BY company_name")->fetchAll();
  ?>
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px"><button class="btn btn-primary" onclick="openModal('prod-modal')">＋ Add Product</button></div>
  <?php endif; ?>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>Product Name</th><th>HS Code</th><th>Category</th><th>Supplier</th><th>UoM</th><th>Import Duty</th><th>VAT</th><th>Excise</th></tr></thead>
    <tbody>
    <?php foreach($products as $pr): ?>
    <tr>
      <td style="font-weight:600"><?= h($pr['product_name']) ?></td>
      <td><span class="hs-chip"><?= h($pr['hs_code']) ?></span></td>
      <td><span class="badge b-info"><?= h($pr['category']) ?></span></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($pr['supplier_name']??'—') ?></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($pr['unit_of_measure']) ?></td>
      <td style="color:var(--orange);font-weight:700"><?= $pr['import_duty_rate'] ?>%</td>
      <td style="color:var(--cyan)"><?= $pr['vat_rate'] ?>%</td>
      <td style="color:var(--red);font-weight:700"><?= $pr['excise_duty_rate'] ?>%</td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div></div></div>
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div class="modal-overlay" id="prod-modal" onclick="if(event.target===this)closeModal('prod-modal')">
  <div class="modal"><div class="modal-hd"><h3>➕ Add Product</h3><button class="modal-close" onclick="closeModal('prod-modal')">✕</button></div>
  <div class="modal-bd"><form method="POST" action="index.php">
    <input type="hidden" name="action" value="add_product"/>
    <div class="fg mb14"><label>Product Name</label><input type="text" name="product_name" placeholder="e.g. iPhone 16 Pro" required/></div>
    <div class="fgrid mb14">
      <div class="fg"><label>HS Code</label><select name="hs_code_id" required><option value="">Select…</option><?php foreach($hsCodes as $h2): ?><option value="<?= $h2['hs_code_id'] ?>"><?= h($h2['code']) ?> — <?= h(substr($h2['description'],0,40)) ?></option><?php endforeach; ?></select></div>
      <div class="fg"><label>Supplier</label><select name="supplier_id"><option value="">— None —</option><?php foreach($suppliers as $s): ?><option value="<?= $s['supplier_id'] ?>"><?= h($s['company_name']) ?></option><?php endforeach; ?></select></div>
      <div class="fg"><label>Unit of Measure</label><select name="uom"><?php foreach(['Unit','Carton','Metric Ton','Litre','Box','Bag (50kg)','Dozen'] as $u2): ?><option><?= $u2 ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="fg mb14"><label>Description</label><textarea name="description" placeholder="Optional description…"></textarea></div>
    <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('prod-modal')">Cancel</button><button type="submit" class="btn btn-primary">Add Product</button></div>
  </form></div></div></div>
  <?php endif; ?>

  <?php
  // ══════════════════════════════════════════════════════════
  // HS CODES
  // ══════════════════════════════════════════════════════════
  elseif($page === 'hscodes'):
    $hsCodes = db()->query("SELECT h.*,(SELECT COUNT(*) FROM products WHERE hs_code_id=h.hs_code_id) AS prod_count FROM hs_codes h ORDER BY h.code")->fetchAll();
  ?>
  <?php if($u['role_name']==='ADMIN'): ?>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px"><button class="btn btn-primary" onclick="openModal('hs-modal')">＋ Add HS Code</button></div>
  <?php endif; ?>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>HS Code</th><th>Description</th><th>Category</th><th>Import Duty</th><th>VAT</th><th>Excise</th><th>Max Rate</th><th>Products</th><?= $u['role_name']==='ADMIN'?'<th>Action</th>':'' ?></tr></thead>
    <tbody>
    <?php foreach($hsCodes as $h2): ?>
    <tr>
      <td><span class="hs-chip"><?= h($h2['code']) ?></span></td>
      <td style="font-size:12px;max-width:200px"><?= h($h2['description']) ?></td>
      <td><span class="badge b-info"><?= h($h2['category']) ?></span></td>
      <td style="font-weight:700;color:var(--orange)"><?= $h2['import_duty_rate'] ?>%</td>
      <td style="color:var(--cyan)"><?= $h2['vat_rate'] ?>%</td>
      <td style="font-weight:700;color:<?= $h2['excise_duty_rate']>0?'var(--red)':'var(--t2)' ?>"><?= $h2['excise_duty_rate'] ?>%</td>
      <td style="font-weight:800;color:var(--cyan)"><?= $h2['import_duty_rate']+$h2['vat_rate']+$h2['excise_duty_rate'] ?>%</td>
      <td><span class="badge b-info"><?= $h2['prod_count'] ?></span></td>
      <?php if($u['role_name']==='ADMIN'): ?>
      <td><button class="btn btn-ghost btn-xs" onclick="openEditHS(<?= htmlspecialchars(json_encode($h2),ENT_QUOTES) ?>)">✏️ Edit</button></td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div></div></div>
  <?php if($u['role_name']==='ADMIN'): ?>
  <div class="modal-overlay" id="hs-modal" onclick="if(event.target===this)closeModal('hs-modal')">
  <div class="modal" style="max-width:500px">
    <div class="modal-hd"><h3 id="hs-modal-title">➕ Add HS Code</h3><button class="modal-close" onclick="closeModal('hs-modal')">✕</button></div>
    <div class="modal-bd"><form method="POST" action="index.php">
      <input type="hidden" name="action" id="hs-action" value="add_hscode"/>
      <input type="hidden" name="hs_code_id" id="hs-edit-id" value=""/>
      <div class="fgrid mb14">
        <div class="fg"><label>HS Code</label><input type="text" name="code" id="hs-code" placeholder="e.g. 8471.30" required/></div>
        <div class="fg"><label>Category</label><input type="text" name="category" id="hs-cat" placeholder="e.g. Electronics" required/></div>
      </div>
      <div class="fg mb14"><label>Description</label><input type="text" name="description" id="hs-desc" placeholder="Full description" required/></div>
      <div class="fgrid3 mb14">
        <div class="fg"><label>Import Duty %</label><input type="number" name="duty" id="hs-duty" step="0.01" value="0"/></div>
        <div class="fg"><label>VAT %</label><input type="number" name="vat" id="hs-vat" step="0.01" value="18"/></div>
        <div class="fg"><label>Excise Duty %</label><input type="number" name="excise" id="hs-excise" step="0.01" value="0"/></div>
      </div>
      <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('hs-modal')">Cancel</button><button type="submit" class="btn btn-primary" id="hs-btn">Save HS Code</button></div>
    </form></div>
  </div></div>
  <?php endif; ?>

  <?php
  // ══════════════════════════════════════════════════════════
  // SUPPLIERS
  // ══════════════════════════════════════════════════════════
  elseif($page === 'suppliers'):
    $suppliers = db()->query("SELECT s.*,(SELECT COUNT(*) FROM products WHERE supplier_id=s.supplier_id) AS prod_count FROM suppliers s ORDER BY s.company_name")->fetchAll();
  ?>
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px"><button class="btn btn-primary" onclick="openModal('sup-modal')">＋ Add Supplier</button></div>
  <?php endif; ?>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>Company</th><th>Contact</th><th>Email</th><th>Phone</th><th>Country</th><th>Products</th></tr></thead>
    <tbody>
    <?php foreach($suppliers as $s): ?>
    <tr>
      <td style="font-weight:600"><?= h($s['company_name']) ?></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($s['contact_person']??'—') ?></td>
      <td style="font-size:12px"><?= h($s['email']??'—') ?></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($s['phone']??'—') ?></td>
      <td><span class="badge b-info"><?= h($s['country']) ?></span></td>
      <td><span class="badge b-approved"><?= $s['prod_count'] ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div></div></div>
  <?php if(in_array($u['role_name'],['ADMIN','IMPORTER'])): ?>
  <div class="modal-overlay" id="sup-modal" onclick="if(event.target===this)closeModal('sup-modal')">
  <div class="modal" style="max-width:500px">
    <div class="modal-hd"><h3>➕ Add Supplier</h3><button class="modal-close" onclick="closeModal('sup-modal')">✕</button></div>
    <div class="modal-bd"><form method="POST" action="index.php">
      <input type="hidden" name="action" value="add_supplier"/>
      <div class="fgrid mb14">
        <div class="fg"><label>Company Name</label><input type="text" name="company_name" placeholder="Company Ltd" required/></div>
        <div class="fg"><label>Country</label><input type="text" name="country" placeholder="e.g. Kenya" required/></div>
        <div class="fg"><label>Contact Person</label><input type="text" name="contact" placeholder="Full name"/></div>
        <div class="fg"><label>Email</label><input type="email" name="email" placeholder="email@company.com"/></div>
        <div class="fg"><label>Phone</label><input type="text" name="phone" placeholder="+xxx-xxx-xxx"/></div>
      </div>
      <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('sup-modal')">Cancel</button><button type="submit" class="btn btn-primary">Add Supplier</button></div>
    </form></div>
  </div></div>
  <?php endif; ?>

  <?php
  // ══════════════════════════════════════════════════════════
  // PAYMENTS
  // ══════════════════════════════════════════════════════════
  elseif($page === 'payments'):
    $payments = db()->query("SELECT py.*,ir.reference_no,p.product_name,u.full_name AS verifier FROM payments py JOIN import_records ir ON py.import_id=ir.import_id JOIN products p ON ir.product_id=p.product_id LEFT JOIN users u ON py.verified_by=u.user_id ORDER BY py.created_at DESC")->fetchAll();
    $totalCol = db()->query("SELECT COALESCE(SUM(amount_paid),0) FROM payments WHERE payment_status='COMPLETED'")->fetchColumn();
  ?>
  <div class="stats-row" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat sg"><div class="stat-ico">💰</div><div class="stat-val g"><?= usd($totalCol) ?></div><div class="stat-lbl">Total Collected</div></div>
    <div class="stat sc"><div class="stat-ico">🧾</div><div class="stat-val c"><?= count($payments) ?></div><div class="stat-lbl">Total Receipts</div></div>
    <div class="stat so"><div class="stat-ico">⏳</div><div class="stat-val o"><?= count(array_filter($payments,fn($p)=>$p['payment_status']==='PENDING')) ?></div><div class="stat-lbl">Pending</div></div>
  </div>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>Receipt</th><th>Import Ref</th><th>Product</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Verified By</th></tr></thead>
    <tbody>
    <?php foreach($payments as $py): ?>
    <tr>
      <td><span class="mono" style="color:var(--green)"><?= h($py['receipt_no']) ?></span></td>
      <td><span class="mono" style="color:var(--cyan);font-size:11px"><?= h($py['reference_no']) ?></span></td>
      <td style="font-size:12px;max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= h($py['product_name']) ?></td>
      <td style="font-weight:700"><?= usd($py['amount_paid']) ?></td>
      <td><span class="badge b-info"><?= str_replace('_',' ',$py['payment_method']) ?></span></td>
      <td style="font-size:12px;color:var(--t2)"><?= $py['payment_date'] ?></td>
      <td><span class="badge b-<?= strtolower($py['payment_status']) ?>"><?= $py['payment_status'] ?></span></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($py['verifier']??'—') ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$payments): ?><tr><td colspan="8" style="text-align:center;padding:30px;color:var(--t2)">No payments yet</td></tr><?php endif; ?>
    </tbody>
  </table></div></div></div>

  <?php
  // ══════════════════════════════════════════════════════════
  // REPORTS
  // ══════════════════════════════════════════════════════════
  elseif($page === 'reports'):
    $totV  = db()->query("SELECT COALESCE(SUM(ir.quantity*ir.unit_price),0) FROM import_records ir")->fetchColumn();
    $totT  = db()->query("SELECT COALESCE(SUM(amount_paid),0) FROM payments WHERE payment_status='COMPLETED'")->fetchColumn();
    $totI  = db()->query("SELECT COUNT(*) FROM import_records")->fetchColumn();
    $bySt  = db()->query("SELECT status,COUNT(*) AS cnt FROM import_records GROUP BY status ORDER BY cnt DESC")->fetchAll();
    $byHS  = db()->query("SELECT h.code,h.category,h.import_duty_rate,h.vat_rate,h.excise_duty_rate,(SELECT COUNT(*) FROM import_records ir2 JOIN products p2 ON ir2.product_id=p2.product_id WHERE p2.hs_code_id=h.hs_code_id) AS cnt,(SELECT COALESCE(SUM(tc2.total_tax),0) FROM tax_calculations tc2 JOIN import_records ir2 ON tc2.import_id=ir2.import_id JOIN products p2 ON ir2.product_id=p2.product_id WHERE p2.hs_code_id=h.hs_code_id) AS tax FROM hs_codes h ORDER BY tax DESC")->fetchAll();
    $topP  = db()->query("SELECT p.product_name,COUNT(ir.import_id) AS cnt,COALESCE(SUM(ir.quantity*ir.unit_price),0) AS val FROM import_records ir JOIN products p ON ir.product_id=p.product_id GROUP BY p.product_id,p.product_name ORDER BY val DESC LIMIT 5")->fetchAll();
    $maxSt = max(array_column($bySt,'cnt') ?: [1]);
    $maxTP = max(array_column($topP,'val') ?: [1]);
  ?>
  <div class="stats-row">
    <div class="stat sc"><div class="stat-ico">📦</div><div class="stat-val c"><?= $totI ?></div><div class="stat-lbl">Total Imports</div></div>
    <div class="stat sp"><div class="stat-ico">💵</div><div class="stat-val p"><?= usd($totV) ?></div><div class="stat-lbl">Total Import Value</div></div>
    <div class="stat sg"><div class="stat-ico">🏦</div><div class="stat-val g"><?= usd($totT) ?></div><div class="stat-lbl">Tax Collected</div></div>
    <div class="stat so"><div class="stat-ico">📊</div><div class="stat-val o"><?= $totV>0?round($totT/$totV*100,1):0 ?>%</div><div class="stat-lbl">Effective Tax Rate</div></div>
  </div>
  <div class="grid2">
    <div class="card">
      <div class="card-head"><span class="card-title">Import Status Distribution</span></div>
      <div class="card-body"><div class="bar-list">
        <?php foreach($bySt as $st): ?>
        <div class="bar-row">
          <div class="bar-lbl"><?= $st['status'] ?></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= $totI>0?round($st['cnt']/$totI*100):0 ?>%;background:linear-gradient(90deg,var(--cyan2),var(--cyan))"></div></div>
          <div class="bar-val"><?= $st['cnt'] ?> (<?= $totI>0?round($st['cnt']/$totI*100):0 ?>%)</div>
        </div>
        <?php endforeach; ?>
      </div></div>
    </div>
    <div class="card">
      <div class="card-head"><span class="card-title">Top Products by Import Value</span></div>
      <div class="card-body"><div class="bar-list">
        <?php foreach($topP as $tp): ?>
        <div class="bar-row">
          <div class="bar-lbl"><?= h($tp['product_name']) ?></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= $maxTP>0?round($tp['val']/$maxTP*100):0 ?>%;background:linear-gradient(90deg,var(--orange),var(--gold))"></div></div>
          <div class="bar-val"><?= usd($tp['val']) ?></div>
        </div>
        <?php endforeach; ?>
      </div></div>
    </div>
  </div>
  <div class="card mt20"><div class="card-head"><span class="card-title">Tax Report per HS Code</span></div>
  <div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>HS Code</th><th>Category</th><th>Import Duty</th><th>VAT</th><th>Excise</th><th>Imports</th><th>Tax Generated</th></tr></thead>
    <tbody>
    <?php foreach($byHS as $bh): ?>
    <tr>
      <td><span class="hs-chip"><?= h($bh['code']) ?></span></td>
      <td><span class="badge b-info"><?= h($bh['category']) ?></span></td>
      <td style="color:var(--orange);font-weight:700"><?= $bh['import_duty_rate'] ?>%</td>
      <td style="color:var(--cyan)"><?= $bh['vat_rate'] ?>%</td>
      <td style="color:var(--red);font-weight:700"><?= $bh['excise_duty_rate'] ?>%</td>
      <td><?= $bh['cnt'] ?></td>
      <td style="font-weight:800;color:var(--green)"><?= usd($bh['tax']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div></div></div>

  <?php
  // ══════════════════════════════════════════════════════════
  // INVENTORY
  // ══════════════════════════════════════════════════════════
  elseif($page === 'inventory'):
    $inventory = db()->query("SELECT i.*,p.product_name,p.unit_of_measure FROM inventory i JOIN products p ON i.product_id=p.product_id ORDER BY p.product_name")->fetchAll();
  ?>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>Product</th><th>Stock Qty</th><th>Reorder Level</th><th>Location</th><th>Health</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($inventory as $inv): 
      $pct = ($inv['reorder_level']>0) ? min(100,round($inv['stock_quantity']/$inv['reorder_level']*100)) : 100;
      $col = $pct<50?'var(--red)':($pct<100?'var(--orange)':'var(--green)');
    ?>
    <tr>
      <td style="font-weight:600"><?= h($inv['product_name']) ?></td>
      <td style="font-weight:700;color:<?= $col ?>"><?= fmtNum($inv['stock_quantity']) ?> <span style="font-size:11px;color:var(--t2)"><?= h($inv['unit_of_measure']) ?></span></td>
      <td style="color:var(--t2);font-size:12px"><?= fmtNum($inv['reorder_level']) ?></td>
      <td style="font-size:12px;color:var(--t2)"><?= h($inv['warehouse_location']??'') ?></td>
      <td style="min-width:120px">
        <div style="display:flex;align-items:center;gap:7px">
          <div style="flex:1;height:6px;background:var(--s2);border-radius:20px;overflow:hidden">
            <div style="height:100%;width:<?= $pct ?>%;background:<?= $col ?>;border-radius:20px"></div>
          </div>
          <span style="font-size:11px;color:<?= $col ?>;font-weight:700;min-width:30px"><?= $pct ?>%</span>
        </div>
      </td>
      <td><button class="btn btn-ghost btn-xs" onclick="openStockModal(<?= $inv['product_id'] ?>,'<?= h($inv['product_name']) ?>',<?= $inv['stock_quantity'] ?>,<?= $inv['reorder_level'] ?>,'<?= h($inv['warehouse_location']??'') ?>')">Update</button></td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$inventory): ?><tr><td colspan="6" style="text-align:center;padding:30px;color:var(--t2)">No inventory records yet</td></tr><?php endif; ?>
    </tbody>
  </table></div></div></div>
  <!-- Stock Modal -->
  <div class="modal-overlay" id="stock-modal" onclick="if(event.target===this)closeModal('stock-modal')">
  <div class="modal" style="max-width:440px">
    <div class="modal-hd"><h3 id="sk-title">Update Stock</h3><button class="modal-close" onclick="closeModal('stock-modal')">✕</button></div>
    <div class="modal-bd">
      <div class="alert alert-cyan mb14" id="sk-info"></div>
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="update_stock"/>
        <input type="hidden" name="product_id" id="sk-pid"/>
        <div class="fgrid mb14">
          <div class="fg"><label>Stock Quantity</label><input type="number" name="qty" id="sk-qty" step="0.01" required/></div>
          <div class="fg"><label>Reorder Level</label><input type="number" name="reorder" id="sk-re" step="0.01" required/></div>
        </div>
        <div class="fg mb14"><label>Warehouse Location</label><input type="text" name="location" id="sk-loc"/></div>
        <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('stock-modal')">Cancel</button><button type="submit" class="btn btn-primary">Update Stock</button></div>
      </form>
    </div>
  </div></div>

  <?php
  // ══════════════════════════════════════════════════════════
  // USERS (ADMIN ONLY)
  // ══════════════════════════════════════════════════════════
  elseif($page === 'users'):
    $allUsers = db()->query("SELECT u.*,r.role_name FROM users u JOIN roles r ON u.role_id=r.role_id ORDER BY u.created_at DESC")->fetchAll();
    $roles    = db()->query("SELECT * FROM roles ORDER BY role_id")->fetchAll();
    $active2  = count(array_filter($allUsers,fn($x)=>$x['is_active']));
  ?>
  <div style="display:flex;justify-content:flex-end;margin-bottom:14px"><button class="btn btn-primary" onclick="openModal('add-user-modal')">＋ Add User</button></div>
  <div class="stats-row" style="grid-template-columns:repeat(3,1fr);margin-bottom:14px">
    <div class="stat sc"><div class="stat-ico">👥</div><div class="stat-val c"><?= count($allUsers) ?></div><div class="stat-lbl">Total Users</div></div>
    <div class="stat sg"><div class="stat-ico">✅</div><div class="stat-val g"><?= $active2 ?></div><div class="stat-lbl">Active</div></div>
    <div class="stat so"><div class="stat-ico">🚫</div><div class="stat-val o"><?= count($allUsers)-$active2 ?></div><div class="stat-lbl">Deactivated</div></div>
  </div>
  <div class="card"><div class="card-body-p0"><div class="tbl-wrap"><table>
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($allUsers as $au): $isSelf=((int)$au['user_id']===(int)$u['user_id']); ?>
    <tr style="opacity:<?= $au['is_active']?1:.55 ?>">
      <td>
        <div style="display:flex;align-items:center;gap:9px">
          <div style="width:33px;height:33px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;background:<?= $au['is_active']?'linear-gradient(135deg,var(--cyan2),var(--cyan))':'var(--s3)' ?>;color:<?= $au['is_active']?'#000':'var(--t2)' ?>"><?= strtoupper($au['full_name'][0]) ?></div>
          <div style="font-weight:600;font-size:13px"><?= h($au['full_name']) ?><?= $isSelf?' <span style="font-size:10px;color:var(--cyan);background:rgba(0,212,255,.1);padding:1px 6px;border-radius:4px;margin-left:4px">You</span>':'' ?></div>
        </div>
      </td>
      <td style="font-size:12px;color:var(--t2);font-family:var(--mono)"><?= h($au['email']) ?></td>
      <td><span class="badge b-info"><?= str_replace('_',' ',$au['role_name']) ?></span></td>
      <td><?= $au['is_active']?'<span class="badge b-active">ACTIVE</span>':'<span class="badge b-inactive">INACTIVE</span>' ?></td>
      <td style="font-size:11px;color:var(--t2)"><?= $au['last_login']??'Never' ?></td>
      <td>
        <div style="display:flex;gap:5px;flex-wrap:wrap">
          <button class="btn btn-ghost btn-xs" onclick="openEditUser(<?= $au['user_id'] ?>,'<?= h($au['full_name']) ?>','<?= h($au['email']) ?>',<?= $au['role_id'] ?>)">✏️ Edit</button>
          <?php if(!$isSelf): ?>
          <form method="POST" action="index.php" style="display:inline">
            <input type="hidden" name="action" value="toggle_user"/>
            <input type="hidden" name="user_id" value="<?= $au['user_id'] ?>"/>
            <button type="submit" class="btn btn-xs <?= $au['is_active']?'btn-warning':'btn-success' ?>" onclick="return confirm('<?= $au['is_active']?'Deactivate':'Activate' ?> this user?')"><?= $au['is_active']?'🚫 Deactivate':'✅ Activate' ?></button>
          </form>
          <button class="btn btn-ghost btn-xs" onclick="openResetPwd(<?= $au['user_id'] ?>,'<?= h($au['full_name']) ?>')">🔑 Reset Pwd</button>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div></div></div>

  <!-- Add User Modal -->
  <div class="modal-overlay" id="add-user-modal" onclick="if(event.target===this)closeModal('add-user-modal')">
  <div class="modal" style="max-width:500px">
    <div class="modal-hd"><h3>➕ Add New User</h3><button class="modal-close" onclick="closeModal('add-user-modal')">✕</button></div>
    <div class="modal-bd"><form method="POST" action="index.php">
      <input type="hidden" name="action" value="add_user"/>
      <div class="fgrid mb14">
        <div class="fg"><label>Full Name</label><input type="text" name="full_name" placeholder="e.g. John Doe" required/></div>
        <div class="fg"><label>Email</label><input type="email" name="email" placeholder="email@rra.gov.rw" required/></div>
        <div class="fg"><label>Role</label><select name="role_id" required><?php foreach($roles as $r): ?><option value="<?= $r['role_id'] ?>"><?= str_replace('_',' ',$r['role_name']) ?></option><?php endforeach; ?></select></div>
        <div class="fg"><label>Password</label><input type="password" name="password" placeholder="Min 6 characters" required minlength="6"/></div>
      </div>
      <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('add-user-modal')">Cancel</button><button type="submit" class="btn btn-primary">Create User</button></div>
    </form></div>
  </div></div>

  <!-- Edit User Modal -->
  <div class="modal-overlay" id="edit-user-modal" onclick="if(event.target===this)closeModal('edit-user-modal')">
  <div class="modal" style="max-width:480px">
    <div class="modal-hd"><h3>✏️ Edit User</h3><button class="modal-close" onclick="closeModal('edit-user-modal')">✕</button></div>
    <div class="modal-bd"><form method="POST" action="index.php">
      <input type="hidden" name="action" value="edit_user"/>
      <input type="hidden" name="user_id" id="eu-id"/>
      <div class="fgrid mb14">
        <div class="fg"><label>Full Name</label><input type="text" name="full_name" id="eu-name" required/></div>
        <div class="fg"><label>Email</label><input type="email" name="email" id="eu-email" required/></div>
      </div>
      <div class="fg mb14"><label>Role</label><select name="role_id" id="eu-role"><?php foreach($roles as $r): ?><option value="<?= $r['role_id'] ?>"><?= str_replace('_',' ',$r['role_name']) ?></option><?php endforeach; ?></select></div>
      <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('edit-user-modal')">Cancel</button><button type="submit" class="btn btn-primary">💾 Save Changes</button></div>
    </form></div>
  </div></div>

  <!-- Reset Password Modal -->
  <div class="modal-overlay" id="reset-pwd-modal" onclick="if(event.target===this)closeModal('reset-pwd-modal')">
  <div class="modal" style="max-width:420px">
    <div class="modal-hd"><h3>🔑 Reset Password</h3><button class="modal-close" onclick="closeModal('reset-pwd-modal')">✕</button></div>
    <div class="modal-bd">
      <div class="alert alert-cyan mb14" id="rp-info"></div>
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="reset_password"/>
        <input type="hidden" name="user_id" id="rp-uid"/>
        <div class="fg mb14"><label>New Password</label><input type="password" name="pwd1" placeholder="Min 6 characters" required minlength="6"/></div>
        <div class="fg mb14"><label>Confirm Password</label><input type="password" name="pwd2" placeholder="Repeat new password" required/></div>
        <div class="modal-ft" style="padding:0"><button type="button" class="btn btn-ghost" onclick="closeModal('reset-pwd-modal')">Cancel</button><button type="submit" class="btn btn-primary">Reset Password</button></div>
      </form>
    </div>
  </div></div>

  <?php
  // ══════════════════════════════════════════════════════════
  // PROFILE
  // ══════════════════════════════════════════════════════════
  elseif($page === 'profile'):
  ?>
  <div style="max-width:600px;margin:0 auto">
    <div class="card">
      <div class="card-body" style="text-align:center;padding:28px 20px 20px">
        <div class="pav"><?= strtoupper($u['full_name'][0]) ?></div>
        <div style="font-size:18px;font-weight:700;margin-top:6px"><?= h($u['full_name']) ?></div>
        <div style="font-size:13px;color:var(--t2);margin-top:3px"><?= str_replace('_',' ',$u['role_name']) ?></div>
        <span class="badge b-active" style="margin-top:10px;display:inline-flex">Active Account</span>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="tab-bar">
          <button class="tab-btn active" onclick="switchTab('tab-info',this)">Account Info</button>
          <button class="tab-btn" onclick="switchTab('tab-pwd',this)">Change Password</button>
        </div>
        <!-- Account Info Tab -->
        <div class="tab-panel active" id="tab-info">
          <form method="POST" action="index.php">
            <input type="hidden" name="action" value="update_profile"/>
            <div class="fgrid mb14">
              <div class="fg"><label>Full Name</label><input type="text" name="full_name" value="<?= h($u['full_name']) ?>" required/></div>
              <div class="fg"><label>Email Address</label><input type="email" name="email" value="<?= h($u['email']) ?>" required/></div>
            </div>
            <div class="fg mb14"><label>Role (cannot be changed here)</label><input type="text" value="<?= str_replace('_',' ',$u['role_name']) ?>" disabled style="opacity:.5;cursor:not-allowed"/></div>
            <button type="submit" class="btn btn-primary">💾 Save Changes</button>
          </form>
        </div>
        <!-- Change Password Tab -->
        <div class="tab-panel" id="tab-pwd">
          <form method="POST" action="index.php">
            <input type="hidden" name="action" value="change_password"/>
            <div class="fg mb14"><label>Current Password</label><input type="password" name="current_pwd" placeholder="Enter your current password" required/></div>
            <div class="fg mb14"><label>New Password</label><input type="password" name="new_pwd" placeholder="Min 6 characters" required minlength="6"/></div>
            <div class="fg mb14"><label>Confirm New Password</label><input type="password" name="confirm_pwd" placeholder="Repeat new password" required/></div>
            <button type="submit" class="btn btn-primary">🔑 Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php endif; ?>
  </div><!-- /content -->
 </div><!-- /main -->
</div><!-- /app -->
<?php endif; ?>

<script>
function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay-bg').classList.toggle('open')}
function switchTab(id,btn){document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.getElementById(id).classList.add('active');btn.classList.add('active')}
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.querySelectorAll('.modal-overlay.open').forEach(m=>m.classList.remove('open'))})

function viewImport(imp){
  document.getElementById('vi-title').textContent='📋 '+imp.reference_no;
  document.getElementById('vi-body').innerHTML=`
    <div class="grid2 mb14">
      <div class="ip"><span class="lbl">Reference</span><span class="val mono" style="color:var(--cyan)">${imp.reference_no}</span></div>
      <div class="ip"><span class="lbl">Status</span><span class="val"><span class="badge b-${imp.status.toLowerCase()}">${imp.status}</span></span></div>
      <div class="ip"><span class="lbl">Product</span><span class="val">${imp.product_name}</span></div>
      <div class="ip"><span class="lbl">HS Code</span><span class="val"><span class="hs-chip">${imp.hs_code}</span></span></div>
      <div class="ip"><span class="lbl">Importer</span><span class="val">${imp.importer_name}</span></div>
      <div class="ip"><span class="lbl">Country of Origin</span><span class="val">${imp.country_of_origin}</span></div>
      <div class="ip"><span class="lbl">Quantity</span><span class="val">${imp.quantity} ${imp.unit_of_measure}</span></div>
      <div class="ip"><span class="lbl">Unit Price</span><span class="val">$${parseFloat(imp.unit_price).toLocaleString('en-US',{minimumFractionDigits:2})}</span></div>
      <div class="ip"><span class="lbl">Total Value</span><span class="val" style="font-weight:700">$${parseFloat(imp.total_value).toLocaleString('en-US',{minimumFractionDigits:2})}</span></div>
      <div class="ip"><span class="lbl">Import Date</span><span class="val">${imp.import_date}</span></div>
      <div class="ip"><span class="lbl">Border Post</span><span class="val">${imp.border_post||'—'}</span></div>
      <div class="ip"><span class="lbl">Notes</span><span class="val" style="font-size:12px;color:var(--t2)">${imp.notes||'—'}</span></div>
    </div>`;
  openModal('view-import-modal');
}
function openStatusModal(id,current){document.getElementById('st-id').value=id;document.getElementById('st-sel').value=current;openModal('status-modal')}
function openPayModal(id,ref,total){document.getElementById('pay-iid').value=id;document.getElementById('pay-info').innerHTML='Import <strong>'+ref+'</strong> — Total Value: <strong>$'+parseFloat(total).toLocaleString('en-US',{minimumFractionDigits:2})+'</strong>';document.getElementById('pay-amt').value=parseFloat(total).toFixed(2);openModal('pay-modal')}
function openStockModal(pid,name,qty,re,loc){document.getElementById('sk-pid').value=pid;document.getElementById('sk-title').textContent='📦 Update — '+name;document.getElementById('sk-info').innerHTML='Current stock: <strong>'+qty+'</strong>';document.getElementById('sk-qty').value=qty;document.getElementById('sk-re').value=re;document.getElementById('sk-loc').value=loc;openModal('stock-modal')}
function openEditUser(id,name,email,roleId){document.getElementById('eu-id').value=id;document.getElementById('eu-name').value=name;document.getElementById('eu-email').value=email;document.getElementById('eu-role').value=roleId;openModal('edit-user-modal')}
function openResetPwd(id,name){document.getElementById('rp-uid').value=id;document.getElementById('rp-info').innerHTML='Resetting password for <strong>'+name+'</strong>';openModal('reset-pwd-modal')}
function openEditHS(h){document.getElementById('hs-modal-title').textContent='✏️ Edit HS Code';document.getElementById('hs-action').value='edit_hscode';document.getElementById('hs-edit-id').value=h.hs_code_id;document.getElementById('hs-code').value=h.code;document.getElementById('hs-cat').value=h.category;document.getElementById('hs-desc').value=h.description;document.getElementById('hs-duty').value=h.import_duty_rate;document.getElementById('hs-vat').value=h.vat_rate;document.getElementById('hs-excise').value=h.excise_duty_rate;document.getElementById('hs-btn').textContent='Update HS Code';openModal('hs-modal')}

function fetchTax(){
  const pid=document.querySelector('[name="product_id"]')?.value;
  const qty=document.getElementById('imp-qty')?.value;
  const price=document.getElementById('imp-price')?.value;
  const box=document.getElementById('tax-preview');
  if(!box||!pid||!qty||!price||qty<=0||price<=0){if(box)box.innerHTML='';return;}
  const fd=new FormData();fd.append('action','tax_preview');fd.append('product_id',pid);fd.append('qty',qty);fd.append('price',price);
  fetch('index.php',{method:'POST',body:fd}).then(r=>r.json()).then(t=>{
    if(t.error){box.innerHTML='';return;}
    const f=n=>'$'+parseFloat(n).toLocaleString('en-US',{minimumFractionDigits:2});
    box.innerHTML=`<div class="tax-box"><div class="tax-box-hd">📊 Auto Tax Calculation Preview</div>
      <div class="tax-row-item"><span class="lbl">Taxable Value (CIF)</span><span>${f(t.taxable_value)}</span></div>
      <div class="tax-row-item"><span class="lbl">Import Duty (${t.import_duty_rate}%)</span><span>${f(t.import_duty_amt)}</span></div>
      <div class="tax-row-item"><span class="lbl">VAT (${t.vat_rate}%)</span><span>${f(t.vat_amt)}</span></div>
      <div class="tax-row-item"><span class="lbl">Excise Duty (${t.excise_duty_rate}%)</span><span>${f(t.excise_duty_amt)}</span></div>
      <div class="tax-row-item"><span class="lbl">Total Tax</span><span>${f(t.total_tax)}</span></div>
      <div class="tax-row-item"><span class="lbl">TOTAL PAYABLE</span><span>${f(t.total_payable)}</span></div>
    </div>`;
  }).catch(()=>{});
}
</script>
</body>
</html>
