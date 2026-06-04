{{-- Reusable confirm modal. Trigger: add class "btn-confirm-trigger" to a button inside a <form>. --}}
{{-- Supported data attributes: data-confirm-title, data-confirm-text, data-confirm-color, data-confirm-icon --}}
<div id="confirmModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.18);width:100%;max-width:400px;margin:1rem;overflow:hidden;animation:slideUp .25s cubic-bezier(.34,1.56,.64,1);">
        <div style="padding:1.4rem 1.5rem 1rem;display:flex;align-items:center;gap:.85rem;">
            <div id="confirmIconWrap" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                <i id="confirmIcon" class="bi"></i>
            </div>
            <h3 id="confirmTitle" style="font-size:1rem;font-weight:800;color:#0f172a;margin:0;"></h3>
        </div>
        <div style="padding:0 1.5rem 1.25rem;">
            <p id="confirmMsg" style="font-size:.875rem;color:#64748b;margin:0;line-height:1.6;"></p>
        </div>
        <div style="padding:1rem 1.5rem;background:#f8fafc;display:flex;justify-content:flex-end;gap:.65rem;border-top:1px solid #f1f5f9;">
            <button id="confirmCancel" style="padding:.6rem 1.2rem;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;color:#334155;font-size:.85rem;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="confirmOk" style="padding:.6rem 1.4rem;border-radius:10px;border:none;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;">Confirm</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal       = document.getElementById('confirmModal');
    const titleEl     = document.getElementById('confirmTitle');
    const msgEl       = document.getElementById('confirmMsg');
    const iconEl      = document.getElementById('confirmIcon');
    const iconWrap    = document.getElementById('confirmIconWrap');
    const okBtn       = document.getElementById('confirmOk');
    const cancelBtn   = document.getElementById('confirmCancel');
    let   pendingForm = null;

    function openConfirm({ title, text, color, icon }) {
        titleEl.textContent  = title || 'Are you sure?';
        msgEl.innerHTML      = text  || 'This action cannot be undone.';
        iconEl.className     = 'bi ' + (icon || 'bi-exclamation-triangle-fill');
        const c = color || '#ef4444';
        iconWrap.style.background = c + '18';
        iconWrap.style.color      = c;
        okBtn.style.background    = c;
        modal.style.display = 'flex';
    }

    function closeConfirm() {
        modal.style.display = 'none';
        pendingForm = null;
    }

    document.querySelectorAll('.btn-confirm-trigger').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            pendingForm = btn.closest('form');
            openConfirm({
                title : btn.dataset.confirmTitle,
                text  : btn.dataset.confirmText,
                color : btn.dataset.confirmColor,
                icon  : btn.dataset.confirmIcon,
            });
        });
    });

    okBtn?.addEventListener('click', function () {
        if (pendingForm) { closeConfirm(); pendingForm.submit(); }
    });
    cancelBtn?.addEventListener('click', closeConfirm);
    modal?.addEventListener('click', function (e) { if (e.target === modal) closeConfirm(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeConfirm(); });
    cancelBtn?.addEventListener('mouseenter', function () { cancelBtn.style.background = '#f1f5f9'; });
    cancelBtn?.addEventListener('mouseleave', function () { cancelBtn.style.background = '#fff'; });
}());
</script>
@endpush
