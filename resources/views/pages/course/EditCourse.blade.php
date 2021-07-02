@extends('../Layout/Layout')

@section('title', 'edit course')

@section('body')
    <div class="container">
        <form class="form-group" action={{ route('update.course', ['course' => $course->id]) }} method="post">
            <fieldset class="form-control">
                @method('put')
                @csrf
                <legend>About Course</legend>
                <label for="title">title: </label>
                <input class="form-control" type="text" name="title" id="title" value="{{ $course->title }}"><br>
                @error('title')
                    <div class="error-box">
                        {{ $message }}
                    </div>
                @enderror
                <label for="description">description: </label>
                <input class="form-control" type="text" name="description" id="description"
                    value="{{ $course->description }}"><br>
                @error('description')
                    <div class="error-box">
                        {{ $message }}
                    </div>
                @enderror
                <label for="price">price</label>
                <input type="text" name="price" id="price">
                @error('price')
                    <div class="error-box">{{ $message }}</div>
                @enderror
                <input class="form-control" class="btn btn-primary" type="submit" value="save">
            </fieldset>
        </form>
        <div class="row g-0 align-items-center justify-content-between thumblin-box m-4">
            <div class="col col-md-4 col-sm-12 order-md-0">
                <img style="max-width: 100%" class="border border-2 border-gray"
                    src={{ $course->thumblin ? $course->thumblin->file_link : asset('default/Default.jpeg') }} alt="Course thumblin">
            </div>
            <div class="preview-image col-sm-12 col-md-4 order-md-2 ">
                <img class="border border-2 boder-gray" id="thumblin-preview" style="max-width: 100%"
                    src="{{ asset('default/Default.jpeg') }}">
            </div>

            <form class="form-group col-sm-4 col-md-2 order-md-1" action="{{ route('update.course.thumblin', ['course' => $course->id]) }}"
                method="post" enctype="multipart/form-data">
                @csrf
                @error('thumblin')
                    <div class="error-box">
                        {{$message}}
                    </div>
                @enderror
                <input class="one-click-upload" accept="image/*" type="file" name="thumblin" id="thumblin">
                <label style="max-width: 100%; min-width: 50%" class="btn btn-primary form-control" for="thumblin">Select Image</label><br>
                <input style="max-width: 100%; min-width: 50%" class="btn btn-success form-control" type="submit"
                    value="{{ $course->thumblin ? 'Change Thumblin' : 'Add Thumblin' }}">
            </form>

        </div>
        <div class="catagory-edit-box row">

            <div class="add-wrapper col col-6">
                <span><b>Add Catagory</b></span>
                <div id="add-catagory-box">
                    @foreach ($catagories as $item)
                        <div class="catagory-item-box">
                            <p><b>{{ $item->name }}</b></p>
                            <button catagory={{ $item->id }} class="catagory-item-add btn btn-success">Add</button><br>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="remover-wrapper col col-6">
                <span><b>remove Catagory</b></span>
                <div id="remove-catagory-box">
                    @foreach ($course->catagory as $item)
                        <div class="catagory-item-box">
                            <p><b>{{ $item->name }}</b></p>
                            <button catagory={{ $item->id }}
                                class="catagory-item-remove btn btn-danger">Remove</button><br>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <script>
        const course = @json($course);

        //initial selection
        const add_catagories = document.querySelectorAll('.catagory-item-add');
        const remove_catagories = document.querySelectorAll('.catagory-item-remove');
        const add_catagory_box = document.getElementById('add-catagory-box');
        const remove_catagory_box = document.getElementById('remove-catagory-box');

        //add eventlistener
        remove_catagories.forEach((node, index) => {
            node.addEventListener('click', (e) => remove_catagory(e), {
                once: true
            })
        })
        add_catagories.forEach((node, index) => {
            node.addEventListener('click', (e) => add_catagory(e), {

            })
        })

        const remove_catagory = async (e) => {
            const id = e.target.getAttribute('catagory')
            try {

                const res = await fetch(`/delete/course/${course.id}/catagory`, {
                    method: 'DELETE',
                    body: JSON.stringify({
                        catagory: id
                    }),
                    headers: {
                        'X-CSRF-TOKEN': window.csrf,
                        'content-type': 'application/json'
                    },
                })
                if (res.status == 200) {
                    e.target.parentNode.remove()
                }
            } catch (error) {
                console.log('error', error)
            }
        }
        const add_catagory = async (e) => {
            const id = e.target.getAttribute('catagory')
            //recheck
            let exists = false;
            document.querySelectorAll('.catagory-item-remove').forEach(element => {
                if (element.getAttribute('catagory') == id) {
                    exists = true;
                    return false;
                }
            })
            if (exists == true) {
                return false;
            }
            //add catagory
            try {

                const res = await fetch(`/update/course/${course.id}/catagory`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        catagory: id
                    }),
                    headers: {
                        'X-CSRF-TOKEN': window.csrf,
                        'content-type': 'application/json'
                    },
                })
                if (res.status == 200) {
                    const parent = e.target.parentNode.cloneNode(true)
                    const button = parent.childNodes[3]
                    button.innerText = 'Remove'
                    button.className = 'btn btn-danger catagory-item-remove'
                    button.addEventListener('click', (e) => remove_catagory(e), {
                        once: true
                    })
                    remove_catagory_box.appendChild(parent);
                }
            } catch (error) {
                console.log('error', error)
            }
        }
        window.onload = () => {
            const thumblin_input = document.getElementById('thumblin');
            const thumblin_preview = document.getElementById('thumblin-preview');
            thumblin_input.addEventListener('change', (e) => {
                const supported = ["image/jpeg", "image/jpg", "image/png"]
                const [file] = e.target.files
                if (!supported.includes(file.type)) {
                    e.target.value = ""
                    alert('file type must be jpeg, jpg or png')
                    return false;
                } else {
                    let image = URL.createObjectURL(file)
                    thumblin_preview.src = image
                    thumblin_preview.onload = () => URL.revokeObjectURL(image);
                }
            })
        }

    </script>
@endsection