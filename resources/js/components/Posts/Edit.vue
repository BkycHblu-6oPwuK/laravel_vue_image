<template>
    <div>
        <div class="w-25">
            <input v-model="this.title" type="text" class="form-control mb-3" placeholder="title">
            <div ref="dropzone" class="btn mt-3 p-5 bg-dark text-center text-light">
                upload
            </div>
            <div class="mt-3">
                <vue-editor useCustomImageHandler @image-removed="handleImageRemoved" @image-added="handleImageAdded" v-model="content" />
            </div>
            <div><input @click.prevent="update" type="submit" class="btn btn-primary mt-3" value="Изменить"></div>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone';
import { VueEditor } from "vue3-editor";
export default {
    name: "Edit",
    components: { VueEditor },
    data() {
        return {
            dropzone: null,
            title: null,
            content: null,
            images: null,
            imageIdsForDelete: [],
            imageURLsForDelete: [],
        }
    },
    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: "url",
            autoProcessQueue: false,
            addRemoveLinks: true
        })
        this.dropzone.on('removedfile', (file) => {
            this.imageIdsForDelete.push(file.id)
        })
        this.getPost()
    },
    methods: {
        getPost() {
            const id = this.$route.params.id
            axios.get(`/api/posts/get/${id}`)
                .then(res => {
                    console.log(res)
                    this.title = res.data.data.title
                    this.content = res.data.data.content
                    this.images = res.data.data.images
                    this.images.forEach(image => {
                        let file = { id: image.id, name: image.name, size: image.size };
                        this.dropzone.displayExistingFile(file, image.url);
                    })
                })
        },
        update() {
            const id = this.$route.params.id
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles()
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            });
            this.imageIdsForDelete.forEach( id => {
                data.append('image_ids_for_delete[]', id)
            })
            this.imageURLsForDelete.forEach( url => {
                data.append('image_urls_for_delete[]', url)
            })
            data.append('title', this.title)
            data.append('content', this.content)
            data.append('_method', "patch")
            this.title = null
            this.content = null
            axios.post(`/api/posts/${id}`, data)
                .then(res => {
                    this.getPost()
                    this.imageIdsForDelete = []
                    this.$router.push({name:'show',params:{id:id}})
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
        },
        handleImageRemoved(url){
            this.imageURLsForDelete.push(url)
        }
    }
}
</script>
