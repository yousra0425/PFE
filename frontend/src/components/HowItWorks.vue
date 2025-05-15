<template>
    <section class="how-it-works">
      <div class="container">
        <h1 class="section-title">How DomiFix Works</h1>
        <p class="section-subtitle">Getting help around your home has never been easier</p>
        
        <div class="divider"></div>
        
        <div class="steps-container">
          <!-- Step 1 -->
          <div class="step">
            <div class="step-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
              </svg>
            </div>
            <h2 class="step-title">Find the right service</h2>
            <p class="step-description">Browse through our categories and find the service that meets your home needs.</p>
          </div>
          
          <!-- Step 2 (New Map Step) -->
          <div class="step">
            <div class="step-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </div>
            <h2 class="step-title">Choose nearby provider</h2>
            <p class="step-description">View available professionals in your area on our interactive map.</p>
          </div>
          
          <!-- Step 3 -->
          <div class="step">
            <div class="step-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <h2 class="step-title">Book with confidence</h2>
            <p class="step-description">Select your preferred date and time, and book a verified professional instantly.</p>
          </div>
          
          <!-- Step 4 -->
          <div class="step">
            <div class="step-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <h2 class="step-title">Get it done</h2>
            <p class="step-description">Your pro arrives and completes the job to your satisfaction, guaranteed.</p>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <style scoped>
  .how-it-works {
    padding: 4rem 1rem;
    text-align: center;
    background-color: #ffffff;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
  }
  
  .section-subtitle {
    font-size: 1.25rem;
    color: #666666;
    margin-bottom: 2rem;
  }
  
  .divider {
    width: 80px;
    height: 2px;
    background-color: #e0e0e0;
    margin: 0 auto 3rem;
  }
  
  .steps-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
  }
  
  .step {
    padding: 0 1rem;
  }
  
  .step-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
    border-radius: 50%;
    transition: all 0.3s ease;
  }
  
  .step:hover .step-icon {
    background-color: #b7f0ed;
    transform: translateY(-5px);
  }
  
  .step-icon svg {
    width: 24px;
    height: 24px;
    color: #1a978d;
  }
  
  .step-title {
    font-size: 1.25rem;
    font-weight: bolder;
    color: #000000;
    margin-bottom: 1rem;
  }
  
  .step-description {
    font-size: 1rem;
    color: #666666;
    line-height: 1.5;
  }
  
  @media (max-width: 1024px) {
    .steps-container {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 640px) {
    .steps-container {
      grid-template-columns: 1fr;
    }
    
    .step {
      margin-bottom: 2.5rem;
      max-width: 100%;
    }
    
    .step:last-child {
      margin-bottom: 0;
    }
  }
  </style>