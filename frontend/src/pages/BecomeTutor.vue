<template>
  <div class="become-tutor-wrapper">
    <component
      :is="getCurrentStepComponent()"
      :currentStep="step"
      :steps="steps"
      :formData="formData"
      @next="goToNextStep"
      @back="goToPreviousStep"
      @submit="handleSubmit"
      @update:formData="updateFormData"
    />
  </div>
</template>

<script>
import StepOne from '../components/BecomeTutor/StepOne.vue';
import StepTwo from '../components/BecomeTutor/StepTwo.vue';
import StepThree from '../components/BecomeTutor/StepThree.vue';
import StepFour from '../components/BecomeTutor/StepFour.vue';

export default {
  name: 'BecomeTutor',
  components: {
    StepOne,
    StepTwo,
    StepThree,
    StepFour,
  },
  data() {
    return {
      step: 1,
      totalSteps: 4,
      steps: [
        { title: 'Personal Info', description: 'Tell us about yourself' },
        { title: 'Teaching Info', description: 'What do you teach?' },
        { title: 'Documents Upload', description: 'Upload your certificates' },
        { title: 'Review & Submit', description: 'Check everything and submit' },
      ],
      formData: {
        personalInfo: {},
        subjects: [],
        documents: [],
      },
    };
  },
  methods: {
    getCurrentStepComponent() {
      const componentsMap = {
        1: 'StepOne',
        2: 'StepTwo',
        3: 'StepThree',
        4: 'StepFour',
      };
      return componentsMap[this.step];
    },
    goToNextStep() {
      if (this.step < this.totalSteps) {
        this.step++;
      }
    },
    goToPreviousStep() {
      if (this.step > 1) {
        this.step--;
      }
    },
    updateFormData(updatedData) {
      this.formData = { ...this.formData, ...updatedData };
    },
    handleSubmit() {
  if (this.step === this.totalSteps) {
    console.log('Submitting tutor application:', this.formData);
    alert('Application submitted successfully!');
    this.step = 1;
    this.formData = {
      personalInfo: {},
      subjects: [],
      documents: [],
    };
  } else {
    // Just go to next step if not final step (in case a child wrongly emits submit early)
    this.goToNextStep();
  }
},

  },
};
</script>

<style scoped>
.become-tutor-wrapper {
  max-width: 700px;
  margin: 2rem auto;
  padding: 1rem;
}
</style>
