import './bootstrap'
import React, { useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Broadcaster from './components/Broadcaster';
import { createLocalVideoTrack } from 'livekit-client';
import Viewer from './components/Viewer';
import '../css/livestream.css';

// Mount the React app if the broadcaster element exists
const broadcasterElement = document.getElementById('broadcaster-app');
if (broadcasterElement) {
    const root = createRoot(broadcasterElement);
    root.render(
        <Broadcaster />
    );
}

// Mount the React app if the viewer element exists
const viewerElement = document.getElementById('viewer-app');
if (viewerElement) {
    const root = createRoot(viewerElement);
    root.render(
        <Viewer />
    );
}

// Keep any other JavaScript initialization here

const ViewerStream = () => {
    const videoRef = useRef(null);
    const [status, setStatus] = useState('Connecting...');
    const [isOffline, setIsOffline] = useState(false);

    useEffect(() => {
        // Example: Replace with your actual WebRTC/WebSocket/stream logic
        const streamId = document.getElementById('stream-id')?.value;

        // Simulate connecting to a stream
        setTimeout(() => {
            // Simulate stream found
            if (streamId) {
                setStatus('Live');
                setIsOffline(false);
                // Here you would attach the real stream to videoRef.current.srcObject
            } else {
                setStatus('Offline');
                setIsOffline(true);
            }
        }, 2000);

        // Cleanup logic if needed
        return () => {};
    }, []);

    return (
        <div className="stream-container">
            <h1>Live Stream</h1>
            <div id="streamContainer">
                <video
                    ref={videoRef}
                    id="remoteVideo"
                    autoPlay
                    playsInline
                    controls
                    style={{ display: isOffline ? 'none' : 'block' }}
                />
            </div>
            {isOffline && (
                <div className="offline-message" id="offlineMessage" style={{ display: 'block' }}>
                    <h2>Live stream is currently offline</h2>
                    <p>Please check back later when the broadcaster goes live.</p>
                </div>
            )}
            <div className="status" id="status">
                Status: {status}
            </div>
        </div>
    );
};

export default ViewerStream;
