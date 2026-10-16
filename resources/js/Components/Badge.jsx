import React from 'react';

export default function Badge({
    children,
    variant = 'neutral',
    size = 'md',
    dot = false,
    className = '',
}) {
    const variants = {
        success: 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
        danger: 'bg-rose-50 text-rose-800 border-rose-200/80',
        warning: 'bg-amber-50 text-amber-900 border-amber-200/80',
        info: 'bg-blue-50 text-blue-800 border-blue-200/80',
        neutral: 'bg-slate-100 text-slate-700 border-slate-200/80',
    };

    const dotColors = {
        success: 'bg-emerald-500',
        danger: 'bg-rose-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500',
        neutral: 'bg-slate-400',
    };

    const sizes = {
        sm: 'px-2 py-0.5 text-[10px]',
        md: 'px-2.5 py-1 text-xs',
        lg: 'px-3 py-1.5 text-sm',
    };

    return (
        <span className={`inline-flex items-center gap-1.5 font-bold rounded-full border ${variants[variant] || variants.neutral} ${sizes[size] || sizes.md} ${className}`}>
            {dot && <span className={`h-1.5 w-1.5 rounded-full ${dotColors[variant] || dotColors.neutral}`}></span>}
            <span>{children}</span>
        </span>
    );
}

