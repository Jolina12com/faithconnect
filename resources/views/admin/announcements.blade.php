@extends('admin.dashboard')
@section('title', 'Church Announcements')
@section('content')

<style>
    .content-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 40px;
    }

    .table-section {
        flex: 2;
        min-width: 300px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .form-section {
        flex: 1;
        min-width: 300px;
        background-color: #fff;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .section-header {
        font-size: 22px;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .page-title {
        font-size: 28px;
        color: #333;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    .page-title i {
        margin-right: 10px;
        color: #007bff;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    input[type="text"],
    textarea {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: 0.3s ease;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
    }

    textarea {
        min-height: 120px;
        resize: vertical;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 5px;
    }

    .checkbox-container input {
        width: 18px;
        height: 18px;
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: #dc3545;
        color: #fff;
        padding: 8px 12px;
        font-size: 14px;
    }

    .btn-danger:hover {
        background: #bd2130;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
    }

    thead {
        background-color: #f8f9fa;
    }

    th, td {
        text-align: left;
        padding: 15px 16px;
        border-bottom: 1px solid #eee;
    }

    th {
        font-weight: 600;
        color: #555;
    }

    tr:hover {
        background-color: #f1f9ff;
        transition: background 0.3s;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pinned {
        background-color: #ffc107;
        color: #212529;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .truncate {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .content-wrapper {
            flex-direction: column;
        }

        .table-section,
        .form-section {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .page-title {
            font-size: 24px;
        }
    }
</style>

<div class="container fade-in">
    <h1 class="page-title">
        <i class="fas fa-bullhorn"></i> Church Announcements
    </h1>

    @if (session('success'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="content-wrapper">
        <!-- Left: Announcements Table -->
        <div class="table-section">
            <h2 class="section-header">Current Announcements</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr class="fade-in">
                            <td>{{ $announcement->title }}</td>
                            <td class="truncate">{{ $announcement->message }}</td>
                            <td>
                                @if($announcement->is_pinned)
                                <span class="status-badge status-pinned">
                                    <i class="fas fa-thumbtack"></i> Pinned
                                </span>
                                @endif
                            </td>
                            <td>{{ $announcement->published_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="far fa-comment-alt"></i>
                                    <p>No announcements yet. Create your first announcement!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Add Announcement Form -->
        <div class="form-section">
            <h2 class="section-header">Add New Announcement</h2>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="title">Announcement Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter announcement title" required>
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="message">Announcement Message</label>
                    <textarea id="message" name="message" placeholder="Write your announcement message..." required></textarea>
                    @error('message')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" id="is_pinned" name="is_pinned">
                    <label for="is_pinned">Pin this announcement to the top</label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Post Announcement
                </button>
            </form>
        </div>
    </div>
</div>

<div id="notification-toast" class="toast">
    <div id="toast-content"></div>
</div>

<!-- Scripts -->
<script>
    // Delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm("Are you sure you want to delete this announcement?")) {
                e.preventDefault();
            }
        });
    });

    // Show toast notification
    function showToast(message, duration = 3000) {
        const toast = document.getElementById('notification-toast');
        const toastContent = document.getElementById('toast-content');

        toastContent.textContent = message;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, duration);
    }

    // Pusher integration - KEPT EXACTLY AS ORIGINAL
    Pusher.logToConsole = true;

    const echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        forceTLS: true
    });

    echo.channel('announcements')
        .listen('AnnouncementPosted', (e) => {
            console.log('New Announcement:', e);

            // Example: Display announcement in an alert or toast
            alert(`📢 New Announcement: ${e.title}\n${e.message}`);
        });
</script>

@endsection
