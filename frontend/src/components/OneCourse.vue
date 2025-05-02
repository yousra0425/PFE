<template lang="">
   <div class="card">
     <img class="card-img-top" :src="course.image" :alt="course.title">
        <div class="card-body">
            <h4 class="card-title">{{ course.title }}</h4>
               <p class="card-text">Lorem ipsum dolor sit, amet consectetur.</p>
                 <button class="btn btn-sm btn-danger" @Click="deleteCourse(course. id)">Delete</button>
                 <button class="btn btn-sm btn-dark ml-2">{{ course.category }}</button> 

                 
                <div class="my-3">
                    <span class="badge badge-pill badge- ml-2" v-for="tag in course.tag ">{{ tag }}</span>
                </div>
                 
            
        </div>
        
    </div>
</template>
<script>
export default {
    props: ['course'],
    methods: {
        deleteCourse() {
            this.$emit('delete', id)
        }

        
    }
}
</script>
<style lang="">
    
</style>