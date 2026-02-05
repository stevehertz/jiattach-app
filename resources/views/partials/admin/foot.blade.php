 <!-- jQuery -->
 <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
 <!-- Bootstrap -->
 <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
 <!-- SweetAlert2 -->
 <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
 <!-- Toastr -->
 <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
 <!-- AdminLTE -->
 <script src="{{ asset('js/adminlte.js') }}"></script>
 <!-- ChartJS -->
 <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

 @livewireScripts

 <script>
    document.addEventListener('livewire:initialized', () => {
        // Apply phone mask
        $('#phone, #company_phone').mask('0000 000 000');

        // Real-time validation
        Livewire.on('validation-errors', (errors) => {
            Object.keys(errors).forEach(field => {
                const input = $(`[wire\\:model="${field}"]`);
                input.addClass('is-invalid');
                input.next('.invalid-feedback').remove();
                input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
            });
        });

        // Clear errors on input
        $('input, select, textarea').on('input change', function() {
            const field = $(this).attr('wire:model');
            if (field) {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
    });
</script>

 <!-- OPTIONAL SCRIPTS -->

 {{-- <!-- AdminLTE for demo purposes -->
 <script src="{{ asset('js/demo.js') }}"></script>
 <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
 <script src="{{ asset('js/pages/dashboard3.js') }}"></script> --}}
