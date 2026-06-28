# PutPath — نموذج التصميم (HTML Prototype)

نموذج تصميم تفاعلي كامل لمنصّة **PutPath** (Agentic Fleet OS) بنمط Dark Cockpit، يغطّي كل صفحات الـ PRD ببيانات وهمية واقعية.

## التشغيل
افتح `index.html` في أي متصفّح. التنقّل عبر الشريط الجانبي (٢٦ صفحة). لا يحتاج إنترنت أو خادم.

## البنية
```
putpath-prototype/
├── index.html              # الرئيسية (map-first)
├── ai-coworkers.html       # محادثة الوكلاء + Artifact + AI Output Standard
├── work-requests.html      # لوحة Kanban
├── collaboration.html      # قنوات (Slack-like) + منشن للوكلاء
├── projects.html           # مشاريع + Bottleneck Coach
├── bottlenecks.html        # عبء العمل والمخاطر
├── approvals.html          # موافقات ClawPatrol
├── reports.html            # توليد تقارير (قوالب + أمر AI)
├── routine-studio.html     # أتمتة (trigger→condition→agent→action→approval)
├── data-hub.html           # تكاملات + جداول + AI-filled
├── mini-apps.html          # تطبيقات تشغيلية
├── insights.html           # KPIs + أداء الوكلاء
├── alerts.html             # التنبيهات + إجراءات AI
├── history.html            # إعادة تشغيل المسار + AVL
├── devices.html            # الأجهزة + بروتوكولات + حساسات
├── vehicles.html           # المركبات/الأصول
├── drivers.html            # السائقون + بطاقة أداء
├── workers.html            # الموظفون + عبء العمل
├── fuel.html               # إدارة الوقود
├── maintenance.html        # الصيانة + توقّع TimesFM
├── dispatch.html           # تحسين المسار (VROOM)
├── knowledge.html          # RAG هجين (Surya+Vector + OKF/MD)
├── audit-log.html          # سجل التدقيق
├── channels.html           # قنوات التواصل
├── logs.html               # السجلات التقنية
├── settings.html           # الإعدادات (AI/أمان/خطة)
└── assets/
    ├── app.css             # نظام التصميم (Dark Cockpit)
    └── app.js              # حقن الشريط الجانبي والعلوي + التنقّل
```

## ملاحظات
- نظام التصميم مشترك (CSS + JS) — لتعديل القائمة أو الهوية، عدّل `assets/`.
- البيانات وهمية لأغراض العرض (عميل: شركة الباوني، مشروع KSP، تنبيه هبوط وقود).
- جاهز كأساس لتحويله إلى مكوّنات React لاحقًا.
