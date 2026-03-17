import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import ImageGalleryPage from "./components/pages/ImageGalleryPage.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: App,
    },
    {
        path: "/image-gallery",
        name: "image-gallery",
        component: ImageGalleryPage,
    },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return { top: 0 };
    },
});

