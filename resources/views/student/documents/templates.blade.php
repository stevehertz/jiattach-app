<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">CV Templates</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('student.documents.index') }}">Documents</a></li>
                        <li class="breadcrumb-item active">Templates</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-12">
                    <div class="callout callout-info shadow-sm">
                        <h5><i class="fas fa-lightbulb text-warning mr-1"></i> Pro Tip:</h5>
                        <p class="mb-0">
                            These templates are designed to pass Applicant Tracking Systems (ATS). 
                            Download the Word (.docx) version, fill in your details, save as <strong>PDF</strong>, 
                            and upload it to your profile for the best results.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach($templates as $template)
                    <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                        <div class="card card-outline card-{{ $template['color'] }} shadow-sm w-100">
                            <!-- Preview Image Section -->
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: #f4f6f9;">
                                <img src="{{ $template['preview_image'] }}" 
                                     alt="{{ $template['name'] }}" 
                                     class="card-img-top" 
                                     style="object-fit: cover; height: 100%; width: 100%; opacity: 0.9;">
                                
                                <div class="position-absolute" style="top: 10px; right: 10px;">
                                    <span class="badge badge-{{ $template['color'] }}">{{ $template['category'] }}</span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="font-weight-bold mb-2">
                                    <i class="fas {{ $template['icon'] }} text-{{ $template['color'] }} mr-1"></i> 
                                    {{ $template['name'] }}
                                </h5>
                                <p class="text-muted small flex-grow-1">
                                    {{ $template['description'] }}
                                </p>
                                
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <!-- Preview Button triggers Modal -->
                                            <button type="button" 
                                                    class="btn btn-default btn-block btn-sm"
                                                    data-toggle="modal" 
                                                    data-target="#previewModal{{ $loop->index }}">
                                                <i class="fas fa-eye mr-1"></i> Preview
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('student.cv.templates.download', $template['id']) }}" 
                                               class="btn btn-{{ $template['color'] }} btn-block btn-sm">
                                                <i class="fas fa-download mr-1"></i> Word
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for this template -->
                    <div class="modal fade" id="previewModal{{ $loop->index }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-weight-bold">{{ $template['name'] }} - Preview</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body bg-light text-center p-0">
                                    <img src="{{ $template['preview_image'] }}" class="img-fluid shadow-sm m-3" style="max-height: 70vh;">
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <a href="{{ route('student.cv.templates.download', $template['id']) }}" class="btn btn-{{ $template['color'] }}">
                                        <i class="fas fa-download mr-1"></i> Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.student>