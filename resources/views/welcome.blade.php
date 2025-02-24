<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
</head>
<body>

    <h1>Task Manager</h1>

    <label for="projectDropdown">Select Project:</label>
    <select id="projectDropdown"></select>

    <input type="text" id="taskInput" placeholder="Enter task name">
    <button id="addTaskBtn">Add</button>

    <h3>Tasks:</h3>
    <ul id="taskList"></ul>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadProjects();

            document.getElementById('projectDropdown').addEventListener('change', function () {
                loadTasks(this.value);
            });

            document.getElementById('addTaskBtn').addEventListener('click', function () {
                let taskName = document.getElementById('taskInput').value;
                let projectId = document.getElementById('projectDropdown').value;

                if (!taskName || !projectId) {
                    alert("Please enter a task name and select a project!");
                    return;
                }

                fetch('http://127.0.0.1:8000/api/tasks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: taskName, priority: 'medium', project_id: projectId })
                })
                .then(response => response.json())
                .then(() => {
                    alert("Task added successfully!");
                    loadTasks(projectId);
                })
                .catch(error => console.error("Error:", error));
            });

            function loadProjects() {
                fetch('http://127.0.0.1:8000/api/projects')
                    .then(response => response.json())
                    .then(projects => {
                        let projectDropdown = document.getElementById('projectDropdown');
                        projectDropdown.innerHTML = '<option value="">Select a project</option>';

                        projects.forEach(project => {
                            let option = document.createElement('option');
                            option.value = project.id;
                            option.textContent = project.name;
                            projectDropdown.appendChild(option);
                        });
                    });
            }

            function loadTasks(projectId) {
                fetch(`http://127.0.0.1:8000/api/projects/${projectId}/tasks`)
                    .then(response => response.json())
                    .then(tasks => {
                        let taskList = document.getElementById('taskList');
                        taskList.innerHTML = '';

                        tasks.forEach(task => {
                            let li = document.createElement('li');
                            li.dataset.id = task.id;

                            let input = document.createElement('input');
                            input.type = "text";
                            input.value = task.name;
                            input.onchange = function () {
                                editTask(task.id, input.value, task.priority, projectId);
                            };

                            let deleteBtn = document.createElement('button');
                            deleteBtn.textContent = "❌";
                            deleteBtn.onclick = function () {
                                deleteTask(task.id, projectId);
                            };

                            li.appendChild(input);
                            li.appendChild(deleteBtn);
                            taskList.appendChild(li);
                        });
                    });
            }

            function editTask(taskId, newName, priority, projectId) {
                fetch(`http://127.0.0.1:8000/api/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: newName, priority: priority })
                })
                .then(response => response.json())
                .then(() => {
                    alert("Task updated!");
                    loadTasks(projectId);
                })
                .catch(error => console.error("Error updating task:", error));
            }

            function deleteTask(taskId, projectId) {
                fetch(`http://127.0.0.1:8000/api/tasks/${taskId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(() => {
                    alert("Task deleted successfully!");
                    loadTasks(projectId);
                })
                .catch(error => console.error("Error deleting task:", error));
            }

            new Sortable(document.getElementById('taskList'), {
                animation: 150,
                onEnd: function (evt) {
                    let tasks = [];
                    document.getElementById('taskList').querySelectorAll('li').forEach((li, index) => {
                        tasks.push({ id: li.dataset.id, priority: index + 1 });
                    });

                    fetch('http://127.0.0.1:8000/api/tasks/reorder', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ tasks: tasks })
                    })
                    .then(() => alert("Tasks reordered!"))
                    .catch(error => console.error("Error reordering tasks:", error));
                }
            });
        });
    </script>

</body>
</html>
