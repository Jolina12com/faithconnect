// resources/js/components/Viewer.jsx
import React, { useEffect, useRef, useState } from 'react';
import {
  Room,
  RoomEvent,
  VideoPresets
} from 'livekit-client';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
    faPlay, 
    faSignal, 
    faExclamationTriangle,
    faVolumeUp,
    faVolumeMute,
    faUsers,
    faCircle,
    faExpand,
    faCompress
} from '@fortawesome/free-solid-svg-icons';
import CommentsAndReactions from './CommentsAndReactions';
import ViewersList from './ViewersList';

const Viewer = () => {
  const [room, setRoom] = useState(null);
  const [error, setError] = useState(null);
  const [status, setStatus] = useState('Initializing...');
  const [hasUserInteracted, setHasUserInteracted] = useState(false);
  const [viewerCount, setViewerCount] = useState(0);
  const [username, setUsername] = useState('Viewer'); // For comments
  const [streamId, setStreamId] = useState(null);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const videoRef = useRef(null);
  const containerRef = useRef(null);
  const audioRefs = useRef({});
  const pendingAudioTracks = useRef([]);

  // Get or generate username on component mount
  useEffect(() => {
    const storedUsername = localStorage.getItem('stream_username');
    if (storedUsername) {
      setUsername(storedUsername);
    } else {
      const randomUsername = `Viewer${Math.floor(Math.random() * 10000)}`;
      setUsername(randomUsername);
      localStorage.setItem('stream_username', randomUsername);
    }
  }, []);

  // Handle fullscreen toggle
  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      containerRef.current.requestFullscreen();
      setIsFullscreen(true);
    } else {
      document.exitFullscreen();
      setIsFullscreen(false);
    }
  };

  // Handle user interaction to enable audio
  const handleUserInteraction = () => {
    if (!hasUserInteracted) {
      setHasUserInteracted(true);
      
      // Resume any audio context that might have been created
      if (window.audioContext) {
        window.audioContext.resume().catch(err => {
          console.error('Failed to resume AudioContext:', err);
        });
      }
      
      // Process any pending audio tracks
      pendingAudioTracks.current.forEach(({ track, participant }) => {
        attachAudioTrack(track, participant);
      });
      pendingAudioTracks.current = [];
    }
  };

  // Helper to attach audio tracks
  const attachAudioTrack = (track, participant) => {
    if (!track || track.kind !== 'audio') return;
    
    try {
      console.log('Attaching audio track:', track.sid, 'from participant:', participant.identity);
      
      // Create audio element for this track
      const audioEl = document.createElement('audio');
      audioEl.id = `audio-${participant.identity}-${track.sid}`;
      audioEl.autoplay = true;
      audioEl.playsInline = true; // Improves iOS compatibility
      audioEl.controls = false;
      audioEl.volume = 1.0; // Ensure full volume
      document.body.appendChild(audioEl);
      
      // Store reference to the element
      audioRefs.current[track.sid] = audioEl;
      
      // Attach the track
      track.attach(audioEl);
      
      // Force play the audio
      audioEl.play().catch(err => {
        console.warn('Audio play failed, retrying...', err);
        // Retry play after a short delay
        setTimeout(() => {
          audioEl.play().catch(e => console.error('Failed to play audio after retry:', e));
        }, 500);
      });
      
      console.log('Audio track attached successfully');
    } catch (err) {
      console.error('Failed to attach audio track:', err);
    }
  };

  // Add auto-retry for audio playback
  useEffect(() => {
    if (hasUserInteracted) {
      // Periodically check if any audio elements need to be restarted
      const checkAudioPlayback = () => {
        Object.values(audioRefs.current).forEach(audioEl => {
          if (audioEl && audioEl.paused) {
            console.log('Found paused audio, attempting to restart');
            audioEl.play().catch(err => {
              console.warn('Failed to restart audio playback:', err);
            });
          }
        });
      };
      
      const interval = setInterval(checkAudioPlayback, 2000);
      return () => clearInterval(interval);
    }
  }, [hasUserInteracted]);

  useEffect(() => {
    const initializeRoom = async () => {
      try {
        // Check if there's an active stream first
        const liveCheckResponse = await axios.get('/livestream/live');
        if (!liveCheckResponse.data || liveCheckResponse.data.length === 0) {
          setStatus('No livestream available right now');
          setError('No active livestream. Please check back later.');
          return;
        }

        // Get LiveKit URL
        const urlResponse = await axios.get('/livekit/url');
        const livekitUrl = urlResponse.data.url;

        // Get token (use a unique user id for the viewer, e.g., from auth or random)
        const viewerId = document.getElementById('viewer-id')?.value || `viewer_${Math.floor(Math.random() * 10000)}`;
        const tokenResponse = await axios.post('/livekit/token', {
          user_id: viewerId
        });
        const token = tokenResponse.data.token;

        // Create and connect to room
        const newRoom = new Room({
          adaptiveStream: true,
          dynacast: true,
          videoCaptureDefaults: {
            resolution: VideoPresets.h720.resolution,
          },
        });
        setRoom(newRoom);

        newRoom.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
          if (track.kind === 'video' && videoRef.current) {
            track.attach(videoRef.current);
            
            // Ensure video plays
            if (videoRef.current.paused) {
              videoRef.current.play().catch(err => {
                console.warn('Video play failed:', err);
              });
            }
          } else if (track.kind === 'audio') {
            if (hasUserInteracted) {
              // User has interacted, we can attach the audio track directly
              attachAudioTrack(track, participant);
            } else {
              // Store the track for later attachment
              pendingAudioTracks.current.push({ track, participant });
            }
          }
        });

        newRoom.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
          if (track.kind === 'audio') {
            // Clean up audio elements
            const audioEl = audioRefs.current[track.sid];
            if (audioEl) {
              track.detach(audioEl);
              audioEl.remove();
              delete audioRefs.current[track.sid];
            }
          }
        });

        newRoom.on(RoomEvent.ParticipantConnected, () => {
          setViewerCount(newRoom.participants.size);
        });

        newRoom.on(RoomEvent.ParticipantDisconnected, () => {
          setViewerCount(newRoom.participants.size);
        });

        newRoom.on(RoomEvent.Disconnected, async () => {
          setStatus('Stream has ended');
          setError('The livestream has ended. Thank you for watching!');
          
          // Track viewer left
          if (streamId && newRoom.localParticipant) {
            try {
              await axios.post('/livekit/viewer-left', {
                stream_id: streamId,
                participant_identity: newRoom.localParticipant.identity
              });
              console.log('Viewer left tracked');
            } catch (err) {
              console.error('Failed to track viewer leave:', err);
            }
          }
          
          // Clean up audio elements
          Object.values(audioRefs.current).forEach(el => el.remove());
          audioRefs.current = {};
        });

        newRoom.on(RoomEvent.Error, (error) => {
          setError(error.message);
          setStatus('Error: ' + error.message);
        });

        newRoom.on(RoomEvent.Connected, async () => {
          // Get stream ID from metadata or from room name
          let currentStreamId;
          try {
            // Try to get from metadata first
            const metadata = newRoom.metadata ? JSON.parse(newRoom.metadata) : {};
            if (metadata.streamId) {
              currentStreamId = metadata.streamId;
              setStreamId(metadata.streamId);
            } else {
              // Fallback to room name which often contains the stream ID
              currentStreamId = newRoom.name;
              setStreamId(newRoom.name);
            }
          } catch (e) {
            // If all else fails, use room name
            currentStreamId = newRoom.name;
            setStreamId(newRoom.name);
            console.error('Failed to parse room metadata:', e);
          }
          
          // Track viewer joined
          try {
            await axios.post('/livekit/viewer-joined', {
              stream_id: currentStreamId,
              participant_identity: newRoom.localParticipant.identity,
              viewer_name: username
            });
            console.log('Viewer joined tracked');
          } catch (err) {
            console.error('Failed to track viewer join:', err);
          }
        });

        await newRoom.connect(livekitUrl, token);
        setStatus('Connected to LiveKit');
        setViewerCount(newRoom.participants.size);

        // Subscribe to existing tracks
        newRoom.participants.forEach((participant) => {
          participant.tracks.forEach((publication) => {
            if (publication.isSubscribed) {
              const track = publication.track;
              if (track.kind === 'video' && videoRef.current) {
                track.attach(videoRef.current);
              } else if (track.kind === 'audio') {
                if (hasUserInteracted) {
                  attachAudioTrack(track, participant);
                } else {
                  pendingAudioTracks.current.push({ track, participant });
                }
              }
            }
          });
        });

      } catch (err) {
        setError(err.message);
        setStatus('Error: ' + err.message);
      }
    };

    initializeRoom();

    return () => {
      if (room) {
        room.disconnect();
      }
      
      // Clean up any audio elements
      Object.values(audioRefs.current).forEach(el => el.remove());
    };
  }, []);

  // Show error state if no stream or stream ended
  if (error) {
    return (
      <div className="viewer-container">
        <div className="no-stream-container">
          <div className="no-stream-icon">
            <FontAwesomeIcon icon={faExclamationTriangle} size="4x" />
          </div>
          <h2>{status}</h2>
          <p>{error}</p>
          <div className="button-group">
            <button 
              className="refresh-button"
              onClick={() => window.location.reload()}
            >
              <FontAwesomeIcon icon={faPlay} /> Check Again
            </button>
            <button 
              className="recordings-button"
              onClick={() => window.location.href = '/recordings'}
            >
              Watch Past Streams
            </button>
          </div>
        </div>
        
        <style>
          {`
            .viewer-container {
              display: flex;
              align-items: center;
              justify-content: center;
              min-height: 60vh;
              background: #1a1a1a;
              border-radius: 12px;
              padding: 2rem;
            }
            
            .no-stream-container {
              text-align: center;
              color: white;
              max-width: 500px;
            }
            
            .no-stream-icon {
              color: #f59e0b;
              margin-bottom: 1.5rem;
            }
            
            .no-stream-container h2 {
              font-size: 1.5rem;
              margin-bottom: 1rem;
              color: white;
            }
            
            .no-stream-container p {
              font-size: 1rem;
              color: #9ca3af;
              margin-bottom: 2rem;
            }
            
            .button-group {
              display: flex;
              gap: 1rem;
              justify-content: center;
              flex-wrap: wrap;
            }
            
            .refresh-button, .recordings-button {
              background: #3b82f6;
              color: white;
              border: none;
              padding: 0.75rem 1.5rem;
              border-radius: 8px;
              font-size: 1rem;
              cursor: pointer;
              display: inline-flex;
              align-items: center;
              gap: 0.5rem;
              transition: all 0.2s;
            }
            
            .refresh-button:hover, .recordings-button:hover {
              background: #2563eb;
              transform: translateY(-2px);
            }
            
            @media (max-width: 576px) {
              .button-group {
                flex-direction: column;
                width: 100%;
              }
              
              .refresh-button, .recordings-button {
                width: 100%;
              }
            }
          `}
        </style>
      </div>
    );
  }

  return (
    <div 
      ref={containerRef}
      className="viewer-container" 
      onClick={handleUserInteraction}
      onTouchStart={handleUserInteraction}
    >
      {!hasUserInteracted && (
        <div className="interaction-overlay">
          <button className="start-button" title="Enable Audio">
            <FontAwesomeIcon icon={faVolumeUp} />
          </button>
          <p>Click to Enable Audio</p>
        </div>
      )}
      
      <div className="video-container">
        <video 
          ref={videoRef} 
          autoPlay 
          playsInline 
        />
        
        <div className="video-overlay">
          <div className="stream-info">
            <div className="status-indicator">
              <FontAwesomeIcon icon={faCircle} className="live-indicator" />
              <span>LIVE</span>
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
        </div>
      </div>
      
      <div className="status">
        <div>
          <FontAwesomeIcon icon={faSignal} /> {status}
        </div>
      </div>
      
      {/* Comments and Reactions Component */}
      {room && <CommentsAndReactions room={room} username={username} streamId={streamId} />}
      
      <style>
        {`
          .viewer-container {
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #1a1a1a;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          }
          
          .interaction-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            z-index: 10;
            border-radius: 12px;
            backdrop-filter: blur(4px);
          }
          
          .start-button {
            width: 64px;
            height: 64px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
          }
          
          .start-button:hover {
            background: #45a049;
            transform: scale(1.05);
          }
          
          .video-container {
            width: 100%;
            max-width: 800px;
            aspect-ratio: 16/9;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
          }
          
          .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
          }

          .stream-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            pointer-events: auto;
          }

          .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-weight: 600;
          }

          .live-indicator {
            color: #ff4444;
            animation: pulse 2s infinite;
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
          
          .status {
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: #2a2a2a;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
          }
          
          .error {
            color: #f44336;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        `}
      </style>
    </div>
  );
};

export default Viewer;
