<?php require __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CourseHaat BD — Digital Course Marketplace</title>
<meta name="description" content="Facebook Page Monetization and YouTube Monetization — each course only BDT 200. bKash/Nagad payment.">
<link rel="icon" type="image/png" href="assets/favicon.png">
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;line-height:1.7}
  .wrap{max-width:900px;margin:0 auto;padding:0 18px}
  header{display:flex;align-items:center;gap:12px;padding:16px 0}
  header img{height:46px;width:auto}
  .brand{font-size:22px;font-weight:800}
  .brand span{color:#0d9488}
  .tag{margin-left:auto;font-size:14px;color:#475569}
  .hero{text-align:center;padding:36px 0 10px}
  .hero h1{font-size:clamp(30px,6vw,48px);line-height:1.3;margin-bottom:12px}
  .hero h1 span{color:#0d9488}
  .hero p{color:#475569;max-width:640px;margin:0 auto}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin:30px 0}
  .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px}
  .card h2{font-size:22px;margin:10px 0 6px}
  .card p{color:#475569}
  .price{font-size:30px;font-weight:900;margin:12px 0}
  .price small{display:block;font-size:14px;color:#64748b;font-weight:500}
  .buy{display:block;width:100%;text-align:center;background:#0d9488;color:#fff;border:none;border-radius:12px;padding:14px;font-size:18px;font-weight:800;cursor:pointer;font-family:inherit;text-decoration:none;margin-top:12px}
  .buy:hover{background:#0f766e}
  .box{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;margin:22px 0}
  .box h3{font-size:22px;text-align:center;margin-bottom:14px}
  .box ul{list-style:none}
  .box li{padding:7px 0;border-bottom:1px solid #f1f5f9}
  .box li::before{content:'✓ ';color:#0d9488;font-weight:900}
  .how{text-align:center}
  .how p{font-size:17px}
  .paycards{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:16px}
  .paycard{display:flex;align-items:center;gap:12px;border-radius:14px;padding:12px 18px;text-align:left;color:#fff}
  .paycard.bkash{background:#e2136e}
  .paycard.nagad{background:linear-gradient(135deg,#f6921e,#ed752e)}
  .paycard img{width:52px;height:52px;border-radius:12px;object-fit:cover;background:#fff}
  .payname{font-size:14px;font-weight:700;opacity:.95}
  .paynum2{font-size:20px;font-weight:900;letter-spacing:.5px}
  .payicon{width:24px;height:24px;border-radius:6px;vertical-align:-6px;margin:0 4px}
  .paynum img{width:40px;height:40px;border-radius:9px;vertical-align:middle;margin-right:8px}
  .how b{color:#b45309}
  .mission{text-align:center;padding:30px 10px}
  .mission h2{font-size:clamp(28px,5vw,40px);color:#0d9488}
  footer{text-align:center;color:#64748b;font-size:14px;padding:24px 0 36px}
  dialog{border:none;border-radius:16px;padding:0;max-width:440px;width:calc(100% - 32px)}
  dialog::backdrop{background:rgba(0,0,0,.6)}
  .modal{padding:24px}
  .modal label{display:block;font-size:14px;font-weight:700;margin:14px 0 6px}
  .modal input,.modal select{width:100%;padding:12px;border-radius:10px;border:1px solid #cbd5e1;font-size:16px;font-family:inherit}
  .paynum{border-radius:10px;padding:12px;margin-top:14px;display:none;color:#fff}
  .paynum.bkash{background:#e2136e;display:block}
  .paynum.nagad{background:linear-gradient(135deg,#f6921e,#ed752e);display:block}
  .close{float:right;background:none;border:none;font-size:22px;cursor:pointer}
  @media(max-width:560px){.tag{display:none}}
</style>
</head>
<body>
<div class="wrap">
  <header>
    <img src="assets/favicon.png" alt="Corchat BD logo">
    <div class="brand">Corchat <span>BD</span></div>
    <div class="tag">Digital Course Marketplace</div>
  </header>

  <section class="hero">
    <h1>You scroll every day,<br>now turn that time into <span>income</span></h1>
    <p>From page and channel setup to monetization — a step-by-step curriculum. Get access to the private Telegram group right after purchase.</p>
  </section>

  <section class="grid">
    <?php foreach ($COURSES as $c): ?>
    <div class="card">
      <div style="font-size:36px"><?= $c['icon'] ?></div>
      <h2><?= htmlspecialchars($c['name']) ?></h2>
      <p><?= htmlspecialchars($c['desc']) ?></p>
      <div class="price"><?= $c['price'] ?> BDT<small>One-time payment, instant access on Telegram</small></div>
      <button class="buy" onclick="openBuy('<?= $c['id'] ?>','<?= htmlspecialchars($c['name'], ENT_QUOTES) ?>',<?= $c['price'] ?>)">Buy Now — <?= $c['price'] ?> BDT</button>
    </div>
    <?php endforeach; ?>
  </section>

  <section class="box">
    <h3>Facebook Page Monetization — Curriculum</h3>
    <ul><?php foreach ($FB_SYSTEMS as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
    <button class="buy" onclick="openBuy('facebook-monetization','Facebook Page Monetization',200)">Buy This Course — 200 BDT</button>
  </section>

  <section class="box">
    <h3>YouTube Monetization — Curriculum</h3>
    <ul><?php foreach ($YT_SYSTEMS as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
    <button class="buy" onclick="openBuy('youtube-monetization','YouTube Monetization',200)">Buy This Course — 200 BDT</button>
  </section>

  <section class="box how">
    <h3>How to Buy</h3>
    <p>1. Tap “Buy Now” → 2. Send Money <b>200 BDT</b> to one of these numbers → 3. Confirm your order with the TrxID → 4. Join the Telegram group with the link</p>
    <div class="paycards">
      <div class="paycard bkash"><img src="assets/bkash.png" alt="bKash"><div><div class="payname">bKash (Personal)</div><div class="paynum2"><?= htmlspecialchars($SITE['bkash']) ?></div></div></div>
      <div class="paycard nagad"><img src="assets/nagad.png" alt="Nagad"><div><div class="payname">Nagad (Personal)</div><div class="paynum2"><?= htmlspecialchars($SITE['nagad']) ?></div></div></div>
    </div>
  </section>

  <section class="box">
    <h3>Support Included</h3>
    <ul>
      <li>Daily live support</li>
      <li>Personal call solution for views problems</li>
      <li>If you cannot set up a page, the setup will be done for you</li>
    </ul>
  </section>

  <section class="mission">
    <h2>Removing unemployment is the main goal</h2>
    <p>Learning should be easy for everyone — start your online income journey today.</p>
  </section>

  <footer>
    CourseHaat BD · <img class="payicon" src="assets/bkash.png" alt="bKash"> bKash (Personal): <?= htmlspecialchars($SITE['bkash']) ?> · <img class="payicon" src="assets/nagad.png" alt="Nagad"> Nagad (Personal): <?= htmlspecialchars($SITE['nagad']) ?><br>
    If you face any problem, you will get support in the Telegram group
  </footer>
</div>

<dialog id="buyModal">
  <div class="modal">
    <button class="close" type="button" onclick="document.getElementById('buyModal').close()">✕</button>
    <h3 id="courseName">Buy Course</h3>
    <p>Send Money <b id="modalPrice">200 BDT</b> to the number below, then fill in this form.</p>
    <form method="post" action="order.php">
      <input type="hidden" name="course" id="courseId" value="">
      <label>Payment Method</label>
      <select name="method" id="method" required onchange="showNum()">
        <option value="">— Choose —</option>
        <option value="bkash">bKash</option>
        <option value="nagad">Nagad</option>
      </select>
      <div class="paynum" id="paynum"></div>
      <label>Your Name</label>
      <input name="name" required minlength="3" placeholder="Enter your name">
      <label>Mobile Number (the number you sent money from)</label>
      <input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX">
      <label>TrxID</label>
      <input name="trx" required minlength="6" placeholder="e.g. 9HX7XXXXXX">
      <button class="buy" type="submit">Confirm Order</button>
    </form>
  </div>
</dialog>
<script>
const PAYNUM = { bkash: <?= json_encode($SITE['bkash']) ?>, nagad: <?= json_encode($SITE['nagad']) ?> };
function openBuy(id, name, price){
  document.getElementById('courseId').value = id;
  document.getElementById('courseName').textContent = name + ' — ' + price + ' BDT';
  document.getElementById('modalPrice').textContent = price + ' BDT';
  document.getElementById('buyModal').showModal();
}
function showNum(){
  const m = document.getElementById('method').value;
  const box = document.getElementById('paynum');
  if(!m){ box.style.display='none'; box.className='paynum'; return; }
  box.className = 'paynum ' + m;
  box.innerHTML = '<img src="assets/' + m + '.png" alt=""> <b>' + (m==='bkash'?'bKash':'Nagad') + ' Personal:</b> <b>' + PAYNUM[m] + '</b><br>Send the course price to this number, then enter the TrxID.';
}
</script>
</body>
</html>
