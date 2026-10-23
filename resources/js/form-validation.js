// Global Client-Side Form Validation & Toast Notification
(function () {
    if (typeof window === 'undefined') return;

    let toastContainer = null;
    let toastTimeout = null;

    function getOrCreateToastContainer() {
        if (!toastContainer || !document.body.contains(toastContainer)) {
            toastContainer = document.getElementById('global-form-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'global-form-toast-container';
                toastContainer.className = 'fixed top-5 right-5 z-[99999] flex flex-col gap-2 max-w-sm w-full px-4 sm:px-0 pointer-events-none';
                document.body.appendChild(toastContainer);
            }
        }
        return toastContainer;
    }

    function showFormWarningToast(message) {
        const container = getOrCreateToastContainer();
        if (!container) return;

        // Clear existing toasts in container
        container.innerHTML = '';
        if (toastTimeout) clearTimeout(toastTimeout);

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-xs font-semibold text-amber-950 shadow-xl backdrop-blur-md transition-all duration-300 transform translate-y-[-10px] opacity-0';

        toast.innerHTML = `
            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-xl bg-amber-200 text-amber-900 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <div class="flex-1 leading-relaxed">
                <p class="font-extrabold text-amber-900 text-xs mb-0.5">Peringatan Isian Formulir</p>
                <p class="text-[11px] text-amber-800">${escapeHtml(message)}</p>
            </div>
            <button type="button" class="text-amber-600 hover:text-amber-900 p-0.5 transition" aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        const closeBtn = toast.querySelector('button');
        closeBtn.addEventListener('click', () => {
            removeToast(toast);
        });

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto dismiss after 4.5 seconds
        toastTimeout = setTimeout(() => {
            removeToast(toast);
        }, 4500);
    }

    function removeToast(toast) {
        if (!toast || !toast.parentNode) return;
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-[-10px]', 'opacity-0');
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function getFieldLabel(input) {
        // 1. Check aria-label
        const ariaLabel = input.getAttribute('aria-label');
        if (ariaLabel) return ariaLabel.trim();

        // 2. Check label with 'for' attribute
        if (input.id) {
            const labelFor = document.querySelector(`label[for="${input.id}"]`);
            if (labelFor) {
                return cleanLabelText(labelFor.textContent);
            }
        }

        // 3. Check enclosing or preceding label
        const closestParent = input.closest('div, td, .form-group');
        if (closestParent) {
            const label = closestParent.querySelector('label');
            if (label) {
                return cleanLabelText(label.textContent);
            }
        }

        // 4. Check placeholder
        const placeholder = input.getAttribute('placeholder');
        if (placeholder) return placeholder.trim();

        // 5. Fallback to name or default
        if (input.name) {
            const formatted = input.name.replace(/[_-]/g, ' ');
            return formatted.charAt(0).toUpperCase() + formatted.slice(1);
        }

        return 'Isian ini';
    }

    function cleanLabelText(text) {
        if (!text) return 'Isian ini';
        // Remove asterisk, colons, and excessive whitespaces
        return text.replace(/[*:\n\r\t]/g, ' ').replace(/\s+/g, ' ').trim();
    }

    function clearInputError(input) {
        input.classList.remove('input-invalid-shake', 'border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-50/40');
    }

    function highlightInputError(input) {
        input.classList.add('input-invalid-shake', 'border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-50/40');

        const clearHandler = function () {
            clearInputError(input);
            input.removeEventListener('input', clearHandler);
            input.removeEventListener('change', clearHandler);
        };

        input.addEventListener('input', clearHandler);
        input.addEventListener('change', clearHandler);
    }

    // Intercept form submissions across the entire document
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Skip forms opting out
        if (form.hasAttribute('data-no-validate') || form.hasAttribute('data-confirm')) {
            return;
        }

        // Check if form is valid according to HTML5 constraints
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            e.preventDefault();
            e.stopImmediatePropagation();

            // Find all invalid inputs
            const invalidFields = form.querySelectorAll(':invalid');
            if (invalidFields.length > 0) {
                const firstInvalid = invalidFields[0];
                const label = getFieldLabel(firstInvalid);

                // Highlight all invalid fields
                invalidFields.forEach(field => highlightInputError(field));

                let message = `Kolom "${label}" wajib diisi sebelum menyimpan data.`;
                if (firstInvalid.validity.typeMismatch && firstInvalid.type === 'email') {
                    message = `Format email pada kolom "${label}" tidak valid.`;
                } else if (firstInvalid.validity.rangeUnderflow || firstInvalid.validity.rangeOverflow) {
                    message = `Nilai pada kolom "${label}" berada di luar batas yang diizinkan.`;
                }

                showFormWarningToast(message);

                // Scroll and focus on the first invalid field
                try {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus({ preventScroll: true });
                } catch (_) {
                    firstInvalid.focus();
                }
            }
        }
    }, true); // Use capture phase so we intercept before other listeners
})();

