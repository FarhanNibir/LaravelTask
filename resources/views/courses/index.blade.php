@extends('layouts.app')

@section('title', 'Courses List')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-black mb-0">Courses List</h4>
            <a href="{{ route('courses.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Create New Course
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table id="courses-table" class="table table-hover table-striped table-bordered align-middle" style="width:100%">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Feature Video</th>
                        <th>Modules</th>
                        <th>Contents</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td class="fw-bold">{{ $course->title }}</td>
                            <td class="text-center">
                                @if($course->feature_video)
                                    <video width="120" height="70" style="border-radius: 5px;" muted loop>
                                        <source src="{{ asset('storage/'.$course->feature_video) }}" type="video/mp4">
                                    </video>
                                @else
                                    <span class="badge bg-secondary">No Video</span>
                                @endif
                            </td>
                            <td>
                                @if($course->modules->isEmpty())
                                    <span class="text-muted">No modules</span>
                                @else
                                    <ul class="list-unstyled mb-0">
                                        @foreach($course->modules as $module)
                                            <li>📦 {{ $module->title }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                @php $contentsCount = $course->modules->flatMap->contents->count(); @endphp
                                @if($contentsCount === 0)
                                    <span class="text-muted">No contents</span>
                                @else
                                    <button class="btn btn-sm btn-outline-info toggle-contents" type="button">
                                        View Contents ({{ $contentsCount }})
                                    </button>
                                    <div class="content-list mt-2 d-none">
                                        <ul class="list-unstyled small">
                                            @foreach($course->modules as $module)
                                                @foreach($module->contents as $content)
                                                    <li class="mb-2">
                                                        <strong>{{ $content->title }}</strong><br>
                                                        <span class="text-muted">
                                                        {{ $content->video_source_type }} • {{ $content->video_length }}
                                                    </span>
                                                    </li>
                                                @endforeach
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-primary me-1">
                                    Edit
                                </a>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this course?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        video { max-height: 70px; }
        .toggle-contents { white-space: nowrap; }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#courses-table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']]
            });

            // Toggle course contents
            $(document).on('click', '.toggle-contents', function() {
                $(this).next('.content-list').toggleClass('d-none');
                $(this).text(function(i, text) {
                    return text.includes('View') ? text.replace('View', 'Hide') : text.replace('Hide', 'View');
                });
            });
        });
    </script>
@endsection
