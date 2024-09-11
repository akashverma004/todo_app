<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="my-4">PHP - Simple To Do List App</h1>

        <!-- Input Task Form -->
        <form id="task-form">
            <div class="input-group mb-3">
                <input type="text" id="task-name" class="form-control" placeholder="Add Task">
                <button class="btn btn-primary" type="submit" id="add-task">Add Task</button>
            </div>
            <div class="text-danger" id="error-message" style="display:none"></div>
        </form>

        <!-- Tasks List -->
        <ul class="list-group" id="task-list">
            @foreach ($tasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center task-item"
                    data-id="{{ $task->id }}">
                    <span>{{ $task->name }}</span>
                    <div>
                        <input type="checkbox" class="mark-complete" data-id="{{ $task->id }}"
                            {{ $task->status == 'Done' ? 'checked' : '' }}>
                        <button class="btn btn-danger btn-sm delete-task" data-id="{{ $task->id }}">Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>

        <!-- Show All Tasks Button -->
        <button class="btn btn-info mt-4" id="show-all-tasks">Show All Tasks</button>
    </div>

    {{-- Ajax for getting data --}}
    <script>
        $(document).ready(function() {

            // Add task without page reload
            $('#task-form').on('submit', function(e) {
                e.preventDefault();
                const taskName = $('#task-name').val();

                $.ajax({
                    url: "{{ route('tasks.store') }}",
                    type: "POST",
                    data: {
                        name: taskName,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#task-list').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center task-item" data-id="${response.id}">
                        <span>${response.name}</span>
                        <div>
                            <input type="checkbox" class="mark-complete" data-id="${response.id}">
                            <button class="btn btn-danger btn-sm delete-task" data-id="${response.id}">Delete</button>
                        </div>
                    </li>
                `);
                        $('#task-name').val('');
                        $('#error-message').hide();
                    },
                    error: function(response) {
                        $('#error-message').text(response.responseJSON.errors.name[0]).show();
                    }
                });
            });

            // Mark task as complete
            $(document).on('change', '.mark-complete', function() {
                const taskId = $(this).data('id');
                const status = $(this).is(':checked') ? 'Done' : 'Pending';

                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PATCH',
                    data: {
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (status === 'Done') {
                            // If the task is marked as done, hide it from the list
                            $(`li[data-id='${taskId}']`).remove();
                        }
                        console.log('Task updated successfully');
                    },
                    error: function(response) {
                        console.error('Error updating task:', response);
                    }
                });
            });

            // Delete task with confirmation
            $(document).on('click', '.delete-task', function() {
                const taskId = $(this).data('id');
                if (confirm('Are you sure you want to delete this task?')) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $(`li[data-id='${taskId}']`).remove();
                        }
                    });
                }
            });

            // Show all tasks (both completed and pending)
            $('#show-all-tasks').on('click', function() {
                $.ajax({
                    url: "{{ route('tasks.show') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        $('#task-list').empty();
                        $.each(response.tasks, function(key, task) {
                            $('#task-list').append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center task-item" data-id="${task.id}">
                            <span>${task.name}</span>
                            <div>
                                <input type="checkbox" class="mark-complete" data-id="${task.id}" ${task.status == 'Done' ? 'checked' : ''}>
                                <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                            </div>
                        </li>
                    `);
                        });
                    },
                    error: function(response) {
                        console.log('Error occurred while fetching tasks');
                    }
                });
            });

        });
    </script>
</body>

</html>
