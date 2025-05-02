<template lang="">

    <form @submit.prevent="newCourse">
            <div class="form-group">
        <label for="Title">Title</label>
        <input v-model="title" id="Title" type="text" class="form-control">
    </div>

    <div class="form-group">
        <label for="Image">Image url</label>
        <input v-model="image" id="Image" type="text" class="form-control">
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <select v-model="category" id="category" class="form-control">
            <option value="frontend">Frontend</option>
            <option value="backend">Backend</option>
            <option value="fullstack">Fullstack</option>
            <option value="mobile">Mobile</option>
        </select>
    </div>

    <div class="form-check form-check-inline my-3">
        <label class="form-check-label">
            <input v-model="typeOfPayment" class="form-check-input" type="radio" name="typeOfPayment" value="free"> Free
        </label>

        <label class="form-check-label ml-3">
            <input v-model="typeOfPayment" class="form-check-input" type="radio" name="typeOfPayment" value="paying"> Paying
        </label>
    </div>

    <div class="form-check">
        <label class="form-check-label">
            <input type="checkbox" class="form-check-input" v-model="published"> Published
        </label>
    </div>

    <div class="my-3">
        <h5>Tags</h5>
        <div class="form-check" v-for="tag in tags">
            <label for="form-check-label">
                <input type="checkbox" class="form-check-input" :value="tag" v-model="myTags">
                {{ tag }}
            </label>
        </div>
    </div>

    
    <button class="btn btn-block btn-warning">Add Course</button>
    </form>




    <div class="well">
    <p>title : {{title}}</p>
    <p>image: {{image}}</p>
    <p>Category: {{ category }}</p>
    <p>typeOfPayment: {{ typeOfPayment }}</p>
    <p>published: {{ published }}</p>
    <p>my Tags: {{ myTags }}</p>

    </div>
    
</template>
<script>
export default {
    data() {
        return {
            title: '',
            image: '',
            category: '',
            typeOfPayment: 'paying',
            published: false,
            tags: ['Framework', 'Frontend', 'Backend', 'Javascript'], 
            myTags: [] 

        }
    },
    methods:{
        newCourse( ) {
            let title = this.title;
            let image = this.image;

            if(title == "" || image == "") {
                return;
            }

            const course = {
                title, 
                image,
                category: this.category,
                tags: this.myTags
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