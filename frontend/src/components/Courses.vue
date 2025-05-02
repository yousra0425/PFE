<template lang="">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <AddCourse  @add="addCourse($event)"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">List Courses</li>
                </ol>
              </nav>

            <h1>All Courses
                <hr>
            </h1>
        </div>
        <div class="cold-md-6 text-right">
            <button class="btn btn-sm btn-success">New</button>
        </div>
    </div>

    <div class="row">
       <div class="col-md-4" v-for="course in courses">
         <OneCourse :course="course" @delete="deleteOneCourse($event)" />
       </div>
    </div>
 </template>

 <script>
   import OneCourse from './OneCourse.vue'
   import AddCourse from './AddCourse.vue'
 export default {
    components: {
        OneCourse,
        AddCourse,
    },
    data(){
        return {
            courses: [
                {
                    id: 1,
                    title: "Learn ReactJS",
                    category: 'Framework',
                    tags: [],
                    image:"https://process.fs.teachablecdn.com/ADNupMnWyR7kCWRvm76Laz/resize=width:705/https://cdn.filestackcontent.com/DXitcrOkSqig73mA6GPS"

                },

                {
                    id: 2,
                    title: "Learn ReactJS",
                    category: 'Framework',
                    tags: [],
                    image:"https://process.fs.teachablecdn.com/ADNupMnWyR7kCWRvm76Laz/resize=width:705/https://cdn.filestackcontent.com/DXitcrOkSqig73mA6GPS"

                },

                {
                    id: 3,
                    title: "Learn ReactJS",
                    category: 'Framework',
                    tags: [],
                    image:"https://process.fs.teachablecdn.com/ADNupMnWyR7kCWRvm76Laz/resize=width:705/https://cdn.filestackcontent.com/DXitcrOkSqig73mA6GPS"

                }
            ],

            
        }
    },
    methods: {
        deleteOneCourse(id){
            this.courses = this.courses.filter(course => course.id != id)
        },

        addCourse(course) {
            this.courses.unshift(course);

        }
    }
 }
 </script>

 <style scoped>
    h1 {
        color: rgb(23, 54, 138);
    }
 </style>