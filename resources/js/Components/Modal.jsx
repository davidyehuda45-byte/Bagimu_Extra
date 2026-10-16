import React, { useEffect } from 'react';
import { X } from 'lucide-react';

export default function Modal({
    isOpen = false,
    onClose,
    title,
    description,
    children,
    maxWidth = 'max-w-md',
}) {
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (e.key === 'Escape' && isOpen) {
                onClose?.();
            }
        };

        if (isOpen) {
            document.body.style.overflow = 'hidden';
            window.addEventListener('keydown', handleKeyDown);
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity animate-fade-in"
                onClick={onClose}
            />

            {/* Modal Dialog Content */}
            <div className={`relative w-full ${maxWidth} rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-slate-100 z-10 transition-all transform animate-scale-up`}>
                <div className="flex items-start justify-between gap-4 mb-4">
                    <div>
                        {title && <h3 className="text-lg font-extrabold text-slate-900 tracking-tight">{title}</h3>}
                        {description && <p className="text-xs text-slate-500 mt-1">{description}</p>}
                    </div>
                    {onClose && (
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                        >
                            <X className="h-5 w-5" />
                        </button>
                    )}
                </div>

                <div>{children}</div>
            </div>
        </div>
    );
}

