<script setup>

</script>

<template>
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div>
        <h3>Signup</h3>
        <hr />
      </div>
      <form>
         
         <div class="form-group">
          <label for="fullName">Full Name</label>
          <input
            type="text"
            class="form-control"
            id="fullName"
            placeholder="Enter your full name"
            required
          />
        </div>

        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" placeholder="Enter your email" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" placeholder="Enter your password" required />
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password" required />
        </div>

        <div class="form-group">
          <label for="city">City</label>
          <input type="text" class="form-control" id="city" placeholder="Enter your city" required />
        </div>

        <div class="form-group">
          <label for="country">Country</label>
          <select class="form-control" id="country" required>
            <option value="" disabled selected>Select your country</option>
            <option value="US">United States</option>
            <option value="CA">Canada</option>
            <option value="GB">United Kingdom</option>
           
          </select>
        </div>

        <div class="my-3">
          <button type="submit" class="btn btn-primary">Signup</button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>
 