import React, { useState, useEffect, useRef } from 'react';
import { Room, RoomEvent, VideoPresets, createLocalVideoTrack, createLocalAudioTrack } from 'livekit-client';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
    faPlay, 
    faStop, 
    faVideo, 
    faVideoSlash, 
    faMicrophone, 
    faMicrophoneSlash,
    faSignal,
    faExclamationTriangle,
    faUsers,
    faCircle,
    faExpand,
    faCompress,
    faChartLine,
    faRecordVinyl,
    faStopCircle
} from '@fortawesome/free-solid-svg-icons';
import CommentsAndReactions from './CommentsAndReactions';
import ViewersList from './ViewersList';

// Set up axios defaults
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const Broadcaster = () => {
    const [room, setRoom] = useState(null);
    const [isConnected, setIsConnected] = useState(false);
    const [error, setError] = useState(null);
    const [localVideo, setLocalVideo] = useState(null);
    const [localAudio, setLocalAudio] = useState(null);
    const [status, setStatus] = useState('Ready to start streaming');
    const [isVideoEnabled, setIsVideoEnabled] = useState(true);
    const [isAudioEnabled, setIsAudioEnabled] = useState(true);
    const [isStreaming, setIsStreaming] = useState(false);
    const [audioContext, setAudioContext] = useState(null);
    const [audioAnalyser, setAudioAnalyser] = useState(null);
    const [isInitialized, setIsInitialized] = useState(false);
    const [audioPublication, setAudioPublication] = useState(null);
    const [videoPublication, setVideoPublication] = useState(null);
    const [audioLevel, setAudioLevel] = useState(0);
    const [viewerCount, setViewerCount] = useState(0);
	const [isFullscreen, setIsFullscreen] = useState(false);
	const [showRecordingUI, setShowRecordingUI] = useState(false);
	const [isSaving, setIsSaving] = useState(false);
	const endInProgressRef = useRef(false);
    const [streamQuality, setStreamQuality] = useState('high');
    const [username, setUsername] = useState('Broadcaster');
    const [currentStreamId, setCurrentStreamId] = useState(null);
    
    // NEW: Recording states
    const [isRecording, setIsRecording] = useState(false);
    const [mediaRecorder, setMediaRecorder] = useState(null);
    const [recordedChunks, setRecordedChunks] = useState([]);
    const [egressId, setEgressId] = useState(null);
    const [recordingDuration, setRecordingDuration] = useState(0);
    const [streamTitle, setStreamTitle] = useState('');
    const [facingMode, setFacingMode] = useState('user'); // 'user' for front, 'environment' for back
    const [liveDuration, setLiveDuration] = useState(0);
    
    const containerRef = useRef(null);
    const liveStartTime = useRef(null);
    const recordingStartTime = useRef(null);
    const recordedChunksRef = useRef([]);
    const recordedUploadedRef = useRef(false);
    const uploadInProgressRef = useRef(false);
    // Minimum recording duration (milliseconds)
    const MIN_RECORDING_DURATION = 5000; // 5 seconds

    // Get or set broadcaster username
    useEffect(() => {
        const storedUsername = localStorage.getItem('broadcaster_username');
        if (storedUsername) {
            setUsername(storedUsername);
        } else {
            const broadcasterId = document.getElementById('broadcaster-id')?.value || 'Broadcaster';
            setUsername(broadcasterId);
            localStorage.setItem('broadcaster_username', broadcasterId);
        }
    }, []);

    // Recording duration timer
    useEffect(() => {
        let interval;
        if (isRecording && recordingStartTime.current) {
            interval = setInterval(() => {
                const now = Date.now();
                const duration = Math.floor((now - recordingStartTime.current) / 1000);
                setRecordingDuration(duration);
            }, 1000);
        }
        return () => clearInterval(interval);
    }, [isRecording]);

    // Live duration timer
    useEffect(() => {
        let interval;
        if (isStreaming && liveStartTime.current) {
            interval = setInterval(() => {
                const now = Date.now();
                const duration = Math.floor((now - liveStartTime.current) / 1000);
                setLiveDuration(duration);
            }, 1000);
        }
        return () => clearInterval(interval);
    }, [isStreaming]);

    // Format duration for display
    const formatDuration = (seconds) => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    // Improve cleanup function
    const cleanupTracks = async () => {
        try {
            console.log('Starting cleanup...');
            
            if (localVideo) {
                console.log('Cleaning up video track...');
                await localVideo.stop();
                setLocalVideo(null);
            }
            
            if (localAudio) {
                console.log('Cleaning up audio track...');
                await localAudio.stop();
                setLocalAudio(null);
            }
            
            if (audioContext) {
                console.log('Cleaning up audio context...');
                await audioContext.close();
                setAudioContext(null);
                setAudioAnalyser(null);
            }
            
            if (audioPublication) {
                console.log('Cleaning up audio publication...');
                await audioPublication.track.stop();
                setAudioPublication(null);
            }
            
            if (videoPublication) {
                console.log('Cleaning up video publication...');
                await videoPublication.track.stop();
                setVideoPublication(null);
            }
            
            if (room) {
                console.log('Disconnecting from room...');
                await room.disconnect();
                setRoom(null);
            }
            
            setIsInitialized(false);
            console.log('Cleanup completed');
        } catch (err) {
            console.error('Error during cleanup:', err);
        }
    };

    // Handle disconnect
const handleDisconnect = async () => {
    try {
        console.log('Starting disconnect...');
        
        // Auto-stop recording and upload if active
        if (isRecording && mediaRecorder) {
            await stopRecording(); // This will upload automatically
        }
        
        await cleanupTracks();
        setIsConnected(false);
        setIsStreaming(false);
        setStatus('Stream ended - Recording saved');
        setCurrentStreamId(null);
        console.log('Disconnect completed');
    } catch (err) {
        console.error('Disconnect error:', err);
        setError('Error disconnecting: ' + err.message);
    }
};

    // Cleanup on unmount
    useEffect(() => {
        return () => {
            // Emergency end stream on component unmount
            if (currentStreamId) {
                navigator.sendBeacon('/livekit/emergency-end-stream', JSON.stringify({
                    stream_id: currentStreamId
                }));
            }
            cleanupTracks();
        };
    }, [currentStreamId]);

    // Handle page unload - auto end stream
    useEffect(() => {
        const handleBeforeUnload = async (e) => {
            if (isStreaming || isRecording) {
                // Try to end stream before leaving
                if (isRecording && mediaRecorder) {
                    try {
                        await stopRecording();
                    } catch (err) {
                        console.error('Failed to stop recording on unload:', err);
                    }
                }
                
                // Send beacon to mark stream as ended
                if (currentStreamId) {
                    navigator.sendBeacon('/livekit/emergency-end-stream', JSON.stringify({
                        stream_id: currentStreamId
                    }));
                }
                
                e.preventDefault();
                e.returnValue = 'Stream is active. Are you sure you want to leave?';
                return e.returnValue;
            }
        };
        
        const handleUnload = () => {
            // Force end stream on actual unload
            if (currentStreamId) {
                navigator.sendBeacon('/livekit/emergency-end-stream', JSON.stringify({
                    stream_id: currentStreamId
                }));
            }
        };

        window.addEventListener('beforeunload', handleBeforeUnload);
        window.addEventListener('unload', handleUnload);
        
        return () => {
            window.removeEventListener('beforeunload', handleBeforeUnload);
            window.removeEventListener('unload', handleUnload);
        };
    }, [isStreaming, isRecording, currentStreamId, mediaRecorder]);

    // Add debug logging for audio context
    useEffect(() => {
        if (audioContext) {
            console.log('Audio Context State:', audioContext.state);
            audioContext.addEventListener('statechange', () => {
                console.log('Audio Context State Changed:', audioContext.state);
            });
        }
    }, [audioContext]);

    const initializeMedia = async () => {
        try {
            console.log('Initializing media...');
            
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                    channelCount: 2,
                    sampleRate: 48000,
                },
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: facingMode
                }
            });

            console.log('Media permissions granted');

            const context = new AudioContext();
            const analyser = context.createAnalyser();
            analyser.fftSize = 1024;
            analyser.smoothingTimeConstant = 0.8;
            
            if (context.state === 'suspended') {
                console.log('Resuming audio context...');
                await context.resume();
            }
            
            const source = context.createMediaStreamSource(stream);
            source.connect(analyser);

            setAudioContext(context);
            setAudioAnalyser(analyser);
            setIsInitialized(true);

            // Create preview video track
            const previewTrack = await createLocalVideoTrack({
                resolution: VideoPresets.h720.resolution,
                facingMode: facingMode
            });
            setLocalVideo(previewTrack);

            console.log('Media initialization completed');
            return true;
        } catch (err) {
            console.error('Media initialization error:', err);
            setError('Failed to access camera and microphone. Please ensure you have granted the necessary permissions.');
            return false;
        }
    };

    // NEW: Start Recording Function (browser MediaRecorder + backend record creation)
    const startRecording = async () => {
        if (!room || !localVideo || !localAudio) {
            setError('Cannot start recording: Stream not active');
            return;
        }

        try {
            setStatus('Starting recording...');
            
            // First, notify backend to create stream record
            const response = await axios.post('/livekit/start-recording', {
                room_name: room.name,
                title: streamTitle || 'Untitled Stream',
                stream_id: currentStreamId || `stream_${username}_${Date.now()}`
            });

            if (!response.data.success) {
                throw new Error('Failed to create stream record');
            }

            const streamId = response.data.stream_id;
            setCurrentStreamId(streamId);
            console.log('Stream record created:', streamId);

            // Get the MediaStream from LiveKit tracks
            const videoStream = localVideo.mediaStreamTrack || localVideo.track?.mediaStreamTrack;
            const audioStream = localAudio.mediaStreamTrack || localAudio.track?.mediaStreamTrack;
            
            // Combine into one MediaStream
            const combinedStream = new MediaStream([videoStream, audioStream]);

            // Create MediaRecorder
            const recorder = new MediaRecorder(combinedStream, {
                mimeType: 'video/webm;codecs=vp9,opus',
                videoBitsPerSecond: 1500000 // reduce to ~1.5 Mbps to speed uploads
            });

            recordedChunksRef.current = [];
            setRecordedChunks([]);
            
            recorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    recordedChunksRef.current.push(event.data);
                    setRecordedChunks(prev => [...prev, event.data]);

                    // Verbose debug info to help track chunks
                    const totalChunks = recordedChunksRef.current.length;
                    const totalSize = recordedChunksRef.current.reduce((sum, chunk) => sum + chunk.size, 0);
                    console.log('Chunk recorded:', {
                        size: event.data.size,
                        totalChunks,
                        totalSize
                    });
                }
            };

            // Do NOT trigger upload directly from the recorder 'stop' event.
            // stopRecording() is the single source of truth that triggers upload to avoid duplicates
            recorder.onstop = () => {
                console.log('MediaRecorder stopped, total chunks:', recordedChunksRef.current.length);
            };

            recorder.onerror = (event) => {
                console.error('MediaRecorder error:', event.error);
                setError('Recording error: ' + event.error);
            };

            // Start recording - save chunks every 1 second
            // reset upload flags for this recording session
            recordedUploadedRef.current = false;
            uploadInProgressRef.current = false;
            recorder.start(1000);
            setMediaRecorder(recorder);
            setIsRecording(true);
            recordingStartTime.current = Date.now();
            setRecordingDuration(0);
            setStatus('Recording in progress');
            
            console.log('Browser recording started - minimum 5 seconds recommended');

        } catch (err) {
            console.error('Recording start error:', err);
            setError('Failed to start recording: ' + err.message);
        }
    };

    // NEW: Stop Recording Function (browser)
	// IMPROVED: Stop Recording Function
const stopRecording = async () => {
    if (!mediaRecorder || !currentStreamId) {
        console.warn('No active recording to stop');
        return;
    }

    try {
        setStatus('Stopping recording...');

        // Check minimum duration (skip confirm when ending stream)
        const duration = recordingStartTime.current ? (Date.now() - recordingStartTime.current) : 0;
        if (!endInProgressRef.current && duration < MIN_RECORDING_DURATION) {
            const confirmStop = window.confirm('Recording is very short (less than 5 seconds). Continue stopping?');
            if (!confirmStop) {
                setStatus('Recording continues');
                return;
            }
        }

        // ✅ CRITICAL FIX: Wait for final chunks before stopping
        if (mediaRecorder.state === 'recording') {
            // Create promise to wait for final data
            const finalChunksPromise = new Promise((resolve) => {
                let dataReceived = false;
                
                const onData = (event) => {
                    if (event.data && event.data.size > 0) {
                        recordedChunksRef.current.push(event.data);
                        setRecordedChunks(prev => [...prev, event.data]);
                        console.log('Final chunk received:', event.data.size, 'bytes');
                        dataReceived = true;
                    }
                };

                const onStop = () => {
                    mediaRecorder.removeEventListener('dataavailable', onData);
                    mediaRecorder.removeEventListener('stop', onStop);
                    console.log('MediaRecorder stopped, total chunks:', recordedChunksRef.current.length);
                    resolve();
                };

                mediaRecorder.addEventListener('dataavailable', onData);
                mediaRecorder.addEventListener('stop', onStop);
                
                // Timeout fallback (5 seconds)
                setTimeout(() => {
                    if (!dataReceived) {
                        console.warn('Timeout waiting for final chunks');
                        resolve();
                    }
                }, 5000);
            });

            // ✅ Request final data BEFORE stopping
            console.log('Requesting final data...');
            try {
                if (typeof mediaRecorder.requestData === 'function') {
                    mediaRecorder.requestData();
                    // Wait a bit for the data to arrive
                    await new Promise(r => setTimeout(r, 300));
                }
            } catch (e) {
                console.warn('requestData failed:', e);
            }

            // Stop the recorder
            mediaRecorder.stop();

            // Wait for final chunks
            await finalChunksPromise;
            
            // Extra delay to ensure all events processed
            await new Promise(r => setTimeout(r, 500));
        }

        console.log('Final chunks count:', recordedChunksRef.current.length);
        console.log('Total size:', recordedChunksRef.current.reduce((sum, chunk) => sum + chunk.size, 0), 'bytes');

        // Upload immediately after stopping
        if (recordedChunksRef.current.length > 0) {
            await uploadRecording(currentStreamId);
        } else {
            console.warn('No recorded data available; skipping upload');
            setStatus('No recording data captured');
            
            // Still notify backend
            await axios.post('/livekit/stop-recording', {
                stream_id: currentStreamId,
                has_data: false
            });
        }

        setIsRecording(false);
        setMediaRecorder(null);
        recordingStartTime.current = null;
        setRecordingDuration(0);
        setStatus('Recording saved');

        console.log('Recording stopped and upload flow completed');

    } catch (err) {
        console.error('Recording stop error:', err);
        setError('Failed to stop recording: ' + err.message);
    } finally {
        // Ensure state resets even on error
        setIsRecording(false);
        setMediaRecorder(null);
        recordingStartTime.current = null;
        setRecordingDuration(0);
    }
};

    // ADD this new function for uploading:
const uploadRecording = async (streamId) => {
    console.log('\n=== UPLOAD RECORDING ===');
    console.log('Stream ID:', streamId);
    console.log('Chunks:', recordedChunksRef.current.length);
    console.log('Already uploaded?', recordedUploadedRef.current);
    console.log('Upload in progress?', uploadInProgressRef.current);

    // Prevent duplicates
    if (recordedUploadedRef.current) {
        console.log('✅ Already uploaded, skipping');
        return true;
    }

    if (uploadInProgressRef.current) {
        console.log('⏳ Upload in progress, skipping');
        return false;
    }

    if (!streamId) {
        console.error('❌ No stream ID');
        alert('ERROR: No stream ID for upload!');
        return false;
    }

    if (recordedChunksRef.current.length === 0) {
        console.error('❌ No chunks');
        alert('ERROR: No recording data!');
        return false;
    }

    try {
        uploadInProgressRef.current = true;
        setStatus('Preparing upload...');
        
        const blob = new Blob(recordedChunksRef.current, { type: 'video/webm' });
        const sizeMB = (blob.size / 1024 / 1024).toFixed(2);
        
        console.log('📦 Blob created:', sizeMB, 'MB');
        
        if (blob.size < 1000) {
            throw new Error(`Blob too small: ${blob.size} bytes`);
        }

        const formData = new FormData();
        formData.append('video', blob, `stream_${streamId}.webm`);
        formData.append('stream_id', streamId);
        
        console.log('⬆️ Uploading to /livekit/upload-recording-file...');
        console.log('Stream ID being sent:', streamId);
        
        setStatus('Uploading to server...');

        const response = await axios.post('/livekit/upload-recording-file', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (progressEvent) => {
                const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                console.log(`📊 Progress: ${percent}%`);
                setStatus(`Uploading: ${percent}%`);
            },
            timeout: 300000 // 5 min
        });

        console.log('📥 Server response:', response.data);

        if (response.data.success) {
            console.log('✅ UPLOAD SUCCESS!');
            setStatus('Recording saved successfully!');
            recordedUploadedRef.current = true;
            recordedChunksRef.current = [];
            alert('✅ Recording saved! Check the Recordings page.');
            return true;
        } else {
            throw new Error(response.data.error || 'Unknown error');
        }

    } catch (err) {
        console.error('❌ UPLOAD FAILED');
        console.error('Error:', err.message);
        console.error('Response:', err.response?.data);
        console.error('Status:', err.response?.status);
        
        alert(`Upload failed: ${err.response?.data?.error || err.message}`);
        setError('Upload failed: ' + (err.response?.data?.error || err.message));
        return false;
    } finally {
        uploadInProgressRef.current = false;
    }
};
    
    // Optional: Add this function to manually trigger upload after stream ends
    const handleStreamEnd = async () => {
        try {
            if (endInProgressRef.current) {
                console.log('End already in progress, ignoring extra click');
                return;
            }
            endInProgressRef.current = true;
            setIsSaving(true);
            setStatus('Ending stream...');

            // If recording was active, stop and upload without short-duration confirm
            if (isRecording && mediaRecorder) {
                setStatus('Saving recording...');
                const prevMin = MIN_RECORDING_DURATION;
                try {
                    // temporarily bypass minimum duration confirmation flow
                    recordingStartTime.current = recordingStartTime.current || (Date.now() - prevMin);
                    await stopRecording();
                } finally {
                    // no-op: we only spoofed start time to bypass the confirm
                }
            } else {
                // If not actively recording but there are chunks not uploaded yet, upload them
                if (recordedChunksRef.current && recordedChunksRef.current.length > 0 && !recordedUploadedRef.current) {
                    setStatus('Saving recording...');
                    try {
                        await uploadRecording(currentStreamId);
                    } catch (e) {
                        console.warn('Upload after end failed:', e);
                    }
                }
            }

            setStatus('Finalizing stream...');
            await handleDisconnect();
            setStatus('Stream ended');
        } catch (err) {
            console.error('Stream end error:', err);
            setError('Failed to end stream: ' + err.message);
        } finally {
            endInProgressRef.current = false;
            setIsSaving(false);
        }
    };

const startStream = async () => {
    const broadcasterId = document.getElementById('broadcaster-id')?.value;
    if (!broadcasterId) {
        setError('Broadcaster ID not found');
        return;
    }

    try {
        setStatus('Initializing stream...');
        console.log('Starting new stream...');

        await cleanupTracks();

        const newStreamId = `stream_${broadcasterId}_${Date.now()}`;
        setCurrentStreamId(newStreamId);
        console.log('Generated new stream ID:', newStreamId);

        if (!isInitialized) {
            console.log('Initializing media for new stream...');
            const initialized = await initializeMedia();
            if (!initialized) {
                return;
            }
        }

        if (audioContext && audioContext.state === 'suspended') {
            console.log('Resuming audio context for new stream...');
            await audioContext.resume();
        }
        
        const urlResponse = await axios.get('/livekit/url');
        const livekitUrl = urlResponse.data.url;

        const tokenResponse = await axios.post('/livekit/token', {
            user_id: broadcasterId,
            metadata: JSON.stringify({ streamId: newStreamId })
        });
        const token = tokenResponse.data.token;

        const newRoom = new Room({
            adaptiveStream: true,
            dynacast: true,
            videoCaptureDefaults: {
                resolution: VideoPresets.h720.resolution,
            },
            connectOptions: {
                autoSubscribe: true,
                rtcConfig: {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun1.l.google.com:19302' },
                    ],
                },
            },
            metadata: JSON.stringify({ streamId: newStreamId })
        });
        setRoom(newRoom);

        // ... (keep all event handlers the same) ...

        await newRoom.connect(livekitUrl, token);
        setStatus('Connecting to LiveKit...');

        const videoTrack = await createLocalVideoTrack({
            resolution: VideoPresets.h720.resolution,
        });
        setLocalVideo(videoTrack);

        const audioTrack = await createLocalAudioTrack({
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            channelCount: 2,
            sampleRate: 48000,
        });
        setLocalAudio(audioTrack);

        // ... (keep audio track setup) ...

        if (videoTrack) {
            const vidPub = await newRoom.localParticipant.publishTrack(videoTrack);
            setVideoPublication(vidPub);
            console.log('Video track published:', vidPub);
        }

        if (audioTrack) {
            audioTrack.unmute();
            const audioPub = await newRoom.localParticipant.publishTrack(audioTrack, {
                name: 'microphone'
            });
            setAudioPublication(audioPub);
            console.log('Audio track published successfully:', audioPub);
        }

        setIsStreaming(true);
        setIsVideoEnabled(true);
        setIsAudioEnabled(true);
        setStatus('Streaming live');
        liveStartTime.current = Date.now();
        setLiveDuration(0);
        console.log('Stream started successfully');

		// ✅ AUTO-START RECORDING AFTER STREAM IS LIVE USING ACTUAL TRACKS
		setTimeout(async () => {
			await startRecordingWithTracks(newRoom, videoTrack, audioTrack);
		}, 2000);

    } catch (err) {
        console.error('Stream initialization error:', err);
        setError(err.message);
        setStatus('Error: ' + err.message);
        await cleanupTracks();
    }
};

// ✅ NEW FUNCTION: Start recording with actual track references
const startRecordingWithTracks = async (activeRoom, videoTrack, audioTrack) => {
    console.log('=== START RECORDING WITH TRACKS ===');
    console.log('Room:', activeRoom?.name);
    console.log('Video track:', videoTrack?.mediaStreamTrack);
    console.log('Audio track:', audioTrack?.mediaStreamTrack);

    if (!activeRoom || !videoTrack || !audioTrack) {
        console.error('ERROR: Missing room or tracks');
        setError('Cannot start recording: missing resources');
        return;
    }

    try {
        setStatus('Creating stream record...');
        
        // Create DB record
        const response = await axios.post('/livekit/start-recording', {
            room_name: activeRoom.name,
            title: streamTitle || `Stream ${new Date().toLocaleString()}`,
            stream_id: `stream_${username}_${Date.now()}`
        });

        console.log('Backend response:', response.data);

        if (!response.data.success) {
            throw new Error('Backend failed to create stream record');
        }

        const dbStreamId = response.data.stream_id;
        setCurrentStreamId(dbStreamId);
        console.log('✅ Stream record created, DB ID:', dbStreamId);

        // Get MediaStreamTracks
        const videoStream = videoTrack.mediaStreamTrack;
        const audioStream = audioTrack.mediaStreamTrack;
        
        if (!videoStream || !audioStream) {
            throw new Error('MediaStreamTracks not available');
        }

        const combinedStream = new MediaStream([videoStream, audioStream]);
        console.log('Combined stream tracks:', combinedStream.getTracks().length);

        // Determine best MIME type with better compatibility
        let mimeType = 'video/webm;codecs=vp8,opus';
        let recorderOptions = {
            mimeType: mimeType,
            videoBitsPerSecond: 2500000
        };
        
        if (!MediaRecorder.isTypeSupported(mimeType)) {
            mimeType = 'video/webm';
            recorderOptions.mimeType = mimeType;
            console.warn('Using fallback MIME type:', mimeType);
        }

        const recorder = new MediaRecorder(combinedStream, recorderOptions);

        // Reset refs
        recordedChunksRef.current = [];
        recordedUploadedRef.current = false;
        uploadInProgressRef.current = false;

        // ✅ IMPROVED: Better chunk handling
        recorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) {
                recordedChunksRef.current.push(event.data);
                const totalSize = recordedChunksRef.current.reduce((sum, chunk) => sum + chunk.size, 0);
                const totalMB = (totalSize / 1024 / 1024).toFixed(2);
                console.log(`📦 Chunk ${recordedChunksRef.current.length}: ${(event.data.size/1024).toFixed(1)}KB (Total: ${totalMB}MB)`);
                
                // Update state for UI
                setRecordedChunks(prev => [...prev, event.data]);
            } else {
                console.warn('⚠️ Empty chunk received');
            }
        };

        recorder.onstop = () => {
            console.log('🛑 MediaRecorder stopped');
            console.log('Final chunk count:', recordedChunksRef.current.length);
        };

        recorder.onerror = (event) => {
            console.error('❌ MediaRecorder error:', event.error);
            setError('Recording error: ' + event.error);
        };

        // ✅ Start with 1-second timeslice (good for short recordings)
        recorder.start(1000);
        setMediaRecorder(recorder);
        setIsRecording(true);
        setShowRecordingUI(false);
        recordingStartTime.current = Date.now();
        setRecordingDuration(0);
        setStatus('🔴 Recording in progress');
        
        console.log('✅ MediaRecorder started successfully');

    } catch (err) {
        console.error('❌ Recording start failed:', err);
        console.error('Error details:', err.response?.data || err.message);
        setError('Failed to start recording: ' + (err.response?.data?.error || err.message));
    }
};

// 🔥 NEW: Auto-start recording function
const startRecordingAuto = async (streamId) => {
    if (!room || !localVideo || !localAudio) {
        console.error('Cannot auto-start recording: tracks not ready');
        return;
    }

    try {
        setStatus('Starting automatic recording...');
        
        // Create stream record in backend
        const response = await axios.post('/livekit/start-recording', {
            room_name: room.name,
            title: streamTitle || 'Livestream ' + new Date().toLocaleString(),
            stream_id: streamId
        });

        if (!response.data.success) {
            throw new Error('Failed to create stream record');
        }

        const dbStreamId = response.data.stream_id;
        setCurrentStreamId(dbStreamId);
        console.log('Stream record created:', dbStreamId);

        // Get MediaStream from LiveKit tracks
        const videoStream = localVideo.mediaStreamTrack || localVideo.track?.mediaStreamTrack;
        const audioStream = localAudio.mediaStreamTrack || localAudio.track?.mediaStreamTrack;
        
        const combinedStream = new MediaStream([videoStream, audioStream]);

        // Create MediaRecorder
        const recorder = new MediaRecorder(combinedStream, {
            mimeType: 'video/webm;codecs=vp9,opus',
            videoBitsPerSecond: 1500000
        });

        recordedChunksRef.current = [];
        setRecordedChunks([]);
        
        recorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunksRef.current.push(event.data);
                setRecordedChunks(prev => [...prev, event.data]);
            }
        };

        recorder.onstop = () => {
            console.log('Auto-recording stopped, total chunks:', recordedChunksRef.current.length);
        };

        recorder.onerror = (event) => {
            console.error('MediaRecorder error:', event.error);
        };

        recordedUploadedRef.current = false;
        uploadInProgressRef.current = false;
        recorder.start(1000);
        setMediaRecorder(recorder);
        setIsRecording(true);
        recordingStartTime.current = Date.now();
        setRecordingDuration(0);
        setStatus('Recording in progress (auto)');
        
        console.log('Auto-recording started');

    } catch (err) {
        console.error('Auto-recording start error:', err);
        setError('Failed to start auto-recording: ' + err.message);
    }
};

    const toggleVideo = async () => {
        if (!room || !localVideo) return;
        
        try {
            if (isVideoEnabled) {
                localVideo.mute();
            } else {
                localVideo.unmute();
            }
            setIsVideoEnabled(!isVideoEnabled);
        } catch (err) {
            console.error('Video toggle error:', err);
            setError('Failed to toggle video: ' + err.message);
        }
    };

    const toggleAudio = async () => {
        if (!room || !localAudio) return;
        
        try {
            if (isAudioEnabled) {
                localAudio.mute();
            } else {
                localAudio.unmute();
            }
            setIsAudioEnabled(!isAudioEnabled);
            
            console.log('Audio enabled:', !isAudioEnabled);
            console.log('Audio track muted:', localAudio.isMuted);
        } catch (err) {
            console.error('Audio toggle error:', err);
            setError('Failed to toggle audio: ' + err.message);
        }
    };

    // Add audio track monitoring
    useEffect(() => {
        if (!audioAnalyser || !localAudio) return;
        
        const checkAudioLevel = () => {
            try {
                const dataArray = new Uint8Array(audioAnalyser.frequencyBinCount);
                audioAnalyser.getByteFrequencyData(dataArray);
                const average = dataArray.reduce((a, b) => a + b, 0) / dataArray.length;
                setAudioLevel(average);
                
                if (average > 15) {
                    console.log('Audio level:', average);
                }
                
                if (isAudioEnabled && localAudio && localAudio.isMuted) {
                    console.log('Audio should be on but is muted, unmuting...');
                    localAudio.unmute();
                }
            } catch (err) {
                console.error('Audio monitoring error:', err);
            }
        };

        const interval = setInterval(checkAudioLevel, 300);
        return () => clearInterval(interval);
    }, [audioAnalyser, localAudio, isAudioEnabled]);

    // Add fullscreen toggle
    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            containerRef.current.requestFullscreen();
            setIsFullscreen(true);
        } else {
            document.exitFullscreen();
            setIsFullscreen(false);
        }
    };

    const switchCamera = async () => {
        if (!isStreaming) return;
        
        try {
            const newFacingMode = facingMode === 'user' ? 'environment' : 'user';
            setStatus(`Switching camera...`);
            
            // Stop and save recording state
            const wasRecording = isRecording;
            let savedChunks = [];
            
            if (wasRecording && mediaRecorder && mediaRecorder.state === 'recording') {
                savedChunks = [...recordedChunksRef.current];
                mediaRecorder.stop();
                await new Promise(r => setTimeout(r, 300));
            }
            
            // Stop current video track
            if (localVideo) {
                localVideo.stop();
            }
            
            // Create new video track with fallback
            let newVideoTrack;
            try {
                newVideoTrack = await createLocalVideoTrack({
                    resolution: VideoPresets.h720.resolution,
                    facingMode: newFacingMode
                });
            } catch (e) {
                console.warn('Failed with facingMode, trying deviceId fallback');
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(d => d.kind === 'videoinput');
                if (videoDevices.length > 1) {
                    const currentDevice = videoDevices.find(d => d.label.includes(facingMode === 'user' ? 'front' : 'back'));
                    const nextDevice = videoDevices.find(d => d.deviceId !== currentDevice?.deviceId);
                    newVideoTrack = await createLocalVideoTrack({
                        deviceId: nextDevice.deviceId,
                        resolution: VideoPresets.h720.resolution
                    });
                } else {
                    throw new Error('No alternative camera found');
                }
            }
            
            // Replace track in room
            if (room && videoPublication) {
                await room.localParticipant.unpublishTrack(videoPublication.track);
                const newPub = await room.localParticipant.publishTrack(newVideoTrack);
                setVideoPublication(newPub);
            }
            
            setLocalVideo(newVideoTrack);
            setFacingMode(newFacingMode);
            
            // Restart recording with new camera
            if (wasRecording) {
                const videoStream = newVideoTrack.mediaStreamTrack;
                const audioStream = localAudio.mediaStreamTrack || localAudio.track?.mediaStreamTrack;
                const newCombinedStream = new MediaStream([videoStream, audioStream]);
                
                const newRecorder = new MediaRecorder(newCombinedStream, {
                    mimeType: 'video/webm;codecs=vp8,opus',
                    videoBitsPerSecond: 2500000
                });
                
                recordedChunksRef.current = savedChunks;
                
                newRecorder.ondataavailable = (event) => {
                    if (event.data && event.data.size > 0) {
                        recordedChunksRef.current.push(event.data);
                        setRecordedChunks(prev => [...prev, event.data]);
                    }
                };
                
                newRecorder.onstop = () => {
                    console.log('MediaRecorder stopped');
                };
                
                newRecorder.onerror = (event) => {
                    console.error('MediaRecorder error:', event.error);
                };
                
                newRecorder.start(1000);
                setMediaRecorder(newRecorder);
                console.log('Recording restarted with new camera');
            }
            
            setStatus('Streaming live');
            
        } catch (err) {
            console.error('Camera switch failed:', err);
            setError('Camera switch failed: ' + err.message);
            setStatus('Streaming live');
        }
    };

    // Initialize camera on mount
    useEffect(() => {
        initializeMedia();
    }, []);

    return (
        <div ref={containerRef} className="stream-container">
            <div className="video-container" onDoubleClick={isStreaming ? switchCamera : undefined}>
                <video
                    ref={(el) => {
                        if (el && localVideo) {
                            localVideo.attach(el);
                            if (el.paused) {
                                el.play().catch(err => {
                                    console.warn('Video play failed:', err);
                                });
                            }
                        }
                    }}
                    className={facingMode === 'user' ? 'front-camera' : ''}
                    autoPlay
                    playsInline
                    muted={true}
                />
                
                {isStreaming && (
                    <div className="video-overlay">
                        <div className="stream-info">
                            <div className="status-indicator">
                                <FontAwesomeIcon icon={faCircle} className="live-indicator" />
                                <span>LIVE {formatDuration(liveDuration)}</span>
                                {/* NEW: Recording indicator */}
                                {isRecording && showRecordingUI && (
                                    <div className="recording-indicator">
                                        <FontAwesomeIcon icon={faRecordVinyl} className="recording-icon" />
                                        <span>REC {formatDuration(recordingDuration)}</span>
                                    </div>
                                )}
                            </div>
                            <ViewersList room={room} />
                        </div>
                        
                        <button 
                            className="fullscreen-button"
                            onClick={toggleFullscreen}
                            title={isFullscreen ? "Exit Fullscreen" : "Enter Fullscreen"}
                        >
                            <FontAwesomeIcon icon={isFullscreen ? faCompress : faExpand} />
                        </button>

                        {isSaving && (
                            <div className="saving-overlay">
                                <div className="saving-box">
                                    <div className="spinner" />
                                    <div className="saving-text">Saving recording...</div>
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>

            {!isStreaming && (
                <>
                    <div className="audio-meter">
                        <div className="audio-level" style={{ width: `${audioLevel}%`, height: '8px', backgroundColor: `rgb(${audioLevel * 2}, 200, 100)`, borderRadius: '4px' }}></div>
                        <div className="audio-level-label">
                            <FontAwesomeIcon icon={faChartLine} />
                            <span>Audio Level</span>
                        </div>
                    </div>

                    <div className="quality-indicator">
                        <FontAwesomeIcon icon={faSignal} />
                        <span>Quality: {streamQuality}</span>
                    </div>
                </>
            )}

            {!isStreaming && (
                <div className="stream-setup-overlay">
                    <input
                        type="text"
                        placeholder="Enter stream title (optional)"
                        value={streamTitle}
                        onChange={(e) => setStreamTitle(e.target.value)}
                        className="stream-title-input"
                        maxLength={100}
                    />
                </div>
            )}

            <div className="controls">
                {!isStreaming ? (
                    <button
                        onClick={startStream}
                        className="control-button start-button"
                        title="Start Stream"
                    >
                        <FontAwesomeIcon icon={faPlay} />
                    </button>
                ) : (
                    <div className="control-buttons">
                        <button
                            onClick={toggleVideo}
                            className={`control-button ${!isVideoEnabled ? 'disabled' : ''}`}
                            title={isVideoEnabled ? 'Turn Off Video' : 'Turn On Video'}
                        >
                            <FontAwesomeIcon icon={isVideoEnabled ? faVideo : faVideoSlash} />
                        </button>
                        <button
                            onClick={toggleAudio}
                            className={`control-button ${!isAudioEnabled ? 'disabled' : ''}`}
                            title={isAudioEnabled ? 'Turn Off Audio' : 'Turn On Audio'}
                        >
                            <FontAwesomeIcon icon={isAudioEnabled ? faMicrophone : faMicrophoneSlash} />
                        </button>
                        <button
                            onClick={handleStreamEnd}
                            className={`control-button danger ${(endInProgressRef.current || isSaving) ? 'disabled' : ''}`}
                            title={(endInProgressRef.current || isSaving) ? 'Ending...' : 'End Stream'}
                            disabled={endInProgressRef.current || isSaving}
                        >
                            <FontAwesomeIcon icon={faStop} />
                        </button>
                    </div>
                )}
            </div>

            <div className="status">
                {error ? (
                    <div className="error">
                        <FontAwesomeIcon icon={faExclamationTriangle} /> {error}
                    </div>
                ) : (
                    <div>
                        <FontAwesomeIcon icon={faSignal} /> {status}
                        {isStreaming && (
                            <span className="stream-status">
                                • Audio: {isAudioEnabled ? 'On' : 'Off'}, Video: {isVideoEnabled ? 'On' : 'Off'}
                                {isRecording && showRecordingUI && ` • Recording: ${formatDuration(recordingDuration)}`}
                            </span>
                        )}
                    </div>
                )}
            </div>

            {/* Comments and Reactions Component */}
            {room && isStreaming && (
                <CommentsAndReactions 
                    room={room} 
                    username={username} 
                    isBroadcaster={true}
                    streamId={currentStreamId} 
                />
            )}

            <style>
                {`
                    .stream-container {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 1rem;
                        padding: 1rem;
                        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
                        border-radius: 16px;
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                        min-height: 100vh;
                        width: 100%;
                        max-width: 100%;
                        box-sizing: border-box;
                    }
                    
                    @media (max-width: 768px) {
                        body {
                            overflow: hidden;
                        }
                        
                        .stream-container {
                            padding: 0;
                            gap: 0;
                            border-radius: 0;
                            height: calc(100vh - 60px);
                            background: #000;
                            overflow: hidden;
                            position: fixed;
                            top: 60px;
                            left: 0;
                            right: 0;
                        }
                    }

                    .stream-setup-overlay {
                        position: absolute;
                        top: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        z-index: 10;
                        width: 85%;
                        max-width: 350px;
                    }

                    .stream-title-input {
                        width: 100%;
                        padding: 12px 16px;
                        border: none;
                        border-radius: 8px;
                        background: rgba(255, 255, 255, 0.1);
                        color: white;
                        font-size: 0.95rem;
                        transition: all 0.2s ease;
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                    }

                    .stream-title-input:focus {
                        outline: none;
                        background: rgba(255, 255, 255, 0.15);
                        border-color: rgba(255, 255, 255, 0.4);
                    }

                    .stream-title-input::placeholder {
                        color: rgba(255, 255, 255, 0.6);
                    }
                    
                    @media (max-width: 768px) {
                        .stream-setup-overlay {
                            top: 20px;
                            width: 85%;
                            max-width: 350px;
                        }
                        
                        .stream-title-input {
                            padding: 10px 14px;
                            font-size: 0.9rem;
                            border-radius: 20px;
                        }
                    }

                    .video-container {
                        width: 100%;
                        max-width: 100%;
                        aspect-ratio: 16/9;
                        background: linear-gradient(45deg, #000, #1a1a1a);
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
                        position: relative;
                        cursor: ${isStreaming ? 'pointer' : 'default'};
                        user-select: none;
                        border: 2px solid #333;
                        transition: all 0.3s ease;
                    }
                    
                    .video-container:hover {
                        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.7);
                        border-color: #555;
                    }
                    
                    @media (min-width: 769px) {
                        .video-container {
                            max-width: 900px;
                            border-radius: 16px;
                        }
                    }
                    
                    @media (max-width: 768px) {
                        .video-container {
                            border-radius: 0;
                            border: none;
                            margin: 0;
                            aspect-ratio: unset;
                            height: calc(100vh - 60px);
                            max-width: 100vw;
                        }
                    }

                    .video-container video {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    
                    .video-container video.front-camera {
                        transform: scaleX(-1);
                    }
                    
                    @media (max-width: 768px) {
                        .video-container video {
                            object-fit: cover;
                            object-position: center;
                        }
                        
                        .video-container video.front-camera {
                            transform: scaleX(-1);
                        }
                    }

                    .video-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        padding: 1rem;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        background: linear-gradient(to bottom, 
                            rgba(0,0,0,0.7) 0%,
                            rgba(0,0,0,0) 20%,
                            rgba(0,0,0,0) 80%,
                            rgba(0,0,0,0.7) 100%
                        );
                        pointer-events: none;
                        z-index: 1;
                    }

                    .stream-info {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        pointer-events: auto;
                        width: 100%;
                    }

                    .status-indicator {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: white;
                        font-weight: 700;
                        background: rgba(0, 0, 0, 0.7);
                        padding: 0.5rem 1rem;
                        border-radius: 20px;
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        font-size: 0.9rem;
                    }
                    
                    @media (max-width: 768px) {
                        .status-indicator {
                            font-size: 0.7rem;
                            padding: 0.3rem 0.6rem;
                            gap: 0.3rem;
                            border-radius: 15px;
                        }
                        
                        .live-indicator {
                            font-size: 0.6rem;
                        }
                        
                        .stream-info {
                            flex-direction: row;
                            align-items: flex-start;
                            gap: 0.5rem;
                            justify-content: space-between;
                            width: 100%;
                        }
                    }

                    .live-indicator {
                        color: #ff4444;
                        animation: pulse 2s infinite;
                    }

                    .recording-indicator {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        margin-left: 1rem;
                        background: rgba(255, 0, 0, 0.8);
                        padding: 0.25rem 0.75rem;
                        border-radius: 15px;
                        font-size: 0.85rem;
                    }

                    .recording-icon {
                        color: white;
                        animation: pulse 1.5s infinite;
                    }

                    .viewer-count {
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: white;
                        background: rgba(0, 0, 0, 0.5);
                        padding: 0.5rem 1rem;
                        border-radius: 20px;
                    }

                    .fullscreen-button {
                        position: absolute;
                        bottom: 1rem;
                        right: 1rem;
                        background: rgba(0, 0, 0, 0.5);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 40px;
                        height: 40px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        transition: all 0.2s;
                        pointer-events: auto;
                    }

                    .fullscreen-button:hover {
                        background: rgba(0, 0, 0, 0.7);
                        transform: scale(1.1);
                    }
                    
                    .audio-meter {
                        width: 100%;
                        max-width: 100%;
                        height: 10px;
                        background: linear-gradient(90deg, #333, #444);
                        border-radius: 6px;
                        overflow: hidden;
                        position: relative;
                        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
                        border: 1px solid rgba(255, 255, 255, 0.1);
                    }

                    .audio-level-label {
                        position: absolute;
                        top: -24px;
                        right: 0;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: #fff;
                        font-size: 0.85rem;
                        font-weight: 600;
                        background: rgba(0, 0, 0, 0.5);
                        padding: 0.25rem 0.5rem;
                        border-radius: 4px;
                        backdrop-filter: blur(5px);
                    }
                    
                    @media (max-width: 768px) {
                        .audio-meter {
                            display: none;
                        }
                    }

                    .controls {
                        display: flex;
                        gap: 1rem;
                        margin: 1rem 0;
                        position: relative;
                        z-index: 2;
                        justify-content: center;
                        flex-wrap: wrap;
                        width: 100%;
                        max-width: 500px;
                    }
                    
                    @media (max-width: 768px) {
                        .controls {
                            position: fixed;
                            bottom: 20px;
                            left: 50%;
                            transform: translateX(-50%);
                            gap: 1rem;
                            margin: 0;
                            padding: 0;
                            z-index: 1000;
                            width: auto;
                        }
                        
                        .control-buttons {
                            gap: 1rem;
                        }
                    }
                    .saving-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: rgba(0,0,0,0.4);
                        pointer-events: none;
                    }

                    .saving-box {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 0.75rem;
                        background: rgba(0,0,0,0.7);
                        color: white;
                        padding: 1rem 1.25rem;
                        border-radius: 8px;
                    }

                    .spinner {
                        width: 28px;
                        height: 28px;
                        border: 3px solid rgba(255,255,255,0.3);
                        border-top-color: #fff;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }

                    @keyframes spin {
                        to { transform: rotate(360deg); }
                    }

                    .control-buttons {
                        display: flex;
                        gap: 1rem;
                    }

                    .control-button {
                        width: 56px;
                        height: 56px;
                        border: none;
                        border-radius: 50%;
                        font-size: 1.3rem;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                        backdrop-filter: blur(10px);
                        border: 2px solid rgba(255, 255, 255, 0.1);
                    }
                    
                    .control-button:active {
                        transform: scale(0.95);
                    }
                    
                    @media (max-width: 768px) {
                        .control-button {
                            width: 48px;
                            height: 48px;
                            font-size: 1rem;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
                            background: rgba(255, 255, 255, 0.15);
                            backdrop-filter: blur(15px);
                            border: 1.5px solid rgba(255, 255, 255, 0.25);
                        }
                        
                        .control-button:not(.start-button):not(.danger) {
                            background: rgba(255, 255, 255, 0.2);
                        }
                        
                        .control-button.danger {
                            background: rgba(244, 67, 54, 0.85);
                        }
                    }

                    .start-button {
                        background: linear-gradient(135deg, #4CAF50, #45a049);
                        width: 80px;
                        height: 80px;
                        font-size: 1.8rem;
                        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
                        border: 3px solid rgba(255, 255, 255, 0.2);
                    }

                    .start-button:hover {
                        background: linear-gradient(135deg, #45a049, #3d8b40);
                        transform: scale(1.05);
                        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.6);
                    }
                    
                    @media (max-width: 768px) {
                        .start-button {
                            width: 60px;
                            height: 60px;
                            font-size: 1.4rem;
                            background: rgba(76, 175, 80, 0.9);
                            backdrop-filter: blur(15px);
                            border: 2px solid rgba(255, 255, 255, 0.3);
                        }
                    }

                    .control-button:not(.start-button):not(.record-button):not(.stop-record-button):not(.danger) {
                        background: #2196F3;
                    }

                    .control-button:not(.start-button):not(.record-button):not(.stop-record-button):not(.danger):hover {
                        background: #1976D2;
                        transform: scale(1.05);
                    }

                    .record-button {
                        background: #FF4444;
                    }

                    .record-button:hover {
                        background: #FF2222;
                        transform: scale(1.05);
                    }

                    .stop-record-button {
                        background: #FF6B6B;
                        animation: pulse 2s infinite;
                    }

                    .stop-record-button:hover {
                        background: #FF5252;
                        transform: scale(1.05);
                    }

                    .control-button.disabled {
                        background: #666;
                        cursor: not-allowed;
                    }

                    .danger {
                        background: #f44336;
                    }

                    .danger:hover {
                        background: #d32f2f;
                    }

                    .status {
                        padding: 1rem 1.5rem;
                        border-radius: 12px;
                        background: linear-gradient(135deg, #2a2a2a, #3a3a3a);
                        font-size: 0.95rem;
                        color: #fff;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        width: 100%;
                        max-width: 100%;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        backdrop-filter: blur(10px);
                        text-align: center;
                        justify-content: center;
                        flex-wrap: wrap;
                    }
                    
                    @media (max-width: 768px) {
                        .status {
                            position: fixed;
                            bottom: 110px;
                            left: 50%;
                            transform: translateX(-50%);
                            padding: 0.6rem 1.2rem;
                            font-size: 0.8rem;
                            border-radius: 25px;
                            margin: 0;
                            max-width: 85%;
                            z-index: 999;
                            background: rgba(0, 0, 0, 0.75);
                            backdrop-filter: blur(20px);
                            border: 1px solid rgba(255, 255, 255, 0.2);
                        }
                    }

                    .stream-status {
                        color: #aaa;
                        margin-left: 0.5rem;
                    }

                    .error {
                        color: #f44336;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                    }

                    .quality-indicator {
                        position: absolute;
                        top: 5rem;
                        right: 1rem;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        color: #fff;
                        font-size: 0.9rem;
                        background: rgba(0, 0, 0, 0.5);
                        padding: 0.5rem 0.75rem;
                        border-radius: 20px;
                        backdrop-filter: blur(10px);
                    }

                    @keyframes pulse {
                        0% {
                            opacity: 1;
                        }
                        50% {
                            opacity: 0.5;
                        }
                        100% {
                            opacity: 1;
                        }
                    }
                    
                    /* Mobile-specific improvements */
                    @media (max-width: 768px) {
                        .video-container video {
                            object-fit: cover;
                        }
                        
                        .fullscreen-button {
                            width: 48px;
                            height: 48px;
                            bottom: 0.75rem;
                            right: 0.75rem;
                            font-size: 1.2rem;
                        }
                        
                        .recording-indicator {
                            font-size: 0.8rem;
                            padding: 0.2rem 0.6rem;
                            margin-left: 0.5rem;
                        }
                        
                        .stream-status {
                            font-size: 0.8rem;
                            margin-left: 0.25rem;
                        }
                        
                        .error {
                            font-size: 0.9rem;
                        }
                    }
                    
                    /* Landscape mobile optimization */
                    @media (max-width: 768px) and (orientation: landscape) {
                        .stream-container {
                            padding: 0.25rem;
                            gap: 0.5rem;
                        }
                        
                        .video-container {
                            max-height: 70vh;
                        }
                        
                        .controls {
                            margin: 0.5rem 0;
                        }
                        
                        .status {
                            padding: 0.5rem 1rem;
                            font-size: 0.85rem;
                        }
                    }
                    
                    /* Touch device optimizations */
                    @media (hover: none) and (pointer: coarse) {
                        .control-button {
                            min-width: 60px;
                            min-height: 60px;
                        }
                        
                        .start-button {
                            min-width: 90px;
                            min-height: 90px;
                        }
                        
                        .video-container {
                            -webkit-tap-highlight-color: transparent;
                        }
                    }
                    
                    /* High DPI displays */
                    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
                        .control-button {
                            border-width: 1px;
                        }
                        
                        .video-container {
                            border-width: 1px;
                        }
                    }
                `}
            </style>
        </div>
    );
};

export default Broadcaster;