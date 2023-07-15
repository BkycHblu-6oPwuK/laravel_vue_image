<template>
    <div v-if="post">
        <div>
            <h1 class="text-center">{{ this.post.title }}</h1>
            <router-link :to="{ name: 'edit', params: { id: post.id } }">Изменить</router-link>
        </div>
        <div v-for="image in this.post.images">
            <img :src="image.url" alt="">
        </div>
        <div class="w-25 mt-3 ql-editor" v-html="this.post.content"></div>
    </div>
</template>

<script>
export default {
    name: "Show",
    data() {
        return {
            post: null,
        };
    },
    mounted() {
        this.getPost();
    },
    methods: {
        getPost() {
            const id = this.$route.params.id;
            axios.get(`/api/posts/get/${id}`)
                .then(res => {
                this.post = res.data.data;
            });
        }
    },
}
</script>
