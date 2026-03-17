import "./bootstrap";
import { createApp } from "vue";
import { router } from "./router";
import AppShell from "./AppShell.vue";

createApp(AppShell).use(router).mount("#app");