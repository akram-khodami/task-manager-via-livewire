<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دستیار شخصی من</title>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="https://tapi.bale.ai/miniapp.js?3"></script>

    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #212529;
            --card-bg: #f8f9fa;
            --primary-btn: #0d6efd;
            --primary-btn-text: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
        }

        .miniapp-container {
            max-width: 480px;
            margin: 0 auto;
            padding: 16px;
        }

        .feature-card {
            background: var(--card-bg);
            border: none;
            border-radius: 16px;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-2px);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .btn-primary {
            background-color: var(--primary-btn);
            border-color: var(--primary-btn);
            color: var(--primary-btn-text);
        }

        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s;
        }

        .loading-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .page-content {
            display: none;
        }

        .page-content.active {
            display: block;
        }
    </style>
</head>
<body>
{{-- اسکرین بارگذاری --}}
<div id="loadingScreen" class="loading-screen">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">در حال بارگذاری...</span>
        </div>
        <h5>در حال بارگذاری...</h5>
    </div>
</div>

{{-- ========= صفحه اصلی ========= --}}
<div id="homePage" class="miniapp-container page-content">
    <div class="text-center mb-4">
        <h4 class="fw-bold">دستیار شخصی من</h4>
        <p class="text-muted small">خوش آمدید!</p>
    </div>

    <div class="card feature-card mb-4">
        <div class="card-body text-center">
            <div class="feature-icon">👤</div>
            <h5>پروفایل شما</h5>
            <p id="userInfo" class="text-muted small">در حال دریافت اطلاعات...</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('todos')">
                <div class="card-body text-center">
                    <div class="feature-icon">📝</div>
                    <h6>لیست کارها</h6>
                    <small class="text-muted">مدیریت وظایف</small>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('payment')">
                <div class="card-body text-center">
                    <div class="feature-icon">💳</div>
                    <h6>کیف پول</h6>
                    <small class="text-muted">پرداخت و اعتبار</small>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('scanner')">
                <div class="card-body text-center">
                    <div class="feature-icon">📷</div>
                    <h6>اسکنر QR</h6>
                    <small class="text-muted">اسکن کد</small>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('settings')">
                <div class="card-body text-center">
                    <div class="feature-icon">⚙️</div>
                    <h6>تنظیمات</h6>
                    <small class="text-muted">تنظیمات ظاهری</small>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-outline-primary w-100 mb-2" onclick="requestContact()">
            📞 دریافت شماره تماس
        </button>
        <button class="btn btn-outline-secondary w-100 mb-2" onclick="addToHomeScreen()">
            ➕ افزودن به صفحه اصلی
        </button>
    </div>
</div>

{{-- ========= صفحه تنظیمات ========= --}}
<div id="settingsPage" class="miniapp-container page-content">
    <div class="mb-4">
        <button class="btn btn-outline-secondary btn-sm" onclick="goBack()">
            🔙 بازگشت
        </button>
        <h4 class="d-inline-block me-3">⚙️ تنظیمات</h4>
    </div>

    <div class="card feature-card mb-3">
        <div class="card-body">
            <h5 class="card-title">تنظیمات ظاهری</h5>

            <div class="mb-3">
                <label class="form-label">رنگ هدر</label>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm flex-grow-1" style="background: #007AFF; color: white;"
                            onclick="changeHeaderColor('#007AFF')">آبی</button>
                    <button class="btn btn-sm flex-grow-1" style="background: #34C759; color: white;"
                            onclick="changeHeaderColor('#34C759')">سبز</button>
                    <button class="btn btn-sm flex-grow-1" style="background: #FF3B30; color: white;"
                            onclick="changeHeaderColor('#FF3B30')">قرمز</button>
                    <button class="btn btn-sm flex-grow-1" style="background: #FF9500; color: white;"
                            onclick="changeHeaderColor('#FF9500')">نارنجی</button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">حالت تمام‌صفحه</label>
                <button class="btn btn-outline-primary w-100" onclick="toggleFullscreen()">
                    <span id="fullscreenBtnText">فعال‌سازی حالت تمام‌صفحه</span>
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">تأیید خروج</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="confirmClose"
                           onchange="toggleConfirmClose(this.checked)">
                    <label class="form-check-label" for="confirmClose">
                        هنگام بستن مینی‌اپ تأیید بگیر
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card feature-card mb-3">
        <div class="card-body">
            <h5 class="card-title">تم فعلی</h5>
            <div class="row" id="themeColors"></div>
        </div>
    </div>

    <div class="card feature-card mb-3">
        <div class="card-body">
            <h5 class="card-title">میانبر صفحه اصلی</h5>
            <button class="btn btn-outline-primary w-100" onclick="checkHomeScreen()">
                بررسی وضعیت میانبر
            </button>
            <div id="homeScreenResult" class="mt-2 small"></div>
        </div>
    </div>

    <div class="card feature-card">
        <div class="card-body">
            <h5 class="card-title">درباره</h5>
            <ul class="list-unstyled small mb-0">
                <li>نسخه مینی‌اپ: <span id="miniappVersion">-</span></li>
                <li>پلتفرم: <span id="platformType">-</span></li>
                <li>تم سیستم: <span id="colorScheme">-</span></li>
                <li>پشتیبانی مینی‌اپ: <span id="miniAppSupported">-</span></li>
            </ul>
        </div>
    </div>
</div>

{{-- ========= صفحه اسکنر ========= --}}
<div id="scannerPage" class="miniapp-container page-content">
    <div class="mb-4">
        <button class="btn btn-outline-secondary btn-sm" onclick="goBack()">
            🔙 بازگشت
        </button>
        <h4 class="d-inline-block me-3">📷 اسکنر QR</h4>
    </div>

    <div class="card feature-card mb-3">
        <div class="card-body text-center">
            <div style="font-size: 4rem;">📷</div>
            <h5>اسکن کد QR</h5>
            <p class="text-muted small">دوربین رو به سمت QR code بگیرید</p>
            <button class="btn btn-primary w-100" onclick="startScanning()">
                شروع اسکن
            </button>
        </div>
    </div>

    <div id="scanResult" style="display: none;">
        <div class="card feature-card mb-3">
            <div class="card-body">
                <h5 class="card-title">✅ نتیجه اسکن</h5>
                <div class="p-3 bg-light rounded">
                    <code id="scanText" class="text-break"></code>
                </div>
                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="copyScanResult()">
                        📋 کپی
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="openScannedLink()">
                        🔗 باز کردن لینک
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card feature-card">
        <div class="card-body">
            <h5 class="card-title">📋 تاریخچه اسکن‌ها</h5>
            <div id="scanHistory">
                <p class="text-muted small">هنوز اسکنی انجام نشده</p>
            </div>
        </div>
    </div>
</div>

{{-- ========= Toast Container ========= --}}
<div id="toastContainer" class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;"></div>

<script>
    // ============ متغیرهای سراسری ============
    let BaleApp;
    let currentUser;
    let currentPage = 'home';
    let pageHistory = ['home'];
    let lastScannedText = '';

    // ============ شروع ============
    document.addEventListener('DOMContentLoaded', function () {
        initMiniApp();
    });

    function initMiniApp() {
        if (!window.Bale || !window.Bale.WebApp) {
            showError('SDK بله بارگذاری نشده است');
            return;
        }

        BaleApp = window.Bale.WebApp;
        console.log('نسخه مینی‌اپ:', BaleApp.version);
        console.log('تم:', BaleApp.colorScheme);

        applyTheme(BaleApp.themeParams);
        loadUserInfo();
        setupEventListeners();
        setupHeaderButtons();

        BaleApp.ready();

        setTimeout(() => {
            document.getElementById('loadingScreen').classList.add('hidden');
            navigateTo('home');
        }, 500);
    }

    function setupEventListeners() {
        // دکمه بازگشت بله
        if (BaleApp.BackButton) {
            BaleApp.BackButton.onClick(() => goBack());
        }

        // رویدادهای اسکنر
        BaleApp.onEvent('qrTextReceived', function (data) {
            lastScannedText = data.text;
            document.getElementById('scanResult').style.display = 'block';
            document.getElementById('scanText').textContent = data.text;
            addToScanHistory(data.text);
            showToast('✅ اسکن با موفقیت انجام شد');
        });

        BaleApp.onEvent('scanQrPopupClosed', function () {
            showToast('اسکنر بسته شد');
        });

        // رویدادهای میانبر
        BaleApp.onEvent('homeScreenAdded', function () {
            showToast('🎉 میانبر با موفقیت اضافه شد');
        });

        BaleApp.onEvent('homeScreenFailed', function () {
            showToast('❌ خطا در افزودن میانبر');
        });
    }

    // ============ تم ============
    function applyTheme(themeParams) {
        if (!themeParams) return;
        const root = document.documentElement;

        const map = {
            bg_color: '--bg-color',
            text_color: '--text-color',
            secondary_bg_color: '--card-bg',
            button_color: '--primary-btn',
            button_text_color: '--primary-btn-text'
        };

        Object.entries(map).forEach(([key, cssVar]) => {
            if (themeParams[key]) {
                root.style.setProperty(cssVar, themeParams[key]);
            }
        });
    }

    // ============ اطلاعات کاربر ============
    function loadUserInfo() {
        if (BaleApp.initDataUnsafe && BaleApp.initDataUnsafe.user) {
            currentUser = BaleApp.initDataUnsafe.user;
            const html = `${currentUser.first_name || 'کاربر'} ${currentUser.username ? '@' + currentUser.username : ''}`;
            document.getElementById('userInfo').innerHTML = html;
            validateWithServer(BaleApp.initData);
        }
    }

    async function validateWithServer(initData) {
        try {
            const response = await fetch('/api/miniapp/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ initData })
            });
            const result = await response.json();
            console.log('نتیجه اعتبارسنجی:', result);
        } catch (error) {
            console.error('خطا در اعتبارسنجی:', error);
        }
    }

    // ============ هدر ============
    function setupHeaderButtons() {
        if (BaleApp.SettingsButton) {
            BaleApp.SettingsButton.show();
            BaleApp.SettingsButton.onClick(() => navigateTo('settings'));
        }
    }

    // ============ ناوبری ============
    function navigateTo(page) {
        document.querySelectorAll('.page-content').forEach(el => el.classList.remove('active'));

        switch (page) {
            case 'home':
                document.getElementById('homePage').classList.add('active');
                if (BaleApp && BaleApp.BackButton) BaleApp.BackButton.hide();
                break;
            case 'settings':
                document.getElementById('settingsPage').classList.add('active');
                loadSettings();
                if (BaleApp && BaleApp.BackButton) BaleApp.BackButton.show();
                break;
            case 'scanner':
                document.getElementById('scannerPage').classList.add('active');
                if (BaleApp && BaleApp.BackButton) BaleApp.BackButton.show();
                break;
            default:
                document.getElementById('homePage').classList.add('active');
        }

        pageHistory.push(page);
        currentPage = page;
    }

    function goBack() {
        pageHistory.pop();
        const previousPage = pageHistory[pageHistory.length - 1] || 'home';
        navigateTo(previousPage);
    }

    // ============ دکمه‌های عملیاتی ============
    function requestContact() {
        if (!BaleApp || !BaleApp.requestContact) {
            showToast('❌ این قابلیت پشتیبانی نمی‌شود');
            return;
        }
        BaleApp.requestContact(function (shared, contact) {
            if (shared) {
                showToast('📞 شماره تماس دریافت شد: ' + contact);
            } else {
                showToast('⚠️ کاربر اجازه دسترسی نداد');
            }
        });
    }

    function addToHomeScreen() {
        if (BaleApp && BaleApp.addToHomeScreen) {
            BaleApp.addToHomeScreen();
        } else {
            showToast('❌ این قابلیت پشتیبانی نمی‌شود');
        }
    }

    // ============ تنظیمات ============
    function changeHeaderColor(color) {
        if (BaleApp && BaleApp.setHeaderColor) {
            BaleApp.setHeaderColor(color);
            showToast('🎨 رنگ هدر با موفقیت تغییر کرد');
        }
    }

    function toggleFullscreen() {
        const btn = document.getElementById('fullscreenBtnText');
        if (btn.textContent.includes('فعال')) {
            if (BaleApp && BaleApp.expand) {
                BaleApp.expand();
                btn.textContent = 'غیرفعال‌سازی حالت تمام‌صفحه';
                showToast('📱 حالت تمام‌صفحه فعال شد');
            }
        } else {
            showToast('برای خروج، مینی‌اپ را ببندید و دوباره باز کنید');
        }
    }

    function toggleConfirmClose(enabled) {
        if (!BaleApp) return;
        if (enabled && BaleApp.enableClosingConfirmation) {
            BaleApp.enableClosingConfirmation();
            showToast('🔒 تأیید خروج فعال شد');
        } else if (!enabled && BaleApp.disableClosingConfirmation) {
            BaleApp.disableClosingConfirmation();
            showToast('🔓 تأیید خروج غیرفعال شد');
        }
    }

    function checkHomeScreen() {
        if (!BaleApp || !BaleApp.checkHomeScreenStatus) return;

        BaleApp.checkHomeScreenStatus(function (status) {
            const messages = {
                unsupported: '❌ دستگاه شما از این قابلیت پشتیبانی نمی‌کند',
                unknown: '⚠️ وضعیت میانبر قابل تشخیص نیست',
                added: '✅ میانبر قبلاً به صفحه اصلی اضافه شده',
                missed: '➕ میانبر اضافه نشده. می‌توانید اضافه کنید'
            };
            document.getElementById('homeScreenResult').innerHTML =
                `<div class="alert alert-info">${messages[status] || status}</div>`;
        });
    }

    function loadSettings() {
        if (!BaleApp) return;
        document.getElementById('miniappVersion').textContent = BaleApp.version || 'نامشخص';
        document.getElementById('platformType').textContent = BaleApp.isIframe ? 'وب' : 'اندروید';
        document.getElementById('colorScheme').textContent = BaleApp.colorScheme || 'نامشخص';
        document.getElementById('miniAppSupported').textContent = BaleApp.isMiniAppSupported ? '✅ بله' : '❌ خیر';

        if (BaleApp.themeParams) {
            const colorMap = {
                bg_color: 'پس‌زمینه اصلی',
                text_color: 'رنگ متن',
                hint_color: 'متن راهنما',
                link_color: 'لینک‌ها',
                button_color: 'دکمه‌ها',
                button_text_color: 'متن دکمه',
                secondary_bg_color: 'پس‌زمینه ثانویه'
            };

            let html = '';
            Object.entries(colorMap).forEach(([key, name]) => {
                if (BaleApp.themeParams[key]) {
                    html += `
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:24px;height:24px;background:${BaleApp.themeParams[key]};border-radius:50%;border:1px solid #ddd;"></div>
                                    <small>${name}</small>
                                </div>
                            </div>`;
                }
            });
            document.getElementById('themeColors').innerHTML = html;
        }
    }

    // ============ اسکنر QR ============
    function startScanning() {
        if (BaleApp && BaleApp.showScanQrPopup) {
            BaleApp.showScanQrPopup({ text: 'کد QR را اسکن کنید' });
            showToast('📷 اسکنر فعال شد');
        } else {
            showToast('❌ دستگاه شما از اسکنر پشتیبانی نمی‌کند');
        }
    }

    function addToScanHistory(text) {
        const historyDiv = document.getElementById('scanHistory');
        if (historyDiv.querySelector('.text-muted')) historyDiv.innerHTML = '';

        const time = new Date().toLocaleTimeString('fa-IR');
        historyDiv.insertAdjacentHTML('afterbegin', `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                    <small class="text-truncate" style="max-width:200px;">${text}</small>
                    <small class="text-muted">${time}</small>
                </div>`);
    }

    function copyScanResult() {
        navigator.clipboard.writeText(lastScannedText).then(() => showToast('📋 متن کپی شد'));
    }

    function openScannedLink() {
        if (lastScannedText.startsWith('http')) {
            if (BaleApp && BaleApp.openLink) {
                BaleApp.openLink(lastScannedText);
            } else {
                window.open(lastScannedText, '_blank');
            }
        } else {
            showToast('⚠️ متن اسکن شده یک لینک معتبر نیست');
        }
    }

    // ============ توابع کمکی ============
    function showToast(message) {
        const container = document.getElementById('toastContainer');
        const id = 'toast_' + Date.now();
        container.insertAdjacentHTML('beforeend', `
                <div id="${id}" class="toast align-items-center text-bg-primary border-0 mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`);

        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function showError(message) {
        document.getElementById('loadingScreen').innerHTML = `
                <div class="text-center text-danger">
                    <span style="font-size:3rem;">⚠️</span>
                    <h5 class="mt-3">خطا</h5>
                    <p>${message}</p>
                </div>`;
    }
</script>
</body>
</html>
