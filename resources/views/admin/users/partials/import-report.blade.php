@if(session('import_report'))
    @php $report = session('import_report'); @endphp
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Import Summary</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $report['total'] }}</h3>
                            <p class="text-muted mb-0">Total Rows</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $report['imported'] }}</h3>
                            <p class="mb-0">Successfully Imported</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card {{ $report['skipped'] > 0 ? 'bg-warning' : 'bg-light' }}">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $report['skipped'] }}</h3>
                            <p class="mb-0">Skipped Rows</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($report['successful_imports']))
                <div class="mb-4">
                    <h5>Successfully Imported Users (Role: {{ $report['role'] }})</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['successful_imports'] as $user)
                                    <tr>
                                        <td>{{ $user['name'] }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="password" class="form-control" value="{{ $user['password'] }}" readonly>
                                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary copy-password" type="button" data-clipboard-text="{{ $user['password'] }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(session('skipped_rows'))
                <div>
                    <h5>Skipped Rows</h5>
                    <div class="alert alert-warning">
                        <ul class="mb-0">
                            @foreach(session('skipped_rows') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer text-muted">
            <small>Import completed at {{ now()->format('Y-m-d H:i:s') }}</small>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Copy password to clipboard
            document.querySelectorAll('.copy-password').forEach(button => {
                button.addEventListener('click', function() {
                    const password = this.getAttribute('data-clipboard-text');
                    navigator.clipboard.writeText(password).then(() => {
                        const originalIcon = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        
                        setTimeout(() => {
                            this.innerHTML = originalIcon;
                        }, 2000);
                    });
                });
            });
        });
    </script>
    @endpush
@endif
