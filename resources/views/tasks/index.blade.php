<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            text-align: center;
        }

        .cover-container {
            max-width: 600px;
            margin: auto;
        }

        #taskContainer {
            display: none;
            margin-top: 20px;
        }

        .fade-out {
            transition: opacity 0.5s ease-out;
            opacity: 0;
        }
    </style>
</head>

<body class="text-center bg-secondary text-white">
    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column py-5 mt-5">
        <header>
            <h3>Task Manager</h3>
        </header>
        <main>
            <h1>Task Management</h1>
            <button class="btn btn-dark btn mt-3" id="showTaskTable">Enter Task</button>

            <div id="taskContainer">
                <div class="mb-3">
                    <button class="btn btn-success" id="addTaskBtn">Add New Task</button>
                    <button class="btn btn-info" id="viewAllTasksBtn">View All Tasks</button>
                </div>

                <table class="table table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="taskList"></tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal fade" id="addTaskModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="taskInput" class="form-control" placeholder="Enter task name">
                    <div class="text-danger mt-2" id="errorMsg" style="display: none;">Task already exists!</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveTask">Save Task</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let tasks = [];

            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show task table when button is clicked
            $("#showTaskTable").click(function() {
                $("#taskContainer").show();
                loadTasks();
            });

            // Function to load tasks
            function loadTasks() {
                $.get("/tasks", function(data) {
                    tasks = [];
                    $("#taskList").empty();

                    data.forEach(task => {
                        tasks.push(task.title.toLowerCase());
                        addTaskToTable(task);
                    });
                }).fail(function() {
                    alert("Failed to load tasks. Please try again.");
                });
            }

            // Open task modal
            $("#addTaskBtn").click(function() {
                $("#taskInput").val('');
                $("#errorMsg").hide();
                $("#addTaskModal").modal('show');
            });

            // Save task
            $("#saveTask").click(function() {
                let taskName = $("#taskInput").val().trim();

                if (taskName === "" || tasks.includes(taskName.toLowerCase())) {
                    $("#errorMsg").text("Task already exists or is empty!").show();
                    return;
                }

                $.post("/tasks", {
                        title: taskName
                    })
                    .done(function(response) {
                        tasks.push(taskName.toLowerCase());
                        addTaskToTable(response);
                        $("#addTaskModal").modal('hide');
                    })
                    .fail(function(xhr) {
                        let errorMessage = "An error occurred. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message.includes("Duplicate entry")) {
                            errorMessage = "Task already exists! Please enter a different task.";
                        }
                        $("#errorMsg").text(errorMessage).show();
                    });
            });

            // Mark task as complete
            $(document).on("change", ".markComplete", function() {
                let taskId = $(this).data("id");
                let row = $(this).closest("tr");

                $.post(`/tasks/${taskId}/complete`)
                    .done(function() {
                        row.addClass("fade-out");
                        setTimeout(() => row.remove(), 500);
                    })
                    .fail(function() {
                        alert("Failed to mark task as complete.");
                    });
            });

            // View all task
            $("#viewAllTasksBtn").click(function() {
                $.get("/tasks/all", function(data) {
                    $("#taskList").empty();
                    data.forEach(task => {
                        addTaskToTable(task, true);
                    });
                }).fail(function() {
                    alert("Failed to fetch tasks.");
                });
            });

            // Delete task
            $(document).on("click", ".deleteTask", function() {
                let taskId = $(this).data("id");
                let row = $(this).closest("tr");

                if (confirm("Are you sure you want to delete this task?")) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: "DELETE"
                    }).done(function() {
                        row.addClass("fade-out");
                        setTimeout(() => row.remove(), 500);
                    }).fail(function() {
                        alert("Failed to delete task.");
                    });
                }
            });

            // add task
            function addTaskToTable(task, includeCompleted = false) {
                let isCompleted = task.completed ? 'checked disabled' : '';
                let textClass = task.completed ? 'text-muted' : '';

                $("#taskList").append(`
                    <tr data-task-id="${task.id}" class="${textClass}">
                        <td>${task.title}</td>
                        <td><input type="checkbox" class="markComplete" data-id="${task.id}" ${isCompleted}></td>
                        <td><button class="btn btn-danger btn-sm deleteTask" data-id="${task.id}">&times;</button></td>
                    </tr>
                `);
            }
        });
    </script>
</body>

</html>