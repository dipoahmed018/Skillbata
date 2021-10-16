@extends('../Layout/Layout')

@section('title', 'course')
@section('headers')
    <link rel="stylesheet" href={{ asset('css/course.css') }}>
@endsection

@section('tamplates')
    {{-- componetent data --}}

    <template id="review-template">
        <div class="review-content">
            <a class="owner-details" href="/user/template/profile">
                <div class="profile-image"><span></span></div>
                <span class="owner-name"></span>
            </a>
            <p class="content">content<p>
        </div>
        <div class="review-control">
            <span class="reply-creator-show" data-review-id="template" style="cursor: pointer">reply</span>
            <span>created at</span>
            <x-rating :rating="0"></x-rating>
        </div>
        {{-- add replies here form javascript --}}
        <div class="replies">
        </div>
    </template>

@endsection

@section('body')

    <div class="modal fade" id="tutorial-video" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">watch tutorial</h5>
                    <button id="close-modal" type="button" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row video-control-box">
                            <div class="col video-box col-12">
                                <video controls id="video-frame" width="100%"></video>
                            </div>
                            <div class="col control-box">
                                <button class="btn btn-primary"><i class="bi bi-arrow-left"></i></button>
                                <button class="btn btn-primary"><i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- course-introduction is for the title created at and other deitals --}}
    <div class="course-introduction">
        <div class="course-details">
            <h5>{{ $course->title }}</h5>
            <div class="details">
                <span>
                    Create By:
                    <a href="/user/{{ $course->ownerDetails->id }}/profile">
                        {{ $course->ownerDetails->name }}
                    </a>
                </span>
                <span>Create at: {{ $course->created_at->diffForHumans() }}</span>
                <x-rating :rating="$course->avg_rate"> </x-rating>
            </div>
            @can('update', $course)
                <div class="details-update tools">
                    <a class="tool" href="/update/course/{{ $course->id }}" data-toogle="tooltip"
                        title="Edit Course">
                        <i class="bi bi-pencil-square tool tool-icon"></i>
                    </a>
                    <div class="introduction-upload tool" data-toogle="tooltip" title="Update Introduction">
                        <input accept=".mp4" required class="add-introduction one-click-upload" type="file" name="introduction"
                            id="introduction-upload">
                        <label for="introduction-upload">
                            <i class="bi bi-file-earmark-play-fill tool-icon"></i>
                        </label>
                    </div>
                    <div class="thumbnail-upload tool" data-toogle="tooltip" title="Update Thumbnail">
                        <label for="introduction-upload">
                            <i class="bi bi-card-image tool-icon"></i>
                        </label>
                    </div>
                </div>
            @endcan
        </div>

        {{-- thumbnail is for the introduction video or course thumbnail --}}
        <div class="thumbnail-video">
            @if ($course->introduction)
                <video id="introduction-video" width="100%" src="{{ $course->introduction->file_link }}"
                    poster={{ $course->thumbnail ? $course->thumbnail->file_link : '' }} controls>
                </video>
            @elseif($course->thumbnail)
                <img src="{{ $course->thumbnail ? $course->thumbnail->file_link : '' }}" alt="thumbnail">
            @endif
        </div>
    </div>
    <div class="introduction row justify-content-center">
        <div class="introduction-video col col-12 col-md-6 ">
            @if ($course->introduction)
                <video id="introduction-video" width="100%" src="{{ $course->introduction }}"></video>
            @endif
        </div>
        <div class="d-block"></div>
        @can('update', $course)
            <div class="edit col col-2">
                <a class="btn btn-warning" href='/update/course/{{ $course->id }}'>Edit</a>
            </div>
        @endcan
        @can('delete', $course)
            <div class="edit col col-2">
                <form action="{{ route('delete.course', ['course' => $course->id]) }}" method="post">
                    @method('delete')
                    @csrf
                    <input class="btn btn-danger" type="submit" value="Delete">
                </form>
            </div>
        @endcan
        @can('purchase', $course)
            <div class="purchase col col-2">
                <a class="btn btn-success" href={{ route('purchase.product', ['product' => $course->id]) }}> purchase </a>
            </div>
        @endcan
        <div id="introduction-upload-box" class="col col-md-6">
            @can('update', $course)
                <label for="introduction"
                    class="add-button btn btn-primary mb-4">{{ $course->introduction ? 'Change Introduction' : 'Add Introduction' }}</label>
                <div id="introduction-error"></div>
            @endcan
        </div>
        <div id="introduction-progress-box" class="hide col col-md-5">
            <div class="progress">
                <div id="introduction-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <button id="introduction-up-cancel" class="btn btn-danger">cancel</button>
            <button class="pause" id="introduction-up-pause" class="btn btn-primary">pause</button>
        </div>
    </div>
    <div class="tutorial-upload row justify-content-center mb-2">

        @can('update', $course)
            <div id="tutorial-upload-box" class="upload-tutorial col col-md-2 mt-2">
                <input required accept=".mp4" type="file" name="tutorial" class="add-vide one-click-upload" id="tutorial">
                <label class="add-button btn btn-primary" for="tutorial">Add Tutorial</label>
                <div id="tutorial-error"></div>
            </div>
            <div id="tutorial-progress-box" class="hide col col-md-5">
                <div class="progress">
                    <div id="tutorial-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button id="tutorial-up-cancel" class="btn btn-danger">cancel</button>
                <button class="pause" id="tutorial-up-pause" class="btn btn-danger">pause</button>
            </div>
        @endcan
    </div>
    <div class="tutorial-videos container col-md-12">
        @if (count($course->tutorials) < 1)
            <h1> course does not have any tutorial</h1>
        @endif
        @foreach ($course->tutorials as $tutorial)
            <div draggable="true" class="tutorial-card row">
                <div class="details col-sm-10">
                    <h3 id="title">{{ $tutorial->title }}</h3>
                    <span>created_at</span>
                </div>

                <div class="edit col">
                    @can('delete', $course)
                        <form
                            action={{ route('delete.course.tutorial', ['course' => $course->id, 'tutorial' => $tutorial->id]) }}
                            method="post">
                            @method('delete')
                            @csrf
                            <input class="btn btn-danger" type="submit" value="Delete">
                        </form>
                    @endcan
                    @can('update', $course)
                        <a class="btn btn-warning" href="/course/{{ $course->id }}/tutorial/{{ $tutorial->id }}">Edit</a>
                    @endcan
                    @canany(['update', 'tutorial'], $course)
                        <div class="watch">
                            <button tutorial={{ $tutorial->id }} class="btn btn-primary watch-tutorial"
                                id='open-tutorial'>Watch</button>
                        </div>
                    @endcanany
                </div>
            </div>
        @endforeach
    </div>

    <div class="reviews-box">
        <h5>Reviews</h5>
        {{-- @can('review', $course)
            <form class="form-group row justify-content-center mb-3"
                action="{{ route('create.review', ['name' => $course->getTable(), 'id' => $course->id]) }}" method="post">
                @csrf
                <div class="col col-8">
                    <label class="form-label" for="content">review</label><br>
                    <input required class="form-control" type="text" name="content" id="review">
                    @error('content')
                        <div class="error-box">
                            {{ $message }}
                        </div>
                    @enderror
                    <label class="form-label" for="stars">stars</label><br>
                    <input required class="from-control" type="number" name="stars" id="stars" min="1" max="10">
                    @error('stars')
                        <div class="error-box">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="d-block mb-3"></div>
                <div class="col col-8">
                    <input class="form-control" class="btn btn-success" type="submit" value="review">
                </div>

            </form>
        @endcan --}}
        @foreach ($course->reviews as $item)
            <x-course.review :review-data="$item" :course="$course" :user="$user" class="review" />
        @endforeach
        <div class="links-wrapper">
            {{ $course->reviews->links() }}
        </div>
    </div>
    </div>
@endsection
@section('scripts')
    {{-- php data assign to javascript variable --}}
    <script>
        let csrf = document.head.querySelector("meta[name='_token']").content;
        let user = @json($user);
        let course = @json($course);
    </script>

    {{-- pagescripts --}}
    <script src={{ asset('js/course_show.js') }}></script>
    @stack('component-script')
@endsection
