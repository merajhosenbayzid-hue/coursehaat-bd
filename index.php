<?php require __DIR__ . '/api.php'; ?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($SITE['name']) ?> — ডিজিটাল কোর্সের হাট</title>
<meta name="description" content="Facebook Page Monetization ও YouTube Monetization — প্রতিটি কোর্স মাত্র ২০০ টাকায়। রেফার বোনাস: প্রথম সফল রেফারে ১০০ টাকা, পরের প্রতিটিতে ৫০ টাকা। bKash/Nagad পেমেন্ট।">
<link rel="icon" href="assets/logo.webp">
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{font-family:'Hind Siliguri','Segoe UI',system-ui,sans-serif;background:radial-gradient(1200px 700px at 50% -10%,#2a0a5e,#12042e 60%);color:#fff;min-height:100vh;overflow-x:hidden}
  #bg3d{position:fixed;inset:0;width:100%;height:100%;z-index:0;pointer-events:none}
  .wrap{max-width:1080px;margin:0 auto;padding:0 18px;position:relative;z-index:1}
  header{display:flex;align-items:center;gap:12px;padding:16px 0}
  header img{width:46px;height:46px;border-radius:12px}
  header .brand{font-size:22px;font-weight:800}
  header .brand span{color:#ff4fd8}
  header .tag{margin-left:auto;font-size:14px;color:#cdbcf5}
  .hero{text-align:center;padding:40px 0 8px}
  .badge{display:inline-block;color:#ff4fd8;font-weight:700;font-size:14px;border:1px solid rgba(255,79,216,.35);padding:7px 16px;border-radius:999px;background:rgba(255,79,216,.07)}
  .hero h1{font-size:clamp(32px,6.2vw,58px);line-height:1.25;font-weight:900;margin:22px 0 14px;text-shadow:0 4px 30px rgba(0,0,0,.45)}
  .hero h1 .hl{color:#ff4fd8;background:rgba(255,79,216,.13);padding:0 10px;border-radius:8px;white-space:nowrap}
  .hero p.sub{color:#e6dcff;font-size:clamp(16px,2.6vw,20px);line-height:1.7;max-width:760px;margin:0 auto}
  .chips{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin:26px 0 6px}
  .chip{border:1.5px solid rgba(255,255,255,.45);border-radius:999px;padding:9px 20px;font-weight:700;font-size:15px;background:rgba(18,4,46,.45);backdrop-filter:blur(4px)}
  .reveal{opacity:0;transform:translateY(34px);transition:opacity .7s ease,transform .7s ease}
  .reveal.from-left{transform:translateX(-46px)}
  .reveal.from-right{transform:translateX(46px)}
  .reveal.in{opacity:1;transform:none}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin:38px 0 10px}
  .card{background:linear-gradient(160deg,rgba(34,16,77,.92),rgba(24,6,54,.92));border:1px solid rgba(255,79,216,.25);border-radius:20px;padding:26px;backdrop-filter:blur(6px)}
  .card .icon{font-size:38px}
  .card h2{font-size:24px;margin:12px 0 8px;line-height:1.35}
  .card p{color:#d9cdf7;line-height:1.65;font-size:16px}
  .price{font-size:34px;font-weight:900;margin:12px 0 4px}
  .price small{font-size:14px;color:#cdbcf5;font-weight:600;display:block}
  .buy{display:block;width:100%;text-align:center;background:linear-gradient(90deg,#c026d3,#e11dc0);color:#fff;border:none;border-radius:14px;padding:15px;font-size:19px;font-weight:800;cursor:pointer;font-family:inherit;margin-top:12px}
  .buy:hover{filter:brightness(1.12)}
  a.buy{text-decoration:none}
  .systems{background:rgba(18,4,46,.66);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:28px;margin:30px 0;backdrop-filter:blur(6px)}
  .systems h3{font-size:clamp(21px,3.4vw,28px);text-align:center;margin-bottom:6px}
  .systems .lead{text-align:center;color:#ff9de6;font-weight:700;margin-bottom:18px}
  .systems ul{list-style:none;columns:2;column-gap:34px;font-size:16.5px;line-height:1.5}
  .systems li{margin:9px 0;break-inside:avoid}
  .systems li::before{content:'✓ ';color:#ff4fd8;font-weight:900}
  @media(max-width:640px){.systems ul{columns:1}}
  .how{background:rgba(18,4,46,.66);border:1px solid rgba(255,255,255,.09);border-radius:18px;padding:24px;margin:26px 0;text-align:center;backdrop-filter:blur(6px)}
  .how h3{font-size:21px;margin-bottom:12px}
  .how p{color:#d9cdf7;line-height:1.9}
  /* প্রতিটি লাইনে ম্যাজিকের মতো নড়াচড়া — লোড ও স্ক্রলে ধাপে ধাপে জীবন্ত হয়ে ওঠে */
  @keyframes rise{from{opacity:0;transform:translateY(26px)}to{opacity:1;transform:none}}
  @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
  @keyframes shineMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
  @keyframes sweep{0%{left:-80%}100%{left:130%}}
  @keyframes liIn{from{opacity:0;transform:translateX(-16px)}to{opacity:1;transform:none}}
  .hero .badge{animation:rise .7s .05s both;position:relative;overflow:hidden}
  .hero .badge::after{content:'';position:absolute;top:0;left:-80%;width:50%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.35),transparent);animation:sweep 3.2s ease-in-out infinite}
  .hero h1{animation:rise .8s .15s both,floaty 7s 1s ease-in-out infinite}
  .hero .sub{animation:rise .8s .3s both}
  .hero .chips{animation:rise .8s .45s both}
  .hero .chip{animation:floaty 5s ease-in-out infinite}
  .hero .chip:nth-child(2){animation-delay:.6s}.hero .chip:nth-child(3){animation-delay:1.2s}.hero .chip:nth-child(4){animation-delay:1.8s}.hero .chip:nth-child(5){animation-delay:2.4s}
  .systems li{opacity:0}
  .systems.in li{animation:liIn .55s both;animation-delay:calc(var(--i)*75ms)}
  .card{transition:transform .35s ease,box-shadow .35s ease}
  .card:hover{transform:translateY(-6px);box-shadow:0 18px 44px rgba(255,79,216,.22)}
  .mission h2{background-size:220% auto;animation:shineMove 6s linear infinite}
  .discount-note{background:rgba(45,212,238,.12);border:1px solid rgba(45,212,238,.5);color:#a5f3fc;border-radius:12px;padding:10px 12px;font-size:15px;font-weight:700;text-align:center;margin-top:12px;animation:floaty 5.5s ease-in-out infinite}
  @media (prefers-reduced-motion: reduce){.hero h1,.hero .chip,.discount-note{animation:none}.systems li{opacity:1}}
  .mission{text-align:center;padding:54px 10px 8px}
  .mission h2{font-size:clamp(40px,8.5vw,80px);font-weight:900;line-height:1.2;background:linear-gradient(90deg,#ff4fd8,#a855f7,#2dd4ee);-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:none}
  .mission p{color:#d9cdf7;font-size:clamp(15px,2.4vw,19px);margin-top:12px}
  footer{text-align:center;color:#b7a8de;font-size:14px;padding:26px 0 40px;line-height:1.8}
  dialog{border:none;border-radius:20px;padding:0;max-width:440px;width:calc(100% - 32px);background:#1d083f;color:#fff;z-index:5}
  dialog::backdrop{background:rgba(5,0,20,.78)}
  .modal{padding:26px}
  .modal h3{font-size:21px;margin-bottom:6px}
  .modal p{color:#d9cdf7;font-size:15px;line-height:1.6}
  .modal label{display:block;font-size:14px;font-weight:700;margin:14px 0 6px}
  .modal input,.modal select{width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.25);background:#12042e;color:#fff;font-size:16px;font-family:inherit}
  .paynum{background:rgba(255,79,216,.1);border:1px dashed rgba(255,79,216,.6);border-radius:12px;padding:12px;margin-top:14px;font-size:15.5px;line-height:1.7;display:none}
  .paynum b.num{font-size:19px;letter-spacing:.5px}
  .close{float:right;background:none;border:none;color:#cdbcf5;font-size:22px;cursor:pointer}
</style>
</head>
<body>
<canvas id="bg3d"></canvas>
<div class="wrap">
  <header>
    <img src="assets/logo.webp" alt="CourseHaat BD লোগো">
    <div class="brand">Course<span>Haat</span> BD</div>
    <div class="tag"><?= htmlspecialchars($SITE['tagline']) ?></div>
    <nav style="display:flex;gap:14px;font-size:15px;font-weight:600"><a style="color:#d9cdf7;text-decoration:none" href="index.php">হোম</a><?php if (auth_token()): ?><a style="color:#d9cdf7;text-decoration:none" href="dashboard.php">ড্যাশবোর্ড</a><?php else: ?><a style="color:#d9cdf7;text-decoration:none" href="account.php">লগইন / সাইন আপ</a><?php endif; ?></nav>
  </header>

  <section class="hero">
    <div class="badge">অনলাইন ইনকাম কোর্স · স্টেপ বাই স্টেপ · প্রাইভেট গ্রুপ</div>
    <h1>চেহারা না দেখিয়ে<br><span class="hl">Facebook</span> ও <span class="hl">YouTube</span><br>মনিটাইজেশন শিখুন</h1>
    <p class="sub">পেজ ও চ্যানেল সেটআপ থেকে মনিটাইজেশন পর্যন্ত — প্রতিটি কোর্সে পূর্ণাঙ্গ, ধাপে ধাপে সাজানো পাঠ্যক্রম। কেনার সাথে সাথে প্রাইভেট Telegram গ্রুপে অ্যাক্সেস।</p>
    <div class="chips">
      <div class="chip">পূর্ণাঙ্গ পাঠ্যক্রম</div>
      <div class="chip">ভিডিও তৈরির টিউটোরিয়াল</div>
      <div class="chip">AI Automation</div>
      <div class="chip">প্রতিদিন লাইভ সাপোর্ট</div>
      <div class="chip">মাত্র ২০০ টাকায় কোর্স</div>
    </div>
  </section>

  <section class="grid">
    <?php foreach ($COURSES as $i => $c): ?>
    <div class="card reveal <?= $i % 2 ? 'from-right' : 'from-left' ?>">
      <div class="icon"><?= $c['icon'] ?></div>
      <h2><?= htmlspecialchars($c['name']) ?></h2>
      <p><?= htmlspecialchars($c['desc']) ?></p>
      <div class="price"><?= $c['price'] ?> টাকা<small>একবার পেমেন্ট, Telegram-এ সাথে সাথে অ্যাক্সেস</small></div>
      <div class="discount-note">বন্ধুকে জয়েন করান: প্রথম সফল রেফারে ১০০ টাকা বোনাস, পরের প্রতিটিতে ৫০ টাকা করে</div>
      <a class="buy" href="buy.php?course=<?= $c['id'] ?>">এখনই কিনুন <?= $c['price'] ?> টাকায়</a>
    </div>
    <?php endforeach; ?>
  </section>

  <section class="systems reveal">
    <h3>Facebook Page Monetization — পাঠ্যক্রমে যা যা থাকছে</h3>
    <div class="lead">কোর্স ফি মাত্র ২০০ টাকা</div>
    <ul>
      <?php foreach ($FB_SYSTEMS as $i => $s): ?><li style="--i:<?= $i ?>"><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
    </ul>
    <a class="buy" href="buy.php?course=facebook-monetization">কোর্সটি কিনুন — ২০০ টাকা</a>
  </section>

  <section class="systems reveal">
    <h3>YouTube Monetization — পাঠ্যক্রমে যা যা থাকছে</h3>
    <div class="lead">কোর্স ফি মাত্র ২০০ টাকা</div>
    <ul>
      <?php foreach ($YT_SYSTEMS as $i => $s): ?><li style="--i:<?= $i ?>"><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
    </ul>
    <a class="buy" href="buy.php?course=youtube-monetization">কোর্সটি কিনুন — ২০০ টাকা</a>
  </section>

  <section class="systems reveal">
    <h3>শুধু কোর্স না — সাথে থাকছে হাতে-কলমে সাপোর্ট 🤝</h3>
    <div class="lead">আটকে গেলেও একা থাকবে না</div>
    <ul>
      <li style="--i:0"><b>প্রতিদিন লাইভ সাপোর্ট</b> — প্রতিদিন লাইভে তোমাদের প্রশ্নের উত্তর ও সমস্যার সমাধান</li>
      <li style="--i:1"><b>Views সমস্যায় পার্সোনাল কল</b> — কারো Views-এর সমস্যা হলে পার্সোনাল কলে নিয়ে ঠিক করে দেয়া হয়</li>
      <li style="--i:2"><b>Page Setup করে দেয়া হয়</b> — কেউ Page setup করতে না পারলে আমাদের পক্ষ থেকে setup করে দেয়া হয়</li>
    </ul>
  </section>

  <section class="how reveal">
    <h3>কিনবেন যেভাবে</h3>
    <p>১. পছন্দের কোর্সে “এখনই কিনুন” চাপুন &nbsp;→&nbsp; ২. bKash <?= htmlspecialchars($SITE['bkash']) ?> / Nagad <?= htmlspecialchars($SITE['nagad']) ?> নম্বরে ২০০ টাকা Send Money করুন &nbsp;→&nbsp; ৩. TrxID দিয়ে অর্ডার জমা দিন &nbsp;→&nbsp; ৪. যাচাই শেষে Approve হলেই ড্যাশবোর্ডে Telegram জয়েন বাটন পাবেন</p>
  </section>

  <section class="mission reveal">
    <h2>বেকারত্ব দূর করাই<br>মূল লক্ষ্য</h2>
    <p>শেখা হোক সবার জন্য সহজ — আজই শুরু হোক তোমার অনলাইন ইনকামের যাত্রা।</p>
  </section>

  <footer>
    CourseHaat BD · bKash (Personal): <?= htmlspecialchars($SITE['bkash']) ?> · Nagad (Personal): <?= htmlspecialchars($SITE['nagad']) ?><br>
    সমস্যা হলে Telegram গ্রুপেই সাপোর্ট পাবেন
  </footer>
</div>

<script>
// সেকশন স্ক্রল অ্যানিমেশন
const io = new IntersectionObserver(es => es.forEach(e => { if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target);} }), {threshold:.12});
document.querySelectorAll('.reveal').forEach(el => io.observe(el));
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(function(){
  if (typeof THREE === 'undefined') return; // CDN আটকে গেলে সাইট এমনিতেই চলবে
  const canvas = document.getElementById('bg3d');
  const renderer = new THREE.WebGLRenderer({canvas, alpha:true, antialias:true});
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, innerWidth/innerHeight, .1, 200);
  camera.position.set(0, 0, 15);

  scene.add(new THREE.AmbientLight(0x8877ff, .7));
  const pink = new THREE.PointLight(0xff4fd8, 1.6, 90); pink.position.set(8, 4, 8); scene.add(pink);
  const purple = new THREE.PointLight(0x7c3aed, 1.8, 90); purple.position.set(-9, -6, 6); scene.add(purple);
  const teal = new THREE.PointLight(0x2dd4ee, .9, 90); teal.position.set(0, -30, 4); scene.add(teal);

  const world = new THREE.Group(); scene.add(world);
  const matA = new THREE.MeshStandardMaterial({color:0x8b5cf6, metalness:.35, roughness:.25});
  const matB = new THREE.MeshStandardMaterial({color:0xff4fd8, metalness:.3, roughness:.3});
  const matC = new THREE.MeshStandardMaterial({color:0x2dd4ee, metalness:.35, roughness:.25, wireframe:true});
  const shapes = [];
  // পুরো পেজ জুড়ে (উপর থেকে নিচ পর্যন্ত) ছড়ানো 3D অবজেক্ট — স্ক্রলে ক্যামেরা নিচে নামে
  const makers = [
    () => new THREE.Mesh(new THREE.TorusGeometry(2.1,.55,18,42), matA),
    () => new THREE.Mesh(new THREE.BoxGeometry(2.5,3.2,.7), matB),          // বই
    () => new THREE.Mesh(new THREE.ConeGeometry(1.5,2.6,3), matB),          // প্লে ত্রিভুজ
    () => new THREE.Mesh(new THREE.IcosahedronGeometry(1.7,0), matA),
    () => new THREE.Mesh(new THREE.OctahedronGeometry(1.8,0), matC),
    () => new THREE.Mesh(new THREE.TorusKnotGeometry(1.15,.38,80,12), matA),
    () => new THREE.Mesh(new THREE.SphereGeometry(1.45,26,26), matC),
    () => new THREE.Mesh(new THREE.BoxGeometry(2.5,3.2,.7), matA),
    () => new THREE.Mesh(new THREE.TorusGeometry(1.8,.5,18,42), matB),
    () => new THREE.Mesh(new THREE.IcosahedronGeometry(1.6,0), matC),
    () => new THREE.Mesh(new THREE.ConeGeometry(1.5,2.6,3), matA),
    () => new THREE.Mesh(new THREE.TorusKnotGeometry(1.05,.34,80,12), matB),
  ];
  makers.forEach((make,i)=>{
    const m = make();
    m.position.set((i%2? 5.4 : -5.4) + (i%3)*0.7, 3 - i*5.4, -1 - (i%3));
    m.rotation.set(i*.5, i*.8, i*.3);
    m.userData = {spin:(i%2? -1:1)*(.0025+(i%4)*.0009), bob:Math.random()*Math.PI*2, y:m.position.y};
    world.add(m); shapes.push(m);
  });
  // পার্টিকল — পুরো গভীরতা জুড়ে
  const isMobile = innerWidth < 640;
  const COUNT = isMobile ? 650 : 1300;
  const pos = new Float32Array(COUNT*3);
  for(let i=0;i<COUNT;i++){
    pos[i*3] = (Math.random()-.5)*30;
    pos[i*3+1] = 8 - Math.random()*70;
    pos[i*3+2] = -6 + Math.random()*10;
  }
  const pg = new THREE.BufferGeometry();
  pg.setAttribute('position', new THREE.BufferAttribute(pos,3));
  const points = new THREE.Points(pg, new THREE.PointsMaterial({color:0xd8b4fe, size:.09, transparent:true, opacity:.85}));
  world.add(points);
  // অরবিট রিং
  for(let i=0;i<3;i++){
    const ring = new THREE.Mesh(new THREE.TorusGeometry(4.6+i*1.5,.03,8,90), new THREE.MeshBasicMaterial({color:0xff4fd8, transparent:true, opacity:.28}));
    ring.position.y = -i*22; ring.rotation.x = Math.PI/2.4; ring.userData={spin:.0016*(i+1), bob:i, y:ring.position.y};
    world.add(ring); shapes.push(ring);
  }

  let mx=0, my=0, tx=0, ty=0;
  addEventListener('mousemove', e => { tx = (e.clientX/innerWidth - .5)*2; ty = (e.clientY/innerHeight - .5)*2; }, {passive:true});
  addEventListener('touchmove', e => { if(e.touches[0]){ tx=(e.touches[0].clientX/innerWidth-.5)*2; ty=(e.touches[0].clientY/innerHeight-.5)*2; } }, {passive:true});

  function scrollTargetY(){
    const max = Math.max(1, document.body.scrollHeight - innerHeight);
    return -(window.scrollY / max) * 58; // উপর থেকে একদম নিচ পর্যন্ত যাত্রা
  }
  function resize(){
    renderer.setSize(innerWidth, innerHeight);
    camera.aspect = innerWidth/innerHeight; camera.updateProjectionMatrix();
  }
  addEventListener('resize', resize); resize();

  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  let t = 0;
  function animate(){
    requestAnimationFrame(animate);
    t += .01;
    mx += (tx - mx)*.045; my += (ty - my)*.045;
    // মাউস নড়লে পুরো গ্রাফিক্স নড়ে (প্যারালাক্স)
    camera.position.x = mx * 2.4;
    camera.position.y = scrollTargetY() - my * 1.2;
    camera.lookAt(mx*1.2, camera.position.y, 0);
    world.rotation.y = mx * .16;
    world.rotation.x = my * .06;
    shapes.forEach(s => {
      s.rotation.y += s.userData.spin || .003;
      s.rotation.x += (s.userData.spin || .003)*.6;
      s.position.y = s.userData.y + Math.sin(t + (s.userData.bob||0)) * .35;
    });
    points.rotation.y = t * .012;
    renderer.render(scene, camera);
  }
  if (reduced) { renderer.render(scene, camera); } else { animate(); }
})();
</script>
</body>
</html>
