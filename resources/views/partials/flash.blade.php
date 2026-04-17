@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-circle-check mr-1"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-triangle-exclamation mr-1"></i>Periksa kembali input Anda.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Hidden logout form (triggered by sidebar Keluar link) --}}
@auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var link = document.getElementById('logout-link');
                if (link) {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.getElementById('logout-form').submit();
                    });
                }
            });
        </script>
    @endpush
@endauth
