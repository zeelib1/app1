<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    posts: Array,
});
</script>

<template>
    <AppLayout title="Blog Posts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Blog Posts
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <Link
                        :href="route('blog-posts.create')"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Create New Post
                    </Link>

                    <div class="mt-6">
                        <div
                            v-for="post in posts"
                            :key="post.id"
                            class="mb-4 p-4 border rounded"
                        >
                            <h3 class="text-lg font-semibold">
                                {{ post.title }}
                            </h3>
                            <p class="text-gray-600">By {{ post.user.name }}</p>
                            <Link
                                :href="route('blog-posts.show', post.id)"
                                class="text-blue-500 hover:underline"
                            >
                                Read More
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
