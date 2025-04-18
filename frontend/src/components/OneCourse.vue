<template lang="">
   <div class="card">
     <img class="card-img-top" :src="image" :alt="title">
        <div class="card-body">
            <h4 class="card-title">{{ title }}</h4>
               <p class="card-text">Lorem ipsum dolor sit, amet consectetur.</p>
                 <button class="btn btn-sm btn-danger" @onClick="deleteCourse(id)">Delete</button> 
                 
            
        </div>
        
    </div>
</template>
<script>
export default {
    props: ['title', 'image', 'id'],
    methods: {
        deleteCourse() {
            this.$emit('delete', id)
        }

        
    }
}
</script>
<style lang="">
    
</style>