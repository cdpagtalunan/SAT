<!-- jQuery -->
<script src="{{ asset('public/template/jquery/js/jquery.min.js') }}"></script>

<!-- Bootstrap 5 -->
{{-- <script src="{{ asset('public/template/bootstrap/js/bootstrap.min.js') }}"></script> --}}
<script src="{{ asset('public/template/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" ></script> --}}

<!-- AdminLTE -->
<script src="{{ asset('public/template/adminlte/js/adminlte.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('public/template/datatables/js/datatables.min.js') }}"></script>
{{-- <script src="{{ asset('/template/datatables/js/dataTables.bootstrap5.min.js') }}"></script> --}}


<!-- Select2 -->
<script src="{{ asset('public/template/select2/js/select2.min.js') }}"></script>

<!-- Toastr -->
<script src="{{ asset('public/template/toastr/js/toastr.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('public/template/sweetalert2/js/sweetalert2.min.js') }}"></script>

<!-- Moment -->
<script src="{{ asset('public/template/moment/moment.js') }}"></script>
<!-- Custom JS -->
<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "3000",
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "iconClass":  "toast-custom"
    };
</script>

<script src="@php echo asset("public/js/main/common.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/main/sat.js?".date("YmdHis")) @endphp"></script>

