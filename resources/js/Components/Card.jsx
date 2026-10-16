import React from 'react';

export default function Card({
    children,
    className = '',
    hoverEffect = false,
    ...props
}) {
    return (
        <div
            className={`bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 ${
                hoverEffect ? 'hover:shadow-md hover:border-slate-300 transition-all duration-200' : ''
            } ${className}`}
            {...props}
        >
            {children}
        </div>
    );
}

export function CardHeader({ title, subtitle, action, className = '' }) {
    return (
        <div className={`flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100 ${className}`}>
            <div>
                {title && <h3 className="text-base font-extrabold text-slate-900 tracking-tight">{title}</h3>}
                {subtitle && <p className="text-xs text-slate-500 mt-0.5">{subtitle}</p>}
            </div>
            {action && <div className="shrink-0">{action}</div>}
        </div>
    );
}

export function CardBody({ children, className = '' }) {
    return <div className={`space-y-4 ${className}`}>{children}</div>;
}

export function CardFooter({ children, className = '' }) {
    return (
        <div className={`pt-4 mt-4 border-t border-slate-100 flex items-center justify-between ${className}`}>
            {children}
        </div>
    );
}

