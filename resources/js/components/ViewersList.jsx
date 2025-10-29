import React, { useState, useEffect } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faUsers, faEye, faUserCircle } from '@fortawesome/free-solid-svg-icons';

const ViewersList = ({ room }) => {
    const [viewers, setViewers] = useState([]);
    const [totalCount, setTotalCount] = useState(0);
    const [isExpanded, setIsExpanded] = useState(false);

    useEffect(() => {
        if (!room) {
            console.log('No room available');
            return;
        }

        const updateViewers = () => {
            const participants = Array.from(room.participants.values());
            const viewerList = [];
            
            // Add remote participants
            participants.forEach(p => {
                const name = p.name || p.identity;
                viewerList.push({
                    identity: p.identity,
                    name: name,
                    isLocal: false
                });
            });
            
            // Add local participant (broadcaster or viewer)
            if (room.localParticipant) {
                const localName = room.localParticipant.name || room.localParticipant.identity;
                viewerList.push({
                    identity: room.localParticipant.identity,
                    name: localName,
                    isLocal: true
                });
            }
            
            console.log('👥 Viewers updated:', {
                remote: participants.length,
                total: viewerList.length,
                list: viewerList.map(v => v.name)
            });
            
            setViewers(viewerList);
            setTotalCount(viewerList.length);
        };

        // Initial update
        updateViewers();

        // Listen for participant changes
        room.on('participantConnected', (participant) => {
            console.log('✅ Participant joined:', participant.identity);
            updateViewers();
        });
        
        room.on('participantDisconnected', (participant) => {
            console.log('❌ Participant left:', participant.identity);
            updateViewers();
        });

        return () => {
            room.off('participantConnected', updateViewers);
            room.off('participantDisconnected', updateViewers);
        };
    }, [room]);

    const displayedViewers = isExpanded ? viewers : viewers.slice(0, 5);
    const remainingCount = viewers.length - displayedViewers.length;

    if (!room) {
        return null;
    }

    return (
        <div className="viewers-list-container">
            <div className="viewers-header" onClick={() => setIsExpanded(!isExpanded)}>
                <FontAwesomeIcon icon={faUsers} className="viewers-icon" />
                <span className="viewers-title">
                    {totalCount} watching
                </span>
            </div>
            
            {isExpanded && (
                <div className="viewers-dropdown">
                    <div className="viewers-dropdown-header">
                        <strong>Currently Watching</strong>
                    </div>
                    <div className="viewers-list">
                        {viewers.length > 0 ? (
                            <>
                                {displayedViewers.map((viewer) => (
                                    <div key={viewer.identity} className="viewer-item">
                                        <div className="viewer-avatar">
                                            <FontAwesomeIcon icon={faUserCircle} />
                                        </div>
                                        <span className="viewer-name">
                                            {viewer.name}
                                            {viewer.isLocal && <span className="you-badge"> (You)</span>}
                                        </span>
                                    </div>
                                ))}
                                {remainingCount > 0 && (
                                    <div className="viewer-item remaining">
                                        <span>+ {remainingCount} more</span>
                                    </div>
                                )}
                            </>
                        ) : (
                            <div className="no-viewers">No viewers yet</div>
                        )}
                    </div>
                </div>
            )}

            <style>{`
                .viewers-list-container {
                    position: relative;
                    display: inline-block;
                }

                .viewers-header {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    background: rgba(0, 0, 0, 0.6);
                    border-radius: 20px;
                    color: white;
                    cursor: pointer;
                    transition: all 0.2s;
                    user-select: none;
                }

                .viewers-header:hover {
                    background: rgba(0, 0, 0, 0.8);
                }

                .viewers-icon {
                    font-size: 1rem;
                }

                .viewers-title {
                    font-size: 0.9rem;
                    font-weight: 600;
                }

                .viewers-dropdown {
                    position: absolute;
                    top: calc(100% + 8px);
                    right: 0;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                    min-width: 280px;
                    max-width: 350px;
                    z-index: 1000;
                    animation: slideDown 0.2s ease;
                }
                
                .viewers-dropdown-header {
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid #e5e7eb;
                    background: #f9fafb;
                    border-radius: 12px 12px 0 0;
                    color: #374151;
                    font-size: 0.9rem;
                }

                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .viewers-list {
                    max-height: 300px;
                    overflow-y: auto;
                    padding: 0.5rem;
                }

                .viewer-item {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.75rem;
                    border-radius: 8px;
                    transition: background 0.2s;
                }

                .viewer-item:hover {
                    background: #f5f5f5;
                }

                .viewer-item.remaining {
                    justify-content: center;
                    color: #666;
                    font-size: 0.9rem;
                    font-weight: 500;
                }

                .viewer-avatar {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    flex-shrink: 0;
                }

                .viewer-name {
                    font-size: 0.9rem;
                    color: #333;
                    font-weight: 500;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    flex: 1;
                }
                
                .you-badge {
                    color: #3b82f6;
                    font-size: 0.8rem;
                    font-weight: 600;
                }

                .no-viewers {
                    text-align: center;
                    padding: 2rem 1rem;
                    color: #999;
                    font-size: 0.9rem;
                }

                .viewers-list::-webkit-scrollbar {
                    width: 6px;
                }

                .viewers-list::-webkit-scrollbar-track {
                    background: transparent;
                }

                .viewers-list::-webkit-scrollbar-thumb {
                    background: #ddd;
                    border-radius: 3px;
                }

                .viewers-list::-webkit-scrollbar-thumb:hover {
                    background: #bbb;
                }

                @media (max-width: 768px) {
                    .viewers-dropdown {
                        right: auto;
                        left: 0;
                        min-width: 200px;
                    }
                }
            `}</style>
        </div>
    );
};

export default ViewersList;
