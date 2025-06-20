<template>
  <div>
    <div v-if="profile && profile.first_name !== undefined">
      <!-- Centered dashboard content -->
      <div class="dashboard-wrapper">
        <h2 class="dashboard-title">Welcome, {{ profile.first_name }} {{ profile.last_name }}</h2>

        <!-- Tabs -->
        <div class="nav-tabs">
          <span :class="{ active: activeTab === 'users' }" @click="activeTab = 'users'">Users</span>
          <span :class="{ active: activeTab === 'profile' }" @click="activeTab = 'profile'">Profile</span>
          <span v-if="isAdmin" :class="{ active: activeTab === 'categories' }" @click="activeTab = 'categories'">Categories</span>
        </div>

        <!-- USERS MANAGEMENT SECTION -->
        <div v-if="activeTab === 'users'" class="users-management-section">
          <h3>Manage Users</h3>

          <!-- Filter Controls -->
          <div class="user-filters">
            <div class="filter-group">
              <label>Filter by Role:</label>
              <select v-model="roleFilter" class="filter-select">
                <option value="all">All Users</option>
                <option value="tutor">Tutors Only</option>
                <option value="student">Students Only</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Filter by Status:</label>
              <select v-model="statusFilter" class="filter-select">
                <option value="all">All Status</option>
                <option value="approved">Approved Only</option>
                <option value="pending">Pending Only</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Search:</label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name or email"
                class="search-input"
              >
            </div>
          </div>

          <!-- No Users Found -->
          <div v-if="filteredUsers.length === 0" class="no-users">
            <i class="fas fa-users-slash"></i>
            <p>No users match your filters.</p>
          </div>

          <!-- Users List -->
          <div v-else>
            <div v-for="user in filteredUsers" :key="user.id" class="user-card">
              <!-- User Profile Section -->
              <div class="user-profile">
                <div class="avatar-container">
                  <img
                    v-if="user.profile_picture"
                    :src="user.profile_picture"
                    class="profile-avatar"
                    alt="Profile picture"
                  >
                  <div v-else class="avatar-placeholder">
                    {{ user.first_name.charAt(0) }}{{ user.last_name.charAt(0) }}
                  </div>
                </div>

                <div class="user-info">
                  <h4>{{ user.first_name }} {{ user.last_name }}</h4>
                  <div class="user-meta">
                    <span class="user-role" :class="user.role">
                      <i :class="getRoleIcon(user.role)"></i> {{ user.role }}
                    </span>
                    <span class="user-email">
                      <i class="fas fa-envelope"></i> {{ user.email }}
                    </span>
                    <template v-if="user.telephone">
                      <span class="user-phone">
                        <i class="fas fa-phone"></i> {{ user.telephone }}
                      </span>
                    </template>
                  </div>

                  <div v-if="user.role === 'tutor'" class="tutor-status" :class="{ approved: user.isApproved }">
                    <i class="fas" :class="user.isApproved ? 'fa-check-circle' : 'fa-clock'"></i>
                    {{ user.isApproved ? 'Approved Tutor' : 'Pending Approval' }}
                  </div>
                </div>
              </div>

              <!-- CIN Verification Section (for tutors) -->
              <div v-if="user.role === 'tutor'" class="cin-verification">
                <h5>ID Verification</h5>
                <div v-if="user.cin_image" class="cin-image-container">
                  <img
                    :src="user.cin_image"
                    class="cin-image"
                    alt="National ID"
                    @click="showFullImage(user.cin_image)"
                  >
                  <div class="verification-status">
                    <span v-if="user.cin_verified" class="verified">
                      <i class="fas fa-check-circle"></i> Verified
                    </span>
                    <span v-else class="unverified">
                      <i class="fas fa-exclamation-circle"></i> Unverified
                    </span>
                  </div>
                  <button class="btn-view" @click="showFullImage(user.cin_image)">
                    <i class="fas fa-expand"></i> View Full ID
                  </button>
                </div>
                <div v-else class="no-cin">
                  <i class="fas fa-id-card"></i>
                  <p>No ID uploaded yet</p>
                </div>

                <!-- Admin verification actions -->
                <div v-if="user.cin_image" class="verification-actions">
                  <button
                    v-if="!user.cin_verified"
                    @click="verifyCin(user.id)"
                    class="btn-verify"
                  >
                    <i class="fas fa-check"></i> Verify ID
                  </button>
                  <button
                    v-if="user.cin_verified"
                    @click="unverifyCin(user.id)"
                    class="btn-unverify"
                  >
                    <i class="fas fa-times"></i> Unverify
                  </button>
                  <button
                    @click="requestNewCin(user.id)"
                    class="btn-request"
                  >
                    <i class="fas fa-redo"></i> Request New
                  </button>
                </div>
              </div>

              <!-- User Actions -->
              <div class="user-actions">
                <div v-if="user.role === 'tutor'">
                  <button
                    v-if="!user.isApproved"
                    @click="approveTutor(user.id)"
                    class="btn-approve"
                    :disabled="!user.cin_verified"
                    :title="!user.cin_verified ? 'Cannot approve - ID not verified' : ''"
                  >
                    <i class="fas fa-check"></i> Approve Tutor
                  </button>
                  <button
                    v-if="user.isApproved"
                    @click="revokeApproval(user.id)"
                    class="btn-revoke"
                  >
                    <i class="fas fa-times"></i> Revoke
                  </button>
                </div>
                <button
                  @click="deleteUser(user.id)"
                  class="btn-delete"
                >
                  <i class="fas fa-trash"></i> Delete User
                </button>
              </div>
            </div>
          </div>
        </div>

<!-- PROFILE SECTION -->
<div v-if="activeTab === 'profile'" class="profile-card">
  <div class="card-header">
    <h4>Profile Information</h4>
    <button v-if="!isEditing" @click="startEditing" class="edit-btn">Edit Profile</button>
  </div>

  <!-- View Mode -->
  <div v-if="!isEditing" class="card-body">
    <div class="info-grid">
      <div>
        <small class="label">Full Name</small>
        <div class="value"><strong>{{ profile.first_name }} {{ profile.last_name }}</strong></div>
      </div>
      <div>
        <small class="label">Email</small>
        <div class="value"><strong>{{ profile.email }}</strong></div>
      </div>
      <div>
        <small class="label">Phone</small>
        <div class="value"><strong>{{ profile.telephone }}</strong></div>
      </div>
      <div>
        <small class="label">Address</small>
        <div class="value"><strong>{{ profile.address }}</strong></div>
      </div>
      <div>
        <small class="label">Password</small>
        <div class="value"><strong>********</strong></div>
      </div>
    </div>
  </div>

  <!-- Edit Mode -->
  <div v-else class="card-body">
    <div class="form-grid">
      <div>
        <label>First Name</label>
        <input v-model="editProfile.first_name" type="text" class="form-control" />
      </div>
      <div>
        <label>Last Name</label>
        <input v-model="editProfile.last_name" type="text" class="form-control" />
      </div>
      <div>
        <label>Email</label>
        <input v-model="editProfile.email" type="email" class="form-control" />
      </div>
      <div>
        <label>Phone</label>
        <input v-model="editProfile.telephone" type="text" class="form-control" />
      </div>
      <div class="col-12">
        <label>Address</label>
        <textarea v-model="editProfile.address" class="form-control"></textarea>
      </div>
      <div class="col-12">
        <label>New Password</label>
        <input v-model="editProfile.password" type="password" class="form-control" placeholder="Leave blank to keep current password" />
      </div>
    </div>
    <div class="edit-actions">
      <button @click="saveProfile" class="btn btn-primary">Save Changes</button>
      <button @click="cancelEditing" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>


        <!-- CATEGORIES SECTION -->
        <div v-if="activeTab === 'categories'" class="categories-section">
          <div class="section-header">
            <h3>Manage Categories</h3>
            <p class="section-description">Organize your learning content by categories and subcategories</p>
          </div>

          <!-- Add new category card -->
          <div class="add-category-card">
            <div class="input-group">
              <input
                v-model="newCategoryName"
                placeholder="Enter new category name"
                class="category-input"
                @keyup.enter="addCategory"
              />
              <button @click="addCategory" class="btn-add">
                <i class="fas fa-plus"></i> Add Category
              </button>
            </div>
          </div>

          <!-- Categories list -->
          <div v-if="categories.length > 0" class="categories-list">
            <div v-for="category in categories" :key="category.id" class="category-card">
              <!-- Category header -->
              <div class="category-header">
                <!-- Edit mode -->
                <div v-if="editCategoryId === category.id" class="edit-mode">
                  <input
                    v-model="editCategoryName"
                    class="edit-input"
                    ref="categoryInput"
                    @keyup.enter="saveEditCategory"
                    @keyup.esc="cancelEditCategory"
                  />
                  <div class="action-buttons">
                    <button @click="saveEditCategory" class="btn-save">
                      <i class="fas fa-check"></i> Save
                    </button>
                    <button @click="cancelEditCategory" class="btn-cancel">
                      <i class="fas fa-times"></i> Cancel
                    </button>
                  </div>
                </div>
                <!-- View mode -->
                <div v-else class="view-mode">
                  <div class="category-title">
                    <i class="fas fa-folder category-icon"></i>
                    <span class="category-name">{{ category.label }}</span>
                    <span class="subcategory-count">
                      ({{ category.subcategories.length }} subcategories)
                    </span>
                  </div>
                  <div class="action-buttons">
                    <button @click="startEditCategory(category)" class="btn-edit">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <button @click="deleteCategory(category.id)" class="btn-delete">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </div>
                </div>
              </div>

              <!-- Subcategories list -->
              <div class="subcategories-container">
                <div v-if="category.subcategories.length > 0" class="subcategories-list">
                  <div v-for="subcategory in category.subcategories" :key="subcategory.id" class="subcategory-item">
                    <!-- Subcategory edit mode -->
                    <div v-if="editSubcategoryId === subcategory.id" class="edit-mode">
                      <input
                        v-model="editSubcategoryName"
                        class="edit-input"
                        ref="subcategoryInput"
                        @keyup.enter="saveEditSubcategory"
                        @keyup.esc="cancelEditSubcategory"
                      />
                      <div class="action-buttons">
                        <button @click="saveEditSubcategory" class="btn-save">
                          <i class="fas fa-check"></i> Save
                        </button>
                        <button @click="cancelEditSubcategory" class="btn-cancel">
                          <i class="fas fa-times"></i> Cancel
                        </button>
                      </div>
                    </div>
                    <!-- Subcategory view mode -->
                    <div v-else class="view-mode">
                      <div class="subcategory-title">
                        <i class="fas fa-file-alt subcategory-icon"></i>
                        <span>{{ subcategory.name }}</span>
                      </div>
                      <div class="action-buttons">
                        <button @click="startEditSubcategory(category.id, subcategory)" class="btn-edit">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button @click="deleteSubcategory(category.id, subcategory.id)" class="btn-delete">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="no-subcategories">
                  No subcategories yet
                </div>

                <!-- Add subcategory input -->
                <div class="add-subcategory">
                  <div class="input-group">
                    <input
                      v-model="newSubcategoryName[category.id]"
                      :placeholder="`Add subcategory to ${category.label}`"
                      class="subcategory-input"
                      @keyup.enter="addSubcategory(category.id)"
                    />
                    <button @click="addSubcategory(category.id)" class="btn-add-small">
                      <i class="fas fa-plus"></i> Add
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="no-categories">
            <i class="fas fa-folder-open empty-icon"></i>
            <p>No categories yet. Create your first category to get started!</p>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      Loading profile...
    </div>
  </div>

</template>

<script>
export default {
  name: "UserDashboard",
  data() {
    return {
      isEditing: false,
      profile: {
        first_name: "",
        last_name: "",
        telephone: "",
        email: "",
        address: "",
      },
      activeTab: "users", // default tab
      users: [],
      editProfile: {},
      showCinModal: false,
      currentCinImage: null,
      isAdmin: true, // for testing/admin
      roleFilter: 'all',
      statusFilter: 'all',
      searchQuery: '',
      categories: [],
      newCategoryName: "",
      editCategoryId: null,
      editCategoryName: "",

      // Subcategory state
      newSubcategoryName: {}, // key: categoryId, value: string
      editSubcategoryId: null,
      editSubcategoryName: "",
      editSubcategoryCategoryId: null,
    };
  },
  computed: {
    filteredUsers() {
      return this.users.filter(user => {
        // Role filter
        const roleMatch = this.roleFilter === 'all' || 
                         user.role === this.roleFilter;
        
        // Status filter (only applies to tutors)
        let statusMatch = true;
        if (this.statusFilter !== 'all' && user.role === 'tutor') {
          statusMatch = this.statusFilter === 'approved' ? 
                       user.isApproved : !user.isApproved;
        }
        
        // Search filter
        const searchMatch = this.searchQuery === '' ||
          user.first_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          user.last_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          user.email.toLowerCase().includes(this.searchQuery.toLowerCase());
        
        return roleMatch && statusMatch && searchMatch;
      });
    }
  },
  methods: {

    async verifyCin(userId) {
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}/verify-cin`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!res.ok) throw new Error('Failed to verify ID');

        const user = this.users.find(u => u.id === userId);
        if (user) {
          user.cin_verified = true;
        }
      } catch (error) {
        console.error('Verify ID error:', error);
        alert('Failed to verify ID');
      }
    },

    async unverifyCin(userId) {
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}/unverify-cin`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!res.ok) throw new Error('Failed to unverify ID');

        const user = this.users.find(u => u.id === userId);
        if (user) {
          user.cin_verified = false;
        }
      } catch (error) {
        console.error('Unverify ID error:', error);
        alert('Failed to unverify ID');
      }
    },

    async requestNewCin(userId) {
      if (!confirm('Send request to upload a new ID document?')) return;
      
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}/request-new-cin`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!res.ok) throw new Error('Failed to send request');

        alert('Request sent successfully');
      } catch (error) {
        console.error('Request new ID error:', error);
        alert('Failed to send request');
      }
    },

    showFullImage(imageUrl) {
      this.currentCinImage = imageUrl;
      this.showCinModal = true;
    },

    closeModal() {
      this.showCinModal = false;
      this.currentCinImage = null;
    },
    // Profile methods
    startEditing() {
      this.editProfile = { ...this.profile };
      this.isEditing = true;
    },
    cancelEditing() {
      this.isEditing = false;
    },
    saveProfile() {
      this.profile = { ...this.editProfile };
      localStorage.setItem("user", JSON.stringify(this.profile));
      this.isEditing = false;
    },
    async loadProfile() {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      this.$router.push("/login");
      return;
    }
    
    const res = await fetch("http://localhost:8000/api/profile", {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
      },
    });

    if (!res.ok) {
      this.$router.push("/login");
      return;
    }

    const user = await res.json();

    if (!user || !user.first_name) {
      this.$router.push("/login");
      return;
    }

    this.profile = {
      first_name: user.first_name || "",
      last_name: user.last_name || "",
      telephone: user.telephone || "",
      email: user.email || "",
      address: user.address || "",
    };

  } catch (e) {
    console.error("Error loading profile:", e);
    this.$router.push("/login");
  }
},


    // Fetch categories from API
    async fetchCategories() {
      try {
        const token = localStorage.getItem("token");
        const res = await fetch("http://localhost:8000/api/categories-with-subcategories", {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) throw new Error("Failed to fetch categories with subcategories");

        const data = await res.json();
        this.categories = Array.isArray(data) ? data : data.data;
      } catch (error) {
        console.error("Could not load categories:", error);
        alert("Could not load categories.");
      }
    },

    async fetchUsers() {
      try {
        const token = localStorage.getItem("token");
        const res = await fetch("http://localhost:8000/api/users", {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) throw new Error("Failed to fetch users");

        const data = await res.json();
        this.users = Array.isArray(data) ? data : data.data;
      } catch (error) {
        console.error("Could not load users:", error);
        alert("Could not load users.");
      }
    },

    // CATEGORY METHODS
    async addCategory() {
      const trimmedName = this.newCategoryName.trim();
      if (!trimmedName) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch("http://localhost:8000/api/categories", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({ label: trimmedName }),
        });

        if (!res.ok) {
          const errorData = await res.json();
          throw new Error(errorData.message || "Failed to add category");
        }

        const newCategory = await res.json();
        newCategory.subcategories = [];
        this.categories.push(newCategory);
        this.newCategoryName = "";
      } catch (error) {
        console.error("Add category error:", error);
        alert(`Could not add category: ${error.message}`);
      }
    },

    startEditCategory(category) {
      this.editCategoryId = category.id;
      this.editCategoryName = category.label;
    },

    cancelEditCategory() {
      this.editCategoryId = null;
      this.editCategoryName = "";
    },

    async saveEditCategory() {
      const trimmedName = this.editCategoryName.trim();
      if (!trimmedName) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/categories/${this.editCategoryId}`, {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ label: trimmedName }),
        });

        if (!res.ok) throw new Error("Failed to update category");

        const updatedCategory = await res.json();
        const index = this.categories.findIndex((c) => c.id === this.editCategoryId);
        if (index !== -1) {
          this.categories[index].label = updatedCategory.label;
        }
        this.cancelEditCategory();
      } catch (error) {
        console.error("Update category error:", error);
        alert("Failed to update category.");
      }
    },

    async deleteCategory(id) {
      if (!confirm("Are you sure you want to delete this category?")) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/categories/${id}`, {
          method: "DELETE",
          headers: { Authorization: `Bearer ${token}` },
        });

        if (!res.ok) throw new Error("Failed to delete category");

        this.categories = this.categories.filter((c) => c.id !== id);
      } catch (error) {
        console.error("Delete category error:", error);
        alert("Failed to delete category.");
      }
    },

    // SUBCATEGORY METHODS
    async addSubcategory(categoryId) {
      const name = this.newSubcategoryName[categoryId]?.trim();
      if (!name) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch("http://localhost:8000/api/subcategories", {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ label: name, category_id: categoryId }),
        });

        if (!res.ok) {
          const errorData = await res.json();
          throw new Error(errorData.message || "Failed to add subcategory");
        }

        const newSubcategory = await res.json();
        const category = this.categories.find((c) => c.id === categoryId);
        if (category) {
          category.subcategories.push(newSubcategory);
        }
        this.$set(this.newSubcategoryName, categoryId, "");
      } catch (error) {
        console.error("Add subcategory error:", error);
        alert(`Could not add subcategory: ${error.message}`);
      }
    },

    startEditSubcategory(categoryId, subcategory) {
      this.editSubcategoryId = subcategory.id;
      this.editSubcategoryName = subcategory.label;
      this.editSubcategoryCategoryId = categoryId;
    },

    cancelEditSubcategory() {
      this.editSubcategoryId = null;
      this.editSubcategoryName = "";
      this.editSubcategoryCategoryId = null;
    },

    async saveEditSubcategory() {
      if (!this.editSubcategoryName.trim()) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/subcategories/${this.editSubcategoryId}`, {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ label: this.editSubcategoryName }),
        });

        if (!res.ok) throw new Error("Failed to update subcategory");

        const updatedSubcategory = await res.json();
        const category = this.categories.find((c) => c.id === this.editSubcategoryCategoryId);
        if (category) {
          const index = category.subcategories.findIndex((s) => s.id === this.editSubcategoryId);
          if (index !== -1) {
            category.subcategories[index].label = updatedSubcategory.label;
          }
        }
        this.cancelEditSubcategory();
      } catch (error) {
        console.error("Update subcategory error:", error);
        alert("Failed to update subcategory.");
      }
    },

    async deleteSubcategory(categoryId, subcategoryId) {
      if (!confirm("Are you sure you want to delete this subcategory?")) return;

      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/subcategories/${subcategoryId}`, {
          method: "DELETE",
          headers: { Authorization: `Bearer ${token}` },
        });

        if (!res.ok) throw new Error("Failed to delete subcategory");

        const category = this.categories.find((c) => c.id === categoryId);
        if (category) {
          category.subcategories = category.subcategories.filter((s) => s.id !== subcategoryId);
        }
      } catch (error) {
        console.error("Delete subcategory error:", error);
        alert("Failed to delete subcategory.");
      }
    },

    // User management methods
    getRoleIcon(role) {
      return {
        'admin': 'fas fa-shield-alt',
        'tutor': 'fas fa-chalkboard-teacher',
        'student': 'fas fa-user-graduate'
      }[role] || 'fas fa-user';
    },
    
    showFullImage(imageUrl) {
      window.open(imageUrl, '_blank');
    },
    
    async approveTutor(userId) {
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}/approve`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) throw new Error("Failed to approve tutor");

        const user = this.users.find((u) => u.id === userId);
        if (user) {
          user.isApproved = true;
        }
      } catch (error) {
        console.error("Approve tutor error:", error);
        alert("Failed to approve tutor.");
      }
    },

    async revokeApproval(userId) {
      if (!confirm("Are you sure you want to revoke this tutor's approval?")) return;
      
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}/revoke`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        if (!res.ok) throw new Error("Failed to revoke approval");

        const user = this.users.find((u) => u.id === userId);
        if (user) {
          user.isApproved = false;
        }
      } catch (error) {
        console.error("Revoke approval error:", error);
        alert("Failed to revoke approval.");
      }
    },

    async deleteUser(userId) {
      const user = this.users.find((u) => u.id === userId);
      if (!user || !confirm(`Are you sure you want to delete ${user.first_name} ${user.last_name}?`)) return;
      
      try {
        const token = localStorage.getItem("token");
        const res = await fetch(`http://localhost:8000/api/users/${userId}`, {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        if (!res.ok) throw new Error("Failed to delete user");

        this.users = this.users.filter((u) => u.id !== userId);
      } catch (error) {
        console.error("Delete user error:", error);
        alert("Failed to delete user.");
      }
    },
  },
  created() {
    this.loadProfile();
    this.fetchCategories();
    this.fetchUsers();
  },
};
</script>

<style scoped>
/* Base Dashboard Styles */
.dashboard-wrapper {
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-title {
  font-weight: 700;
  font-size: 32px;
  margin-bottom: 30px;
  color: #2d3748;
}

.nav-tabs {
  display: flex;
  background-color: #f4f6f8;
  padding: 10px;
  border-radius: 8px;
  width: fit-content;
  gap: 20px;
  margin-bottom: 20px;
}

.nav-tabs span {
  cursor: pointer;
  padding: 8px 16px;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-weight: 500;
  color: #4a5568;
}

.nav-tabs span.active {
  background-color: white;
  font-weight: 600;
  color: #1f2937;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

/* Users Management Section */
.users-management-section {
  background: #fff;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.no-users {
  text-align: center;
  padding: 40px;
  color: #a0aec0;
}

.no-users i {
  font-size: 40px;
  margin-bottom: 15px;
  color: #cbd5e0;
}

.user-card {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  padding: 20px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin-bottom: 15px;
  background: #f8fafc;
}

.user-profile {
  display: flex;
  gap: 15px;
  flex: 1;
  min-width: 250px;
}

.avatar-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  background: #edf2f7;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.profile-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  font-size: 24px;
  font-weight: bold;
  color: #4a5568;
}

.user-info {
  flex: 1;
}

.user-info h4 {
  margin: 0 0 5px 0;
  color: #2d3748;
  font-size: 18px;
}

.user-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  font-size: 14px;
  color: #718096;
  margin-bottom: 10px;
}

.user-role {
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.user-role.admin {
  background: #ebf8ff;
  color: #3182ce;
}

.user-role.tutor {
  background: #ebf4ff;
  color: #5a67d8;
}

.user-role.student {
  background: #f0fff4;
  color: #38a169;
}

.tutor-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 500;
}

.tutor-status.approved {
  background: #f0fff4;
  color: #38a169;
}

.tutor-status:not(.approved) {
  background: #fffaf0;
  color: #dd6b20;
}

.cin-verification {
  flex: 1;
  min-width: 250px;
}

.cin-verification h5 {
  margin: 0 0 10px 0;
  color: #4a5568;
  font-size: 15px;
}

.cin-image-container {
  max-width: 300px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
}

.cin-image {
  width: 100%;
  height: auto;
  display: block;
  cursor: pointer;
  transition: transform 0.2s;
}

.cin-image:hover {
  transform: scale(1.02);
}

.btn-view {
  width: 100%;
  padding: 8px;
  background: #edf2f7;
  border: none;
  color: #4a5568;
  cursor: pointer;
  font-size: 13px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

.btn-view:hover {
  background: #e2e8f0;
}

.user-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  justify-content: center;
  min-width: 150px;
}

.user-actions button {
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  font-weight: 500;
}

.btn-approve {
  background: #f0fff4;
  color: #38a169;
}

.btn-approve:hover {
  background: #c6f6d5;
}

.btn-revoke {
  background: #fff5f5;
  color: #e53e3e;
}

.btn-revoke:hover {
  background: #fed7d7;
}

.btn-delete {
  background: #fff5f5;
  color: #e53e3e;
}

.btn-delete:hover {
  background: #fed7d7;
}

/* Profile Section */
.profile-card {
  background: white;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h4 {
  font-weight: 600;
  color: #2d3748;
  margin: 0;
}

.edit-btn {
  padding: 8px 16px;
  background-color: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.edit-btn:hover {
  background-color: #3182ce;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.label {
  font-size: 13px;
  color: #718096;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value {
  margin-top: 5px;
  font-size: 16px;
  color: #2d3748;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.2s;
}

.form-control:focus {
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
  outline: none;
}

textarea.form-control {
  min-height: 100px;
}

.edit-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.btn {
  padding: 10px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #4299e1;
  color: white;
  border: none;
}

.btn-primary:hover {
  background: #3182ce;
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
  border: none;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

/* Categories Section */
.categories-section {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-header {
  margin-bottom: 25px;
}

.section-header h3 {
  font-size: 22px;
  font-weight: 700;
  color: #2d3748;
  margin-bottom: 5px;
}

.section-description {
  color: #718096;
  font-size: 14px;
  margin: 0;
}

.add-category-card {
  background: #f8fafc;
  border: 1px dashed #cbd5e0;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 25px;
}

.input-group {
  display: flex;
  gap: 10px;
}

.category-input, .subcategory-input {
  flex: 1;
  padding: 10px 15px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.2s;
}

.category-input:focus, .subcategory-input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
  outline: none;
}

.btn-add, .btn-add-small {
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  padding: 10px 15px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-add-small {
  padding: 10px 12px;
  font-size: 13px;
}

.btn-add:hover, .btn-add-small:hover {
  background: var(--primary-color);
}

.categories-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.category-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.category-header {
  padding: 15px;
  background: #f7fafc;
  border-bottom: 1px solid #edf2f7;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.category-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.category-icon {
  color: var(--primary-color);
  font-size: 18px;
}

.category-name {
  font-weight: 600;
  color: #2d3748;
}

.subcategory-count {
  font-size: 13px;
  color: #718096;
  margin-left: 8px;
}

.subcategories-container {
  padding: 15px;
}

.subcategories-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 15px;
}

.subcategory-item {
  background: #f8fafc;
  border-radius: 6px;
  padding: 10px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.subcategory-title {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #4a5568;
}

.subcategory-icon {
  color: #718096;
  font-size: 14px;
}

.no-subcategories {
  color: #a0aec0;
  font-style: italic;
  font-size: 14px;
  padding: 10px 0;
  text-align: center;
}

.add-subcategory {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px dashed #e2e8f0;
}

.edit-mode {
  display: flex;
  gap: 10px;
  width: 100%;
  
}

.edit-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-edit, .btn-save, .btn-cancel, .btn-delete {
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
  border: none;
}

.btn-edit {
  background: #e6fffa;
  color: #38b2ac;
  border: 1px solid #b2f5ea;
  margin-left: 900px;
}

.btn-edit:hover {
  background: #b2f5ea;
}

.btn-save {
  background: #ebf8ff;
  color: var(--primary-color);
  border: 1px solid #bee3f8;
}

.btn-save:hover {
  background: #bee3f8;
}

.btn-cancel {
  background: #fff5f5;
  color: #e53e3e;
  border: 1px solid #fed7d7;
}

.btn-cancel:hover {
  background: #fed7d7;
}

.btn-delete {
  background: #fff5f5;
  color: #e53e3e;
  border: 1px solid #fed7d7;
}

.btn-delete:hover {
  background: #fed7d7;
}

.no-categories {
  text-align: center;
  padding: 40px 20px;
  color: #a0aec0;
}

.empty-icon {
  font-size: 40px;
  margin-bottom: 15px;
  color: #cbd5e0;
}

.no-categories p {
  margin: 10px 0 0;
  font-size: 15px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .dashboard-wrapper {
    padding: 0 15px;
  }
  
  .dashboard-title {
    font-size: 24px;
  }
  
  .nav-tabs {
    width: 100%;
    justify-content: space-between;
    gap: 5px;
  }
  
  .nav-tabs span {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  .user-card {
    flex-direction: column;
  }
  
  .user-actions {
    flex-direction: row;
    justify-content: flex-end;
    margin-top: 15px;
  }
  
  .cin-verification {
    width: 100%;
  }
  
  .cin-image-container {
    max-width: 100%;
  }
  
  .category-header, .subcategory-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .action-buttons {
    align-self: flex-end;
  }
  
  .input-group {
    flex-direction: column;
  }
  
  .btn-add, .btn-add-small {
    width: 100%;
    justify-content: center;
  }
}

.cin-verification {
  flex: 1;
  min-width: 250px;
}

.cin-verification h5 {
  margin: 0 0 10px 0;
  color: #4a5568;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.cin-verification h5 i {
  color: #4a5568;
}

.cin-image-container {
  max-width: 300px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
  margin-bottom: 10px;
}

.cin-image {
  width: 100%;
  height: auto;
  display: block;
  cursor: pointer;
  transition: transform 0.2s;
}

.cin-image:hover {
  transform: scale(1.02);
}

.verification-status {
  padding: 8px;
  text-align: center;
  font-size: 13px;
  font-weight: 500;
}

.verification-status .verified {
  color: #38a169;
}

.verification-status .unverified {
  color: #dd6b20;
}

.no-cin {
  background: #f8fafc;
  border: 1px dashed #cbd5e0;
  border-radius: 6px;
  padding: 15px;
  text-align: center;
  color: #a0aec0;
}

.no-cin i {
  font-size: 24px;
  margin-bottom: 5px;
  display: block;
}

.no-cin p {
  margin: 5px 0 0;
  font-size: 13px;
}

.verification-actions {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  flex-wrap: wrap;
}

.verification-actions button {
  padding: 6px 10px;
  font-size: 13px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 5px;
  border: none;
  cursor: pointer;
}

.btn-verify {
  background: #f0fff4;
  color: #38a169;
}

.btn-verify:hover {
  background: #c6f6d5;
}

.btn-unverify {
  background: #fff5f5;
  color: #e53e3e;
}

.btn-unverify:hover {
  background: #fed7d7;
}

.btn-request {
  background: #ebf8ff;
  color: #3182ce;
}

.btn-request:hover {
  background: #bee3f8;
}

/* Modal Styles */
.cin-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  max-width: 90%;
  max-height: 90%;
  overflow: auto;
  position: relative;
}

.modal-image {
  max-width: 100%;
  max-height: 80vh;
  display: block;
  margin: 0 auto;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* Disabled button style */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .verification-actions {
    flex-direction: column;
  }
  
  .verification-actions button {
    width: 100%;
    justify-content: center;
  }
  
  .cin-image-container {
    max-width: 100%;
  }
}

.users-header {
  margin-bottom: 20px;
}

.user-filters {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-top: 15px;
  align-items: center;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2%;
}

.filter-group label {
  font-size: 14px;
  color: #4a5568;
  font-weight: 500;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: white;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-select:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
}

.search-input {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  min-width: 200px;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .user-filters {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .filter-group {
    width: 100%;
  }
  
  .filter-select, .search-input {
    width: 100%;
  }
}
</style>