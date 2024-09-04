<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To Do List App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h4>PHP - Simple To Do List App</h4>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" id="task-input" class="form-control" placeholder="Enter task">
                <div class="input-group-append">
                    <button id="add-task-btn" class="btn btn-primary">Add Task</button>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="task-list">
                @foreach($tasks as $task)
                    <tr data-id="{{ $task->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $task->name }}</td>
                        <td>
                            @if($task->is_completed)
                                <span class="badge badge-success">Done</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-success mark-complete">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger delete-task-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script>
    $(document).ready(function() {
        $('#add-task-btn').click(function() {
            var taskName = $('#task-input').val();
            
            if (taskName.trim() === '') {
                alert('Task cannot be empty');
                return;
            }

            $.ajax({
                url: '/tasks',
                method: 'POST',
                data: {
                    name: taskName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        $('#task-list').append(`
                            <tr data-id="${response.task.id}">
                                <td>${response.task.id}</td>
                                <td>${response.task.name}</td>
                                <td>
                                    <span class="badge badge-warning">Pending</span>
                                </td>
                                <td>
                                    <button class="btn btn-success mark-complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger delete-task-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        $('#task-input').val('');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(response) {
                var errors = response.responseJSON.errors;
                if(errors.name) {
                    alert(errors.name[0]);  // Display the first validation error for the name field
                }
            }
            });
        });

        $(document).on('click', '.mark-complete', function() {
            var taskId = $(this).closest('tr').data('id');
            var isCompleted = !$(this).closest('tr').find('.badge').hasClass('badge-success');
            
            $.ajax({
                url: `/tasks/${taskId}/complete`,
                method: 'POST',
                data: {
                    is_completed: isCompleted,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(isCompleted) {
                        $(this).closest('tr').find('.badge').removeClass('badge-warning').addClass('badge-success').text('Done');
                    } else {
                        $(this).closest('tr').find('.badge').removeClass('badge-success').addClass('badge-warning').text('Pending');
                    }
                }.bind(this)
            });
        });

        $(document).on('click', '.delete-task-btn', function() {
            if (confirm('Are you sure to delete this task?')) {
                var taskId = $(this).closest('tr').data('id');
                
                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            $(this).closest('tr').remove();
                        }
                    }.bind(this)
                });
            }
        });
    });
</script>

</body>
</html>
