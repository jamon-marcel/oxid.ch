import { createRouter, createWebHistory } from 'vue-router';
import http from '@/lib/http';

// Forms get type 'create' or 'edit' as a prop
const create = { type: 'create' };
const edit = { type: 'edit' };

// Screens not yet moved to views/ (<script setup>) still use their
// Create/Edit wrappers in components/
const routes = [
  { path: '/', redirect: { name: 'login' } },
  { name: 'admin', path: '/admin', redirect: { name: 'dashboard' } },
  { name: 'dashboard', path: '/admin/dashboard', component: () => import('@/views/dashboard/Index.vue') },
  { name: 'login', path: '/admin/login', component: () => import('@/views/auth/Login.vue'), meta: { guest: true } },
  { name: 'logout', path: '/admin/logout', component: () => import('@/views/auth/Logout.vue'), meta: { guest: true } },

  // Home
  { name: 'news', path: '/admin/home/news', component: () => import('@/views/news/Index.vue') },
  { name: 'news-create', path: '/admin/home/news/create', component: () => import('@/views/news/Form.vue'), props: create },
  { name: 'news-edit', path: '/admin/home/news/edit/:id', component: () => import('@/views/news/Form.vue'), props: edit },
  { name: 'home-images', path: '/admin/home/images', component: () => import('@/components/home/images/Index.vue') },

  // Projects
  { name: 'projects', path: '/admin/projects', component: () => import('@/components/projects/Index.vue') },
  { name: 'project-create', path: '/admin/project/create', component: () => import('@/components/projects/Create.vue') },
  { name: 'project-edit', path: '/admin/project/edit/:id', component: () => import('@/components/projects/Edit.vue') },
  { name: 'project-grids', path: '/admin/project/grid/:id', component: () => import('@/components/projects/grid/Index.vue') },

  // Discourse
  { name: 'discourses', path: '/admin/discourses', component: () => import('@/components/discourses/Index.vue') },
  { name: 'discourse-create', path: '/admin/discourse/create', component: () => import('@/components/discourses/Create.vue') },
  { name: 'discourse-edit', path: '/admin/discourse/edit/:id', component: () => import('@/components/discourses/Edit.vue') },

  // Team
  { name: 'team', path: '/admin/team', component: () => import('@/components/team/team/Index.vue') },
  { name: 'team-create', path: '/admin/team/create', component: () => import('@/components/team/team/Create.vue') },
  { name: 'team-edit', path: '/admin/team/edit/:id', component: () => import('@/components/team/team/Edit.vue') },
  { name: 'team-images', path: '/admin/team/images', component: () => import('@/components/team/images/Index.vue') },

  // Jobs
  { name: 'jobs', path: '/admin/job', component: () => import('@/components/jobs/Index.vue') },
  { name: 'job-create', path: '/admin/job/create', component: () => import('@/components/jobs/Create.vue') },
  { name: 'job-edit', path: '/admin/job/edit/:id', component: () => import('@/components/jobs/Edit.vue') },
  { name: 'job-images', path: '/admin/job/images', component: () => import('@/components/jobs/images/Index.vue') },

  // Profile
  { name: 'profile', path: '/admin/profile', component: () => import('@/components/profile/text/Index.vue') },
  { name: 'profile-create', path: '/admin/profile/create', component: () => import('@/components/profile/text/Create.vue') },
  { name: 'profile-edit', path: '/admin/profile/edit/:id', component: () => import('@/components/profile/text/Edit.vue') },
  { name: 'profile-images', path: '/admin/profile/images', component: () => import('@/components/profile/images/Index.vue') },

  // Contact
  { name: 'contact', path: '/admin/contact', component: () => import('@/components/contact/Index.vue') },
  { name: 'contact-create', path: '/admin/contact/create', component: () => import('@/components/contact/Create.vue') },
  { name: 'contact-edit', path: '/admin/contact/edit/:id', component: () => import('@/components/contact/Edit.vue') },
];

const router = createRouter({ history: createWebHistory(), routes });

// Every screen but login/logout needs a live session; a logged-in user
// skips the login screen
router.beforeEach(async (to) => {
  if (to.name === 'logout') {
    return true;
  }
  try {
    await http.post('/api/auth/me');
    return to.name === 'login' ? { name: 'dashboard' } : true;
  }
  catch {
    return to.meta.guest ? true : { name: 'login' };
  }
});

export default router;
