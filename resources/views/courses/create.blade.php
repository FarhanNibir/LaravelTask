@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <a href="{{ route('courses.index') }}" class="text-info mb-3 d-block">&larr; Back To Course Page</a>
        <h4 class="text-black mb-4">Create a Course</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data" id="course-form">
            @csrf

            <div class="card dark-card mb-4" id="course-container">
                <div class="card-body">
                    <h5 class="text-black mb-3">Course Details</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-black">Course Title</label>
                            <input type="text" name="title" class="form-control dark-input @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-black">Feature Video</label>
                            <input type="file" name="feature_video" class="form-control dark-input @error('feature_video') is-invalid @enderror">
                            @error('feature_video')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-black mb-0">Course Modules</h5>
                            <button type="button" id="add-module" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Module
                            </button>
                        </div>

                        <div id="module-list">
                            @php $moduleIndex = 0; @endphp
                            @foreach(old('modules', [['title' => '', 'contents' => [['title' => '', 'source_type' => '', 'video_url' => '', 'video_length' => '']]]]) as $moduleKey => $module)
                                <div class="card dark-card mb-4 module-item" id="module-{{ $moduleKey }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-black mb-0">Module {{ $moduleKey + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-module" style="display:none">
                                                Remove
                                            </button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-black">Module Title</label>
                                            <input type="text" name="modules[{{ $moduleKey }}][title]"
                                                   class="form-control dark-input @error('modules.'.$moduleKey.'.title') is-invalid @enderror"
                                                   value="{{ old('modules.'.$moduleKey.'.title', $module['title']) }}" required>
                                            @error('modules.'.$moduleKey.'.title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-black mb-0">Module Contents</h6>
                                            <button type="button" class="btn btn-info btn-sm add-content" data-module-index="{{ $moduleKey }}">
                                                <i class="fa fa-plus"></i> Add Content
                                            </button>
                                        </div>

                                        <div class="content-list" id="content-list-{{ $moduleKey }}">
                                            @foreach(old('modules.'.$moduleKey.'.contents', $module['contents']) as $contentKey => $content)
                                                <div class="card content-card mb-3 content-item" id="content-{{ $moduleKey }}-{{ $contentKey }}">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="text-info mb-0">Content {{ $contentKey + 1 }}</h6>
                                                            <button type="button" class="btn btn-sm btn-danger remove-content" style="display:none">
                                                                Remove
                                                            </button>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-black">Content Title</label>
                                                            <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][title]"
                                                                   class="form-control dark-input" value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.title', $content['title']) }}" required>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video Source Type</label>
                                                                <select name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][source_type]"
                                                                        class="form-control dark-input" required>
                                                                    <option value="">Select Source</option>
                                                                    <option value="YouTube" @if(old('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type', $content['source_type']) == 'YouTube') selected @endif>YouTube</option>
                                                                    <option value="Vimeo" @if(old('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type', $content['source_type']) == 'Vimeo') selected @endif>Vimeo</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video URL</label>
                                                                <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][video_url]"
                                                                       class="form-control dark-input"
                                                                       value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_url', $content['video_url']) }}" required>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video Length (HH:MM:SS)</label>
                                                                <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][video_length]"
                                                                       class="form-control dark-input"
                                                                       value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_length', $content['video_length']) }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @php $moduleIndex++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">Save Course</button>
                <a href="{{ route('courses.index') }}" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>

    <template id="module-template">
        <div class="card dark-card mb-4 module-item" id="module-__INDEX__">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-black mb-0">Module __NUMBER__</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-module">
                        Remove
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Module Title</label>
                    <input type="text" name="modules[__INDEX__][title]" class="form-control dark-input" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-black mb-0">Module Contents</h6>
                    <button type="button" class="btn btn-info btn-sm add-content" data-module-index="__INDEX__">
                        <i class="fa fa-plus"></i> Add Content
                    </button>
                </div>

                <div class="content-list" id="content-list-__INDEX__">
                    <div class="card content-card mb-3 content-item" id="content-__INDEX__-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-info mb-0">Content 1</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-content" style="display:none">
                                    Remove
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content Title</label>
                                <input type="text" name="modules[__INDEX__][contents][0][title]" class="form-control dark-input" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video Source Type</label>
                                    <select name="modules[__INDEX__][contents][0][source_type]" class="form-control dark-input" required>
                                        <option value="">Select Source</option>
                                        <option value="YouTube">YouTube</option>
                                        <option value="Vimeo">Vimeo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video URL</label>
                                    <input type="text" name="modules[__INDEX__][contents][0][video_url]" class="form-control dark-input" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video Length (HH:MM:SS)</label>
                                    <input type="text" name="modules[__INDEX__][contents][0][video_length]" class="form-control dark-input" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="content-template">
        <div class="card content-card mb-3 content-item" id="content-__MODINDEX__-__CONTENTINDEX__">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-info mb-0">Content __NUMBER__</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-content">
                        Remove
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content Title</label>
                    <input type="text" name="modules[__MODINDEX__][contents][__CONTENTINDEX__][title]" class="form-control dark-input" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video Source Type</label>
                        <select name="modules[__MODINDEX__][contents][__CONTENTINDEX__][source_type]" class="form-control dark-input" required>
                            <option value="">Select Source</option>
                            <option value="YouTube">YouTube</option>
                            <option value="Vimeo">Vimeo</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video URL</label>
                        <input type="text" name="modules[__MODINDEX__][contents][__CONTENTINDEX__][video_url]" class="form-control dark-input" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video Length (HH:MM:SS)</label>
                        <input type="text" name="modules[__MODINDEX__][contents][__CONTENTINDEX__][video_length]" class="form-control dark-input" required>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let moduleIndex = {{ $moduleIndex }};
            const contentIndexes = {};

            @foreach(old('modules', []) as $moduleKey => $module)
                contentIndexes[{{ $moduleKey }}] = {{ count($module['contents'] ?? []) }};
            @endforeach

            $('#add-module').click(function() {
                let template = $('#module-template').html();
                template = template.replaceAll('__INDEX__', moduleIndex)
                    .replaceAll('__NUMBER__', moduleIndex + 1);

                $('#module-list').append(template);
                contentIndexes[moduleIndex] = 1;

                if ($('.module-item').length > 1) {
                    $('.remove-module').show();
                } else {
                    $('.remove-module').hide();
                }

                moduleIndex++;
            });

            $(document).on('click', '.add-content', function() {
                const parentModuleIndex = $(this).data('module-index');
                const contentIndex = contentIndexes[parentModuleIndex] || 1;

                let template = $('#content-template').html();
                template = template.replaceAll('__MODINDEX__', parentModuleIndex)
                    .replaceAll('__CONTENTINDEX__', contentIndex)
                    .replaceAll('__NUMBER__', contentIndex + 1);

                $(`#content-list-${parentModuleIndex}`).append(template);
                contentIndexes[parentModuleIndex] = contentIndex + 1;

                if ($(`#content-list-${parentModuleIndex} .content-item`).length > 1) {
                    $(`#content-list-${parentModuleIndex} .remove-content`).show();
                }
            });

            $(document).on('click', '.remove-module', function() {
                if ($('.module-item').length > 1) {
                    $(this).closest('.module-item').remove();
                    if ($('.module-item').length === 1) {
                        $('.remove-module').hide();
                    }
                }
            });

            $(document).on('click', '.remove-content', function() {
                const contentList = $(this).closest('.content-list');

                if (contentList.find('.content-item').length > 1) {
                    $(this).closest('.content-item').remove();
                    if (contentList.find('.content-item').length === 1) {
                        contentList.find('.remove-content').hide();
                    }
                }
            });
        });
    </script>
@endsection
