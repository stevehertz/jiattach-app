<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE -->
<script src="{{ asset('js/adminlte.js') }}"></script>

<!-- Optional Scripts -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

<!-- Student Portal Custom Scripts -->
<script>
    $(function () {
        'use strict';

        // Initialize sidebar search
        $('[data-widget="sidebar-search"]').on('click', function() {
            $(this).find('.form-control').focus();
        });

        // Auto-hide alerts after 5 seconds
        $('.alert:not(.alert-permanent)').delay(5000).fadeOut('slow');

        // Profile completeness animation
        const profileCompleteness = {{ Auth::user()->studentProfile->profile_completeness ?? 0 }};
        if (profileCompleteness < 100) {
            setTimeout(() => {
                $('.profile-complete-fill').css('width', profileCompleteness + '%');
            }, 500);
        }
    });
</script>
