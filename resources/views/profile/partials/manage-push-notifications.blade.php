<section>
    <header class="mb-3">
        <p class="text-secondary small mb-0">
            Włącz natywne powiadomienia w przeglądarce i na telefonie (PWA), aby otrzymywać informacje o nowych rozliczeniach, odwołanych lekcjach i ważnych komunikatach ze szkoły.
        </p>
    </header>

    <div id="push-notification-wrapper" class="py-2">
        <div id="push-unsupported" class="alert alert-warning d-none" role="alert">
            <i class="ti ti-alert-circle me-1"></i> Twoja obecna przeglądarka nie obsługuje powiadomień Push (Web Push API).
        </div>

        <div id="push-controls" class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 p-3 bg-light rounded-3 border">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span id="push-status-badge" class="badge bg-secondary-lt text-secondary">Sprawdzanie stanu...</span>
                    <span id="push-permission-hint" class="small text-secondary"></span>
                </div>
                <div class="small text-muted mt-1" id="push-description">
                    Kliknij poniższy przycisk, aby zezwolić na odbieranie powiadomień na tym urządzeniu.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" id="btn-toggle-push" class="btn btn-primary d-inline-flex align-items-center" disabled>
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="push-spinner" role="status"></span>
                    <i class="ti ti-bell-ringing me-1" id="push-btn-icon"></i>
                    <span id="push-btn-text">Włącz powiadomienia</span>
                </button>

                <button type="button" id="btn-test-push" class="btn btn-outline-secondary d-none" title="Wyślij testowe powiadomienie">
                    <i class="ti ti-send me-1"></i> Test
                </button>
            </div>
        </div>

        <div id="push-alert" class="alert mt-3 d-none" role="alert"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const isPushSupported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    const unsupportedEl = document.getElementById('push-unsupported');
    const controlsEl = document.getElementById('push-controls');
    const toggleBtn = document.getElementById('btn-toggle-push');
    const testBtn = document.getElementById('btn-test-push');
    const statusBadge = document.getElementById('push-status-badge');
    const permissionHint = document.getElementById('push-permission-hint');
    const btnIcon = document.getElementById('push-btn-icon');
    const btnText = document.getElementById('push-btn-text');
    const spinner = document.getElementById('push-spinner');
    const alertEl = document.getElementById('push-alert');

    if (!isPushSupported) {
        if (unsupportedEl) unsupportedEl.classList.remove('d-none');
        if (controlsEl) controlsEl.classList.add('d-none');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function showAlert(type, message) {
        if (!alertEl) return;
        alertEl.className = `alert alert-${type} alert-dismissible fade show mt-3 shadow-sm`;
        alertEl.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="ti ${type === 'success' ? 'ti-circle-check' : 'ti-alert-circle'} me-2 fs-3"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertEl.classList.remove('d-none');
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async function getRegistration() {
        return navigator.serviceWorker.ready;
    }

    async function updateUiState() {
        try {
            toggleBtn.disabled = true;
            spinner.classList.remove('d-none');

            const reg = await getRegistration();
            const sub = await reg.pushManager.getSubscription();
            const permission = Notification.permission;

            permissionHint.textContent = `(Uprawnienie: ${permission})`;

            if (sub && permission === 'granted') {
                statusBadge.className = 'badge bg-success-lt text-success';
                statusBadge.textContent = 'Powiadomienia aktywne';
                toggleBtn.className = 'btn btn-outline-danger d-inline-flex align-items-center';
                btnIcon.className = 'ti ti-bell-off me-1';
                btnText.textContent = 'Wyłącz na tym urządzeniu';
                testBtn.classList.remove('d-none');
            } else {
                statusBadge.className = 'badge bg-secondary-lt text-secondary';
                statusBadge.textContent = 'Nieaktywne';
                toggleBtn.className = 'btn btn-primary d-inline-flex align-items-center';
                btnIcon.className = 'ti ti-bell-ringing me-1';
                btnText.textContent = 'Włącz powiadomienia';
                testBtn.classList.add('d-none');
            }
        } catch (err) {
            console.error('[WebPush] Błąd sprawdzania statusu subskrypcji:', err);
        } finally {
            toggleBtn.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    toggleBtn.addEventListener('click', async () => {
        try {
            toggleBtn.disabled = true;
            spinner.classList.remove('d-none');

            const reg = await getRegistration();
            const sub = await reg.pushManager.getSubscription();

            if (sub) {
                // Wyłączanie powiadomień
                await sub.unsubscribe();

                await fetch('{{ route("push.unsubscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        endpoint: sub.endpoint
                    })
                });

                showAlert('info', 'Powiadomienia Push dla tego urządzenia zostały pomyślnie wyłączone.');
            } else {
                // Włączanie powiadomień
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    showAlert('warning', 'Nie udzielono zgody na wyświetlanie powiadomień w przeglądarce.');
                    await updateUiState();
                    return;
                }

                const resKey = await fetch('{{ route("push.key") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const keyData = await resKey.json();

                if (!keyData.publicKey) {
                    showAlert('danger', 'Brak skonfigurowanego klucza publicznego VAPID na serwerze.');
                    return;
                }

                const newSub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
                });

                const rawKey = newSub.getKey ? newSub.getKey('p256dh') : null;
                const rawAuth = newSub.getKey ? newSub.getKey('auth') : null;

                const p256dh = rawKey ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawKey))) : '';
                const auth = rawAuth ? btoa(String.fromCharCode.apply(null, new Uint8Array(rawAuth))) : '';

                const resSub = await fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        endpoint: newSub.endpoint,
                        keys: {
                            p256dh: p256dh,
                            auth: auth
                        }
                    })
                });

                if (resSub.ok) {
                    showAlert('success', 'Świetnie! Powiadomienia Push zostały aktywowane na tym urządzeniu.');
                } else {
                    showAlert('danger', 'Wystąpił problem podczas rejestracji subskrypcji na serwerze.');
                }
            }
        } catch (err) {
            console.error('[WebPush] Błąd toggle:', err);
            showAlert('danger', 'Wystąpił błąd: ' + (err.message || 'Nieznany błąd'));
        } finally {
            await updateUiState();
        }
    });

    testBtn.addEventListener('click', async () => {
        try {
            testBtn.disabled = true;
            const res = await fetch('{{ route("push.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            const data = await res.json();
            if (data.success && data.sent_count > 0) {
                showAlert('success', 'Wysłano powiadomienie testowe! Powinno pojawić się na Twoim ekranie za ułamek sekundy.');
            } else {
                showAlert('warning', data.message || 'Nie udało się dostarczyć powiadomienia testowego.');
            }
        } catch (err) {
            showAlert('danger', 'Błąd podczas wysyłania testu: ' + err.message);
        } finally {
            testBtn.disabled = false;
        }
    });

    updateUiState();
});
</script>
