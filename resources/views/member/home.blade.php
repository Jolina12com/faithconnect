@extends('member.dashboard_member');

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white">
            <h4 class="text-center py-3">User Dashboard</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-white" href="">Home</a></li>
                <li class="nav-item">
                    <a href="notifications" class="nav-link">
                        <i class="fa fa-bell"></i>
                        Notifications
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="badge bg-danger ms-1" id="notif-count">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                </li>
                <script>
                    function updateNotificationCount() {
                        fetch("{{ route('notifications.count') }}")
                            .then(response => response.json())
                            .then(data => {
                                let notifBadge = document.getElementById('notif-count');
                                if (data.count > 0) {
                                    notifBadge.innerText = data.count;
                                    notifBadge.style.display = 'inline';
                                } else {
                                    notifBadge.style.display = 'none';
                                }
                            });
                    }

                    // Run every 5 seconds
                    setInterval(updateNotificationCount, 5000);
                </script>


                <li class="nav-item"><a class="nav-link text-white" href="">Profile</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    {{ __('Dashboard') }}
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h5>Welcome, {{ auth()->user()->first_name }}!</h5>


                </div>
            </div>
        </main>
    </div>
</div>
@endsection