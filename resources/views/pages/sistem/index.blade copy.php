@extends('layouts.admin')
@section('title', 'Dashboard Mtrack')
@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <form id="sshForm">
            <div class="card-header py-3 d-flex justify-content-center">
                <h4>Check Running Sistem on Server</h4>
            </div>
            <div class="card-body">
                
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>     
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Check Processes</button>
                    <button type="button" id="runPhpFile" class="btn btn-secondary">Run comserver</button>
                </div>
            </div>
        </form>    
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div id="output" class="mt-3">
                <pre></pre>
            </div>
            <div id="error" class="mt-3 text-danger">
                <pre></pre>
            </div>
        </div>
    </div>
</div>

<div id="spinner-overlay" class="d-none position-fixed w-100 h-100 top-0 left-0 bg-dark bg-opacity-50 justify-content-center align-items-center" style="z-index: 1000;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Menyertakan token CSRF dalam setiap permintaan AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#sshForm').on('submit', function(e) {
            e.preventDefault();

            // Tampilkan spinner
            $('#spinner-overlay').removeClass('d-none').addClass('d-flex');

            $.ajax({
                url: '/check-php-processes',
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    // Sembunyikan spinner
                    $('#spinner-overlay').removeClass('d-flex').addClass('d-none');

                    $('#output pre').text(data.output.join("\n"));
                    $('#error pre').text('');
                },
                error: function(xhr) {
                    // Sembunyikan spinner
                    $('#spinner-overlay').removeClass('d-flex').addClass('d-none');

                    $('#output pre').text('');
                    $('#error pre').text(xhr.responseJSON.error);
                }
            });
        });

        $('#runPhpFile').on('click', function() {
            $('.spinner-overlay').css('display', 'flex');
            $.ajax({
                url: '/run-remote-php',
                method: 'POST',
                data: $('#sshForm').serialize(),
                success: function(data) {
                    $('.spinner-overlay').hide();
                    $('#output pre').text(data.output);
                    $('#error pre').text('');
                },
                error: function(xhr) {
                    $('.spinner-overlay').hide();
                    $('#output pre').text('');
                    $('#error pre').text(xhr.responseJSON.error);
                }
            });
        });
    });
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
