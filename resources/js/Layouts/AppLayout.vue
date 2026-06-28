<!-- resources/js/layouts/AppLayout.vue -->
<template>
    <div class="min-h-screen bg-gray-100">
        <AppHeader />
        <div class="flex">
            <AppSidebar />
            <div class="flex-1 p-6">
            <h2>TTTTTTTTTTTTT</h2>
                <router-view />
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/Common/AppHeader.vue';
import AppSidebar from '../components/Common/AppSidebar.vue';

export default {
    components: {
        AppHeader,
        AppSidebar
    }
};
</script>