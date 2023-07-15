<template>
    <div class="w-25">
        <input v-model="this.title" type="text" class="form-control mb-3" placeholder="title">
        <div ref="dropzone" class="btn p-5 bg-dark text-center text-light">
            upload
        </div>
        <div class="mt-3">
            <vue-editor useCustomImageHandler @image-added="handleImageAdded" v-model="content" />
        </div>
        <div><input @click.prevent="this.store()" type="submit" class="btn btn-primary mt-3" value="Добавить"></div>
    </div>
    <h1>Посты</h1>
    <div class="w-25 mt-3" v-if="posts">
        <div v-for="post in posts">
            <router-link :to="{ name: 'show', params: { id: post.id } }">
                <h2>{{ post.title }}</h2>
            </router-link>
            <div v-for="image in post.images">
                <img class="w-50" :src="image.preview_url" alt="">
            </div>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone';
import { VueEditor } from "vue3-editor";
export default {
    components: { VueEditor },
    name: "Index",
    data() {
        return {
            dropzone: null,
            title: null,
            posts: null,
            content: null
        }
    },
    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: "url",
            autoProcessQueue: false,
            addRemoveLinks: true
        })
        this.getPosts()
    },
    methods: {
        store() {
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles()
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            });
            data.append('title', this.title)
            data.append('content', this.content)
            this.title = null
            this.content = null
            axios.post('/api/posts', data)
                .then(res => {
                    this.getPosts()
                })
        },
        getPosts() {
            axios.get('/api/posts/get')
                .then(res => {
                    console.log(res)
                    this.posts = res.data.data
                })
        },
        handleImageAdded(file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);

            axios({
                url: "/api/posts/images/",
                method: "POST",
                data: formData
            })
                .then(result => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
        }
    }
}
</script>
