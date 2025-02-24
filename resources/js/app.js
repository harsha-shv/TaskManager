import { createApp } from "vue/dist/vue.esm-bundler.js";
import TaskManager from "./components/TaskManager.vue";

const app = createApp({});
app.component("task-manager", TaskManager);
app.mount("#app");
