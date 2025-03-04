<x-app-layout>
    @if (session('success'))
    <div class="alert alert-success" style="position: fixed; top: 10px; right: 20px; z-index: 1050; width: 300px;">
        {{ session('success') }}
        <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger" style="position: fixed; top: 60px; right: 20px; z-index: 1050; width: 300px;">
        {{ session('error') }}
        <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('📚 My Reading List') }}
        </h2>
    </x-slot>
    <div class="container mt-4">
        <!-- Add New Book Button -->
        <div class="mb-3 text-end">
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addBookModal">
                <i class="fa fa-plus"></i> Add New Book
            </button>
        </div>

        <!-- Books Table -->
        <div class="table-responsive">
            <table class="table shadow-md table-bordered table-hover">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>Title</th>
                        <th>Description</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($readlists->count())
                    @foreach ($readlists as $row)
                    @if ($row->user_id == Auth::id())
                    <tr class="text-center">
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->description }}</td>
                        <td>{{ $row->author }}</td>
                        <td>
                            @php
                            $badgeClass = match ($row->status) {
                            'To Read' => 'bg-primary',
                            'Unread' => 'bg-secondary',
                            'Ongoing' => 'bg-warning text-dark',
                            'Done' => 'bg-success',
                            default => 'bg-dark',
                            };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row->status }}</span>
                        </td>
                        <td style="width: 20px;">
                            <div class="btn-group" role="group">
                                <button class="shadow-none btn btn-sm" style="color:rgb(255, 153, 0);"
                                    data-bs-toggle="modal" data-bs-target="#editBookModal"
                                    href="route{'readlist.update',$row->id}">
                                    <i class=" fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <form hx-post="{{ route('readlist.destroy', $row->id) }}" hx-target="body"
                                    hx-swap="outerHTML" hx-confirm="Are you sure you want to delete this book?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">No books found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form hx-post="{{route('readlist.store')}}" hx-target="body" hx-swap="outerHTML">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Book Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Book Title" required
                                maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="description" placeholder="Description"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" name="author" placeholder="Author" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="To Read">To Read</option>
                                <option value="Unread">Unread</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Book</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" tabindex="-1" aria-labelledby="editBookModalLabel" id="editBookModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" hx-post="{{ route('readlist.update', $row->id) }}" hx-target="body"
                        hx-swap="outerHTML" hx-push-url="{{ route('readlist.index')}}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="editBookId" name="id">

                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title"
                                value="{{ old('title', $row->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" rows="3" id="editDescription" name="description"
                                required>{{ old('description', $row->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="editAuthor" name="author"
                                value="{{ old('author', $row->author) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-control" id="editStatus" name="status">
                                <option value="To Read">To Read</option>
                                <option value="Unread">Unread</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.body.addEventListener("htmx:afterSwap", function() {
            var modals = document.querySelectorAll(".modal");
            modals.forEach(function(modal) {
                new bootstrap.Modal(modal);
            });

            if (document.body.style.overflow === "hidden") {
                document.body.style.overflow = "";
            }
        });
    </script>
</x-app-layout>