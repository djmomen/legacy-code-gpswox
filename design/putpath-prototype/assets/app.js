// PutPath prototype shell: injects sidebar + topbar into every page.
const NAV = [
  {s:"العمليات"},
  {p:"index.html",i:"🗺",t:"الرئيسية"},
  {p:"ai-coworkers.html",i:"🤖",t:"AI Coworkers"},
  {p:"work-requests.html",i:"📋",t:"Work Requests"},
  {p:"collaboration.html",i:"💬",t:"التعاون"},
  {p:"projects.html",i:"🗂",t:"المشاريع"},
  {p:"bottlenecks.html",i:"🚧",t:"الاختناقات"},
  {p:"approvals.html",i:"✅",t:"الموافقات",b:"3"},
  {s:"التقارير والأتمتة"},
  {p:"reports.html",i:"📝",t:"التقارير"},
  {p:"routine-studio.html",i:"⚙️",t:"Routine Studio"},
  {p:"data-hub.html",i:"🔌",t:"Data Hub"},
  {p:"mini-apps.html",i:"🧩",t:"Mini Apps"},
  {p:"insights.html",i:"📊",t:"المؤشرات"},
  {s:"الأسطول"},
  {p:"alerts.html",i:"🔔",t:"التنبيهات"},
  {p:"history.html",i:"⏱",t:"السجل والتتبّع"},
  {p:"devices.html",i:"🛰",t:"الأجهزة"},
  {p:"vehicles.html",i:"🚚",t:"المركبات"},
  {p:"drivers.html",i:"🧑‍✈️",t:"السائقون"},
  {p:"workers.html",i:"👷",t:"الموظفون"},
  {p:"fuel.html",i:"⛽",t:"الوقود"},
  {p:"maintenance.html",i:"🔧",t:"الصيانة"},
  {p:"dispatch.html",i:"🧭",t:"التوزيع والمسارات"},
  {s:"المعرفة والنظام"},
  {p:"knowledge.html",i:"🧠",t:"المعرفة والبيانات"},
  {p:"audit-log.html",i:"🗃",t:"سجل التدقيق"},
  {p:"channels.html",i:"📡",t:"القنوات"},
  {p:"logs.html",i:"📜",t:"السجلات"},
  {p:"settings.html",i:"⚙️",t:"الإعدادات"},
];
const cur = document.body.dataset.page || "index.html";
let nav = "";
NAV.forEach(n=>{
  if(n.s){ nav += `<div class="sect">${n.s}</div>`; return; }
  const a = n.p===cur ? "active" : "";
  const b = n.b ? `<span class="badge">${n.b}</span>` : "";
  nav += `<a class="${a}" href="${n.p}"><span class="ico">${n.i}</span> ${n.t} ${b}</a>`;
});
const side = document.getElementById("side");
if(side) side.innerHTML = `
  <div class="brand">🛰 PutPath <small>Agentic Fleet OS</small></div>
  <nav class="nav">${nav}</nav>
  <div class="foot">العميل: <b>شركة الباوني للنقل</b><br>الخطة: Pro · ١٢٤ جهاز · موزّع: المالك</div>`;
const top = document.getElementById("top");
if(top) top.innerHTML = `
  <input class="search" placeholder="🔍 ابحث عن مركبة، سائق، منطقة، أو اسأل الـ AI…">
  <div class="spacer"></div>
  <div class="chip"><span class="dot" style="background:var(--green)"></span> ٩٨ متصل</div>
  <div class="chip"><span class="dot" style="background:var(--red)"></span> ٦ offline</div>
  <div class="avatar">م</div>`;
