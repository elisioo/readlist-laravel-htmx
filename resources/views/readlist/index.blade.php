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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center"><i
                class="fa-solid fa-book-open-reader" style="color: #FFD43B;"></i>
            {{ __(' My Reading List') }}
        </h2>
    </x-slot>

    <div class=" container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="mb-4 text-end">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fa fa-plus"></i> Add New Book
                    </button>
                </div>


                <div class="bg-white shadow-sm rounded-lg divide-y">
                    @if ($readlists->count())
                    @foreach ($readlists as $readlist)
                    @if ($readlist->user_id == Auth::id())
                    <div class="p-1 flex gap-3 w-full">
                        <i class="fa-solid fa-bookmark" style="color: #FFD43B; font-size:50px;"></i>
                        <div>
                            <span class="text-gray-800">{{ $readlist->user->name }}</span>
                            <small class="ml-2 text-sm text-gray-600">
                                {{ $readlist->created_at->format('j M Y, g:i a') }}
                            </small>
                            @unless ($readlist->created_at->eq($readlist->updated_at))
                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                            @endunless
                        </div>

                        <div class="ml-auto text-end">
                            <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#editBookModal-{{ $readlist->id }}">
                                        <i class="fa fa-pen px-3" style="color: #FFD43B;"></i>{{ __('Edit') }}
                                    </button>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('readlist.destroy', $readlist) }}"
                                        hx-post="{{ route('readlist.destroy', $readlist) }}" hx-target="body"
                                        hx-swap="outerHTML" hx-confirm="Are you sure you want to delete this book?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-trash px-3" style="color: red;"></i>{{ __('Remove') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="p-4 flex space-x-4 align-items-center">
                        <!-- Book Icon -->
                        <div class="flex-1">

                            <!-- Book Details -->
                            <h3 class="mt-2 text-lg fw-medium text-gray-900">Title: {{ $readlist->title }}</h3>
                            <p class="mt-1 text-gray-700">Description: {{ $readlist->description }}</p>
                            <p class="mt-1 text-muted">Author: <span class="fw-medium">{{ $readlist->author }}</span>
                            </p>
                            <p class="mt-1 text-muted">Status:
                                @php
                                $statusClass = match ($readlist->status) {
                                'To Read' => 'bg-primary text-white',
                                'Unread' => 'bg-secondary text-white',
                                'Ongoing' => 'bg-warning text-dark',
                                'Done' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                                };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $readlist->status }}</span>
                            </p>
                        </div>
                    </div>


                    <!-- Edit Modal -->
                    <div class="modal fade" id="editBookModal-{{ $readlist->id }}" tabindex="-1"
                        aria-labelledby="editBookModalLabel-{{ $readlist->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editBookModalLabel-{{ $readlist->id }}">Edit
                                        Book</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form hx-patch="{{ route('readlist.update', $readlist->id) }}" hx-target="body"
                                        hx-swap="outerHTML" hx-push-url="{{ route('readlist.index') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $readlist->id }}">
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control" name="title"
                                                value="{{ old('title', $readlist->title) }}" required maxlength="255">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" rows="3" name="description"
                                                required>{{ old('description', $readlist->description) }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Author</label>
                                            <input type="text" class="form-control" name="author"
                                                value="{{ old('author', $readlist->author) }}" required maxlength="100">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-control" name="status" required>
                                                <option value="To Read"
                                                    {{ $readlist->status == 'To Read' ? 'selected' : '' }}>To
                                                    Read
                                                </option>
                                                <option value="Unread"
                                                    {{ $readlist->status == 'Unread' ? 'selected' : '' }}>Unread
                                                </option>
                                                <option value="Ongoing"
                                                    {{ $readlist->status == 'Ongoing' ? 'selected' : '' }}>
                                                    Ongoing
                                                </option>
                                                <option value="Done"
                                                    {{ $readlist->status == 'Done' ? 'selected' : '' }}>
                                                    Done</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit"
                                                class="btn btn-success">{{ __('Save Changes') }}</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @else
                    <div class="p-6 text-center text-muted">
                        No books found.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form hx-post="{{ route('readlist.store') }}" hx-target="body" hx-swap="outerHTML">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Book Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Book Title" required
                                maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="description" placeholder="Description"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" class="form-control" name="author" placeholder="Author" required
                                maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
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

    <!-- Bootstrap and HTMX Scripts -->
    <script>
        document.addEventListener("htmx:afterSwap", function() {
            if (!document.body) {
                console.warn("document.body is null after HTMX swap");
                return;
            }

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