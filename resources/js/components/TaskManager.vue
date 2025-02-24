<template>
    <div>
        <h2>Task Manager</h2>

        <!-- Project Selection -->
        <select v-model="selectedProject">
            <option v-for="project in projects" :key="project.id" :value="project.id">
                {{ project.name }}
            </option>
        </select>

        <!-- Task List -->
        <ul>
            <li v-for="task in tasks" :key="task.id">
                {{ task.name }}
                <button @click="deleteTask(task.id)">X</button>
            </li>
        </ul>

        <!-- Add Task -->
        <input v-model="newTask" placeholder="Add Task">
        <button @click="addTask">Add</button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            projects: [],
            tasks: [],
            newTask: '',
            selectedProject: null
        };
    },
    mounted() {
        axios.get('/api/projects').then(res => {
            this.projects = res.data;
        });
    },
    watch: {
        selectedProject(newProject) {
            if (newProject) {
                axios.get('/api/tasks', { params: { project_id: newProject } })
                    .then(res => this.tasks = res.data)
                    .catch(error => console.error("Error fetching tasks:", error));
            }
        }
    },
    methods: {
        addTask() {
            if (!this.selectedProject) {
                alert("Select a project first!");
                return;
            }
            axios.post('/api/tasks', {
                name: this.newTask,
                project_id: this.selectedProject
            }).then(() => {
                this.newTask = '';
                this.loadTasks();
            });
        },
        deleteTask(id) {
            axios.delete(`/api/tasks/${id}`).then(() => this.loadTasks());
        },
        loadTasks() {
            axios.get('/api/tasks', { params: { project_id: this.selectedProject } })
                .then(res => this.tasks = res.data);
        }
    }
};
</script>
