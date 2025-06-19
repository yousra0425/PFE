<template>
  <div class="container my-5">
    <div
      class="row g-0 rounded-4 overflow-hidden shadow-lg hover-shadow animated-container"
      style="background-color: #1ca79b68;"
    >
      <!-- Left Image -->
      <div class="col-md-7">
        <img
          :src="image"
          alt="Promotional image"
          class="img-fluid h-100 w-100 promo-img"
        />
      </div>

      <!-- Right Text Box -->
      <div class="col-md-5 d-flex flex-column justify-content-center p-4 text-box">
        <h1 class="fw-bold mb-3">You too,<br /> become a super tutor</h1>
        <p class="text-muted mb-4">
          Share your knowledge, live your passion, and gain independence
        </p>
        <a
          :href="buttonLink"
          class="btn btn-dark rounded-pill fw-semibold d-inline-flex align-items-center custom-btn"
        >
          {{ buttonText }}
          <i class="bi bi-lightning-fill ms-2"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  image: {
    type: String,
    required: true
  },
  buttonLink: {
    type: String,
    default: '#'
  },
  buttonText: {
    type: String,
    default: 'Learn more'
  }
});
</script>

<style scoped>
.promo-img {
  object-fit: cover;
  transition: transform 0.5s ease;
}

.promo-img:hover {
  transform: scale(1.03);
}

.animated-container {
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.animated-container:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  transform: translateY(-5px);
}

.custom-btn {
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.custom-btn:hover {
  background-color: #1d1f21;
  transform: translateY(-2px);
}

.text-box {
  transition: background-color 0.3s ease;
}

.text-box:hover {
  background-color: rgba(255, 255, 255, 0.02);
}
</style>
