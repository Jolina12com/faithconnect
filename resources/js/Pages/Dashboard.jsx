import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';

export default function Dashboard({ auth }) {
    const [analytics, setAnalytics] = useState({
        total_messages: 0,
        emotions: {},
        active_users: 0,
        recent_messages: []
    });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchAnalytics = async () => {
            try {
                setLoading(true);
                setError(null);
                const response = await axios.get('/api/chatbot-analytics');
                console.log('Analytics data:', response.data); // Debug log
                setAnalytics(response.data);
            } catch (error) {
                console.error('Error fetching analytics:', error);
                setError(error.response?.data?.message || 'Failed to fetch analytics');
            } finally {
                setLoading(false);
            }
        };

        fetchAnalytics();
        // Refresh analytics every 5 minutes
        const interval = setInterval(fetchAnalytics, 300000);
        return () => clearInterval(interval);
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-semibold mb-4">Chatbot Analytics</h3>
                            
                            {loading && (
                                <div className="text-center py-4">
                                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                                    <p className="mt-2 text-gray-600">Loading analytics...</p>
                                </div>
                            )}

                            {error && (
                                <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4">
                                    <strong className="font-bold">Error: </strong>
                                    <span className="block sm:inline">{error}</span>
                                </div>
                            )}

                            {!loading && !error && (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        <div className="bg-blue-50 p-4 rounded-lg">
                                            <h4 className="font-medium text-blue-800">Total Messages</h4>
                                            <p className="text-2xl font-bold text-blue-600">{analytics.total_messages}</p>
                                        </div>
                                        
                                        <div className="bg-green-50 p-4 rounded-lg">
                                            <h4 className="font-medium text-green-800">Active Users (24h)</h4>
                                            <p className="text-2xl font-bold text-green-600">{analytics.active_users}</p>
                                        </div>
                                        
                                        <div className="bg-purple-50 p-4 rounded-lg">
                                            <h4 className="font-medium text-purple-800">Emotion Distribution</h4>
                                            <div className="mt-2">
                                                {Object.entries(analytics.emotions).map(([emotion, count]) => (
                                                    <div key={emotion} className="flex justify-between items-center">
                                                        <span className="capitalize">{emotion}</span>
                                                        <span className="font-medium">{count}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-6">
                                        <h4 className="font-medium mb-3">Recent Messages</h4>
                                        <div className="space-y-3">
                                            {analytics.recent_messages.length > 0 ? (
                                                analytics.recent_messages.map((message, index) => (
                                                    <div key={index} className="bg-gray-50 p-3 rounded-lg">
                                                        <div className="flex justify-between items-start">
                                                            <div>
                                                                <span className="font-medium">{message.user}</span>
                                                                <span className="text-sm text-gray-500 ml-2">{message.time}</span>
                                                            </div>
                                                            {message.emotion && (
                                                                <span className="px-2 py-1 text-xs rounded-full bg-gray-200">
                                                                    {message.emotion}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <p className="mt-1 text-gray-700">{message.message}</p>
                                                    </div>
                                                ))
                                            ) : (
                                                <p className="text-gray-500 text-center py-4">No recent messages</p>
                                            )}
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 