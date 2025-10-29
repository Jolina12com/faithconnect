@if (session('success'))
<div class="flash-message success animate__animated animate__fadeInDown" id="success-alert">
    <div class="container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
                <div class="alert-message">
                    <strong>Success!</strong> {!! session('success') !!}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if (session('error'))
<div class="flash-message error animate__animated animate__fadeInDown">
    <div class="container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <i class="bi bi-x-circle-fill fs-3"></i>
                </div>
                <div class="alert-message">
                    <strong>Error!</strong> {!! session('error') !!}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if (session('warning'))
<div class="flash-message warning animate__animated animate__fadeInDown">
    <div class="container">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                </div>
                <div class="alert-message">
                    <strong>Warning!</strong> {!! session('warning') !!}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if (session('info'))
<div class="flash-message info animate__animated animate__fadeInDown">
    <div class="container">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <i class="bi bi-info-circle-fill fs-3"></i>
                </div>
                <div class="alert-message">
                    <strong>Info!</strong> {!! session('info') !!}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<style>
.flash-message {
    position: fixed;
    top: 20px;
    left: 0;
    right: 0;
    z-index: 9999;
}

.alert {
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    border: none;
}

.alert-success {
    background-color: #d4edda;
    border-left: 5px solid #28a745;
}

.alert-danger {
    background-color: #f8d7da;
    border-left: 5px solid #dc3545;
}

.alert-warning {
    background-color: #fff3cd;
    border-left: 5px solid #ffc107;
}

.alert-info {
    background-color: #d1ecf1;
    border-left: 5px solid #17a2b8;
}

.alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
}

.alert-success .alert-icon i {
    color: #28a745;
}

.alert-danger .alert-icon i {
    color: #dc3545;
}

.alert-warning .alert-icon i {
    color: #ffc107;
}

.alert-info .alert-icon i {
    color: #17a2b8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss success messages after 5 seconds
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            const alert = bootstrap.Alert.getOrCreateInstance(successAlert.querySelector('.alert'));
            alert.close();
        }, 5000);
    }
});
</script> 