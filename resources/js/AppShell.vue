<template>
    <RouterView />
</template>

<script setup>
import { RouterView } from "vue-router";
import { onMounted } from "vue";

onMounted(() => {
    const initialOverlay = document.getElementById("initial-preload-overlay");
    if (initialOverlay) {
        initialOverlay.style.display = "none";
    }
});
</script>

