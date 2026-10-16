import React from 'react';

export default function Input({
    label,
    name,
    type = 'text',
    value,
    onChange,
    placeholder = '',
    error,
    helperText,
    icon: Icon,
    required = false,
    disabled = false,
    className = '',
    inputClassName = '',
    ...props
}) {
    return (
        <div className={`w-full ${className}`}>
            {label && (
                <label htmlFor={name} className="block text-xs font-bold text-slate-800 mb-1.5">
                    {label} {required && <span className="text-rose-500">*</span>}
                </label>
            )}

            <div className="relative rounded-xl shadow-2xs">
                {Icon && (
                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <Icon className="h-4 w-4" />
                    </div>
                )}

                <input
                    id={name}
                    name={name}
                    type={type}
                    value={value}
                    onChange={onChange}
                    disabled={disabled}
                    placeholder={placeholder}
                    required={required}
                    className={`block w-full rounded-xl border text-sm transition-colors duration-150 py-2.5 ${
                        Icon ? 'pl-10' : 'pl-3.5'
                    } pr-3.5 ${
                        error
                            ? 'border-rose-400 bg-rose-50/40 text-rose-900 focus:border-rose-500 focus:ring-rose-500/20'
                            : 'border-slate-300 bg-white text-slate-900 focus:border-blue-500 focus:ring-blue-500/20'
                    } disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed ${inputClassName}`}
                    {...props}
                />
            </div>

            {error ? (
                <p className="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                    <span>{error}</span>
                </p>
            ) : helperText ? (
                <p className="mt-1 text-xs text-slate-500">{helperText}</p>
            ) : null}
        </div>
    );
}

