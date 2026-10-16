import React from 'react';
import { Head } from '@inertiajs/react';

export default function Test({ message, user }) {
    return (
        <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-6">
            <Head title="Inertia React Ready" />
            <div className="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">
                <div className="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 className="text-2xl font-bold text-gray-800 mb-2">Inertia.js + React Berhasil!</h1>
                <p className="text-gray-600 mb-4">{message}</p>
                {user && (
                    <div className="p-3 bg-blue-50 text-blue-700 rounded-lg text-sm mb-4">
                        Login sebagai: <strong>{user.name}</strong> ({user.role})
                    </div>
                )}
                <div className="text-xs text-gray-400 border-t pt-4">
                    Bagimu Extra &bull; Siap untuk kolaborasi Backend & Frontend
                </div>
            </div>
        </div>
    );
}

