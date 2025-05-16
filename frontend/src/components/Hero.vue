<template>
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">Your Home Deserves <span>Expert Care</span></h1>
          <p class="hero-description">
            Connect with trusted professionals for all your home service needs. From plumbing and electrical work to cleaning and maintenance — all in one place.
          </p>
          
          <div class="cta-section">
            <h3 class="cta-title">What service do you need?</h3>
            <button class="cta-button" @click="scrollToHowItWorks">Find Services</button>
          </div>
          
          <div class="guarantees">
            <div class="guarantee-item">
              <svg class="check-icon" viewBox="0 0 24 24">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>100% Satisfaction Guarantee</span>
            </div>
            <div class="guarantee-item">
              <svg class="check-icon" viewBox="0 0 24 24">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Verified Professionals</span>
            </div>
            <div class="guarantee-item">
              <svg class="check-icon" viewBox="0 0 24 24">
                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Secure Payments</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
<script>
export default {
  methods: {
    scrollToHowItWorks() {
      const element = document.getElementById('how-it-works');
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    }
  }
}
</script>
  
  <style scoped>
  .hero-section {
    background-color: #f8f9fa;
    padding: 5rem 1rem;
    text-align: center;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .hero-content {
    max-width: 800px;
    margin: 0 auto;
  }
  
  .hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
    color: #1a1a1a;
    margin-bottom: 1.5rem;
  }
  
  .hero-title span {
    color: #1ec9ba;
  }
  
  .hero-description {
    font-size: 1.1rem;
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 2.5rem;
  }
  
  .cta-section {
    margin: 2.5rem 0;
  }
  
  .cta-title {
    font-size: 1.25rem;
    color: #4a5568;
    margin-bottom: 1rem;
  }
  
  .cta-button {
    background-color: #1ca79b;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .cta-button:hover {
    background-color: #1a978dcc;
    transform: translateY(-2px);
  }
  
  .guarantees {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 3rem;
  }
  
  .guarantee-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    color: #4a5568;
  }
  
  .check-icon {
    width: 18px;
    height: 18px;
    color: #10b981;
  }
  
  @media (max-width: 768px) {
    .hero-section {
      padding: 3rem 1rem;
    }
    
    .hero-title {
      font-size: 2rem;
    }
    
    .hero-description {
      font-size: 1rem;
    }
    
    .guarantees {
      flex-direction: column;
      align-items: center;
    }
  }
  </style>