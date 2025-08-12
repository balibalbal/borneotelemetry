
<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ url('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ url('backend/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ url('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/swiper/swiper.js') }}"></script>
    
    <!-- Main JS -->
    <script src="{{ url('backend/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ url('backend/assets/js/dashboards-analytics.js') }}"></script> 

@stack('scripts')

<script>
    // script alert hapus bootstrap
    $(document).ready(function () {
        $('#confirmDeleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');
            $('#deleteForm').attr('action', url);
        });

        $('#confirmDeleteModal').on('hidden.bs.modal', function () {
            $('#deleteForm').attr('action', '');
        });
    });
</script>
