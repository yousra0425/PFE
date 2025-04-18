<template lang="">
    <div class="form-group">
        <label for="Title">Title</label>
        <input ref="title"id="Title" type="text" class="form-control">
    </div>

    <div class="form-group">
        <label for="Image">Image url</label>
        <input ref="image"id="Image" type="text" class="form-control">
    </div>


    
    <button @click="newCourse" class="btn btn-block btn-warning">Add Course</button>

</template>
<script>
export default {
    methods:{
        newCourse( ) {
            let title = this.$refs.title.value;
            let image = this.$refs.image.value;

            if(title == "" || image == "") {
                return;
            }

            const course = {
                title, 
                image
            }

            this.$emit('add', course)
            this.$refs.title.value="";
            this.$refs.image.value="";
        }
    }
}
</script>
<style lang="">
    
</style>