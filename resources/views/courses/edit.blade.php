@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <a href="{{ route('courses.index') }}" class="text-info mb-3 d-block">&larr; Back To Courses</a>
        <h4 class="text-black mb-4">Edit Course</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('courses.update', $course->id) }}" enctype="multipart/form-data" id="course-form">
            @csrf
            @method('PUT')

            <div class="card dark-card mb-4" id="course-container">
                <div class="card-body">
                    <h5 class="text-black mb-3">Course Details</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-black">Course Title</label>
                            <input type="text" name="title" class="form-control dark-input @error('title') is-invalid @enderror"
                                   value="{{ old('title', $course->title) }}" required>
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
                            @if($course->feature_video)
                                <div class="mt-2">
                                    <video width="200" controls>
                                        <source src="{{ asset('storage/'.$course->feature_video) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_feature_video" id="remove_feature_video">
                                        <label class="form-check-label" for="remove_feature_video">
                                            Remove current video
                                        </label>
                                    </div>
                                </div>
                            @endif
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
                            @foreach(old('modules', $course->modules) as $moduleKey => $module)
                                <div class="card dark-card mb-4 module-item" id="module-{{ $moduleKey }}">
                                    <div class="card-body">
                                        <input type="hidden" name="modules[{{ $moduleKey }}][id]" value="{{ $module->id }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-black mb-0">Module {{ $moduleKey + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-module" @if($loop->first) style="display:none" @endif>
                                                Remove
                                            </button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-black">Module Title</label>
                                            <input type="text" name="modules[{{ $moduleKey }}][title]"
                                                   class="form-control dark-input @error('modules.'.$moduleKey.'.title') is-invalid @enderror"
                                                   value="{{ old('modules.'.$moduleKey.'.title', $module->title) }}" required>
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
                                            @php $contentIndex = 0; @endphp
                                            @foreach(old('modules.'.$moduleKey.'.contents', $module->contents) as $contentKey => $content)
                                                <div class="card content-card mb-3 content-item" id="content-{{ $moduleKey }}-{{ $contentKey }}">
                                                    <div class="card-body">
                                                        <input type="hidden" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][id]" value="{{ $content->id }}">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="text-info mb-0">Content {{ $contentKey + 1 }}</h6>
                                                            <button type="button" class="btn btn-sm btn-danger remove-content" @if($loop->first) style="display:none" @endif>
                                                                Remove
                                                            </button>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label text-black">Content Title</label>
                                                            <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][title]"
                                                                   class="form-control dark-input @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.title') is-invalid @enderror"
                                                                   value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.title', $content->title) }}" required>
                                                            @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video Source Type</label>
                                                                <select name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][source_type]"
                                                                        class="form-control dark-input @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type') is-invalid @enderror" required>
                                                                    <option value="">Select Source</option>
                                                                    <option value="YouTube" @if(old('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type', $content->video_source_type) == 'YouTube') selected @endif>YouTube</option>
                                                                    <option value="Vimeo" @if(old('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type', $content->video_source_type) == 'Vimeo') selected @endif>Vimeo</option>
                                                                </select>
                                                                @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.source_type')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video URL</label>
                                                                <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][video_url]"
                                                                       class="form-control dark-input @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_url') is-invalid @enderror"
                                                                       value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_url', $content->video_url) }}" required>
                                                                @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_url')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label text-black">Video Length (HH:MM:SS)</label>
                                                                <input type="text" name="modules[{{ $moduleKey }}][contents][{{ $contentKey }}][video_length]"
                                                                       class="form-control dark-input @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_length') is-invalid @enderror"
                                                                       value="{{ old('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_length', $content->video_length) }}" required>
                                                                @error('modules.'.$moduleKey.'.contents.'.$contentKey.'.video_length')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php $contentIndex++; @endphp
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
                <button type="submit" class="btn btn-success">Update Course</button>
                <a href="{{ route('courses.index') }}" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>

    <template id="module-template">
        <div class="card dark-card mb-4 module-item" id="module-{index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-black mb-0">Module {number}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-module">
                        Remove
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Module Title</label>
                    <input type="text" name="modules[{index}][title]" class="form-control dark-input" required>
                    <div class="invalid-feedback module-title-error" style="display:none"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-black mb-0">Module Contents</h6>
                    <button type="button" class="btn btn-info btn-sm add-content" data-module-index="{index}">
                        <i class="fa fa-plus"></i> Add Content
                    </button>
                </div>

                <div class="content-list" id="content-list-{index}">
                    <div class="card content-card mb-3 content-item" id="content-{index}-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-info mb-0">Content 1</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-content" style="display:none">
                                    Remove
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content Title</label>
                                <input type="text" name="modules[{index}][contents][0][title]" class="form-control dark-input" required>
                                <div class="invalid-feedback content-title-error" style="display:none"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video Source Type</label>
                                    <select name="modules[{index}][contents][0][source_type]" class="form-control dark-input" required>
                                        <option value="">Select Source</option>
                                        <option value="YouTube">YouTube</option>
                                        <option value="Vimeo">Vimeo</option>
                                    </select>
                                    <div class="invalid-feedback source-type-error" style="display:none"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video URL</label>
                                    <input type="text" name="modules[{index}][contents][0][video_url]" class="form-control dark-input" required>
                                    <div class="invalid-feedback video-url-error" style="display:none"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Video Length (HH:MM:SS)</label>
                                    <input type="text" name="modules[{index}][contents][0][video_length]" class="form-control dark-input" required>
                                    <div class="invalid-feedback video-length-error" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="content-template">
        <div class="card content-card mb-3 content-item" id="content-{moduleIndex}-{contentIndex}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-info mb-0">Content {number}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-content">
                        Remove
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content Title</label>
                    <input type="text" name="modules[{moduleIndex}][contents][{contentIndex}][title]" class="form-control dark-input" required>
                    <div class="invalid-feedback content-title-error" style="display:none"></div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video Source Type</label>
                        <select name="modules[{moduleIndex}][contents][{contentIndex}][source_type]" class="form-control dark-input" required>
                            <option value="">Select Source</option>
                            <option value="YouTube">YouTube</option>
                            <option value="Vimeo">Vimeo</option>
                        </select>
                        <div class="invalid-feedback source-type-error" style="display:none"></div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video URL</label>
                        <input type="text" name="modules[{moduleIndex}][contents][{contentIndex}][video_url]" class="form-control dark-input" required>
                        <div class="invalid-feedback video-url-error" style="display:none"></div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Video Length (HH:MM:SS)</label>
                        <input type="text" name="modules[{moduleIndex}][contents][{contentIndex}][video_length]" class="form-control dark-input" required>
                        <div class="invalid-feedback video-length-error" style="display:none"></div>
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

            @foreach($course->modules as $moduleKey => $module)
                contentIndexes[{{ $moduleKey }}] = {{ count($module->contents) }};
            @endforeach

            $('#add-module').click(function() {
                const template = $('#module-template').html();
                const html = template
                    .replace(/{index}/g, moduleIndex)
                    .replace(/{number}/g, moduleIndex + 1);

                $('#module-list').append(html);
                contentIndexes[moduleIndex] = 1;

                if ($('.module-item').length > 1) {
                    $('.remove-module').show();
                }

                moduleIndex++;
            });

            $(document).on('click', '.add-content', function() {
                const moduleIndex = $(this).data('module-index');
                const contentIndex = contentIndexes[moduleIndex] || 1;

                const template = $('#content-template').html();
                const html = template
                    .replace(/{moduleIndex}/g, moduleIndex)
                    .replace(/{contentIndex}/g, contentIndex)
                    .replace(/{number}/g, contentIndex + 1);

                $(`#content-list-${moduleIndex}`).append(html);
                contentIndexes[moduleIndex] = contentIndex + 1;

                if ($(`#content-list-${moduleIndex} .content-item`).length > 1) {
                    $(`#content-list-${moduleIndex} .remove-content`).show();
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

            @if($errors->any())
            @foreach($errors->getMessages() as $field => $messages)
            @if(str_contains($field, 'modules.'))
            @php
                $parts = explode('.', $field);
                $moduleIndex = $parts[1];
                $contentIndex = isset($parts[3]) ? $parts[3] : null;
                $fieldName = isset($parts[4]) ? $parts[4] : $parts[2];
            @endphp

            @if($fieldName == 'title' && is_null($contentIndex))
            $('.module-item').eq({{ $moduleIndex }}).find('.module-title-error')
                .text('{{ $messages[0] }}').show();
            $('.module-item').eq({{ $moduleIndex }}).find('[name="modules[{{ $moduleIndex }}][title]"]')
                .addClass('is-invalid');
            @elseif(!is_null($contentIndex))
            $('.content-item').eq({{ $contentIndex }}).find('.{{ $fieldName }}-error')
                .text('{{ $messages[0] }}').show();
            $('.content-item').eq({{ $contentIndex }}).find('[name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][{{ $fieldName }}]"]')
                .addClass('is-invalid');
            @endif
            @endif
            @endforeach
            @endif
        });
    </script>
@endsection
