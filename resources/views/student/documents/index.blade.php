<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">My Documents</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Documents</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach($documents as $doc)
                <div class="col-md-4">
                    <div class="card card-outline card-{{ $doc['color'] }} shadow-sm h-100">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">{{ $doc['type'] }}</h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas {{ $doc['icon'] }} fa-4x text-{{ $doc['color'] }} opacity-50"></i>
                            </div>
                            <h5 class="mb-2">{{ $doc['name'] }}</h5>
                            
                            @if($doc['url'])
                                <span class="badge badge-success mb-3">
                                    <i class="fas fa-check-circle mr-1"></i> Uploaded
                                </span>
                            @else
                                <span class="badge badge-secondary mb-3">
                                    <i class="fas fa-times-circle mr-1"></i> Not Uploaded
                                </span>
                            @endif

                            <div class="mt-3">
                                @if($doc['url'])
                                    <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-sm btn-outline-{{ $doc['color'] }} btn-block mb-2">
                                        <i class="fas fa-eye mr-1"></i> View Document
                                    </a>
                                    <!-- Optional: Add a download attribute or force download route if needed -->
                                    <a href="{{ $doc['url'] }}" download class="btn btn-sm btn-{{ $doc['color'] }} btn-block">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                @else
                                    <a href="{{ route('student.profile.edit') }}" class="btn btn-sm btn-default btn-block">
                                        <i class="fas fa-upload mr-1"></i> Upload Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle mr-1"></i> Note:</h5>
                        <p>
                            Ensure your School Attachment Letter is signed and stamped by your institution. 
                            Employers require this document to formalize your placement. 
                            All documents must be in <strong>PDF format</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>