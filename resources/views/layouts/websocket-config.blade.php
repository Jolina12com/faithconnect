{{-- Optimized Notification System --}}
<script>
@auth
window.authId = {{ auth()->id() }};
@endauth
</script>

{{-- Load optimized notifications (single script) --}}
<script src="{{ asset('js/optimized-notifications.js') }}"></script>