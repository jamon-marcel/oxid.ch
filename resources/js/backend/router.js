import { createRouter, createWebHistory } from 'vue-router';
import http from '@/lib/http';

// Forms get type 'create' or 'edit' as a prop
const create = { type: 'create' };
const edit = { type: 'edit' };

// Home, team, jobs and profile images share one page
const images = () => import('@/views/images/Index.vue');

const routes = [
  { path: '/', redirect: { name: 'login' } },
  { name: 'admin', path: '/admin', redirect: { name: 'news' } },
  { path: '/admin/dashboard', redirect: { name: 'news' } },
  { name: 'login', path: '/admin/login', component: () => import('@/views/auth/Login.vue'), meta: { guest: true } },
  { name: 'logout', path: '/admin/logout', component: () => import('@/views/auth/Logout.vue'), meta: { guest: true } },

  // Home
  { name: 'news', path: '/admin/home/news', component: () => import('@/views/news/Index.vue') },
  { name: 'news-create', path: '/admin/home/news/create', component: () => import('@/views/news/Form.vue'), props: create },
  { name: 'news-edit', path: '/admin/home/news/edit/:id', component: () => import('@/views/news/Form.vue'), props: edit },
  { name: 'home-images', path: '/admin/home/images', component: images, props: { title: 'Bilder Startseite', endpoint: 'home', sortable: false, ratio: 3 / 4 } },

  // Projects
  { name: 'projects', path: '/admin/projects', component: () => import('@/views/projects/Index.vue') },
  { name: 'project-create', path: '/admin/project/create', component: () => import('@/views/projects/Form.vue'), props: create },
  { name: 'project-edit', path: '/admin/project/edit/:id', component: () => import('@/views/projects/Form.vue'), props: edit },
  // The layout is a tab of the project form now
  { name: 'project-grids', path: '/admin/project/grid/:id', redirect: to => ({ name: 'project-edit', params: { id: to.params.id }, query: { tab: 'grid' } }) },

  // Discourse
  { name: 'discourses', path: '/admin/discourses', component: () => import('@/views/discourses/Index.vue') },
  { name: 'discourse-create', path: '/admin/discourse/create', component: () => import('@/views/discourses/Form.vue'), props: create },
  { name: 'discourse-edit', path: '/admin/discourse/edit/:id', component: () => import('@/views/discourses/Form.vue'), props: edit },

  // Team
  { name: 'team', path: '/admin/team', component: () => import('@/views/team/Index.vue') },
  { name: 'team-create', path: '/admin/team/create', component: () => import('@/views/team/Form.vue'), props: create },
  { name: 'team-edit', path: '/admin/team/edit/:id', component: () => import('@/views/team/Form.vue'), props: edit },
  { name: 'team-images', path: '/admin/team/images', component: images, props: { title: 'Bilder Team', endpoint: 'team' } },

  // Jobs
  { name: 'jobs', path: '/admin/job', component: () => import('@/views/jobs/Index.vue') },
  { name: 'job-create', path: '/admin/job/create', component: () => import('@/views/jobs/Form.vue'), props: create },
  { name: 'job-edit', path: '/admin/job/edit/:id', component: () => import('@/views/jobs/Form.vue'), props: edit },
  { name: 'job-images', path: '/admin/job/images', component: images, props: { title: 'Bilder Jobs', endpoint: 'job' } },

  // Profile
  { name: 'profile', path: '/admin/profile', component: () => import('@/views/profile/Index.vue') },
  { name: 'profile-create', path: '/admin/profile/create', component: () => import('@/views/profile/Form.vue'), props: create },
  { name: 'profile-edit', path: '/admin/profile/edit/:id', component: () => import('@/views/profile/Form.vue'), props: edit },
  { name: 'profile-images', path: '/admin/profile/images', component: images, props: { title: 'Bilder Profil', endpoint: 'profile' } },

  // Contact
  { name: 'contact', path: '/admin/contact', component: () => import('@/views/contact/Index.vue') },
  { name: 'contact-create', path: '/admin/contact/create', component: () => import('@/views/contact/Form.vue'), props: create },
  { name: 'contact-edit', path: '/admin/contact/edit/:id', component: () => import('@/views/contact/Form.vue'), props: edit },

  // Users (admin logins)
  { name: 'users', path: '/admin/users', component: () => import('@/views/users/Index.vue') },
  { name: 'user-create', path: '/admin/user/create', component: () => import('@/views/users/Form.vue'), props: create },
  { name: 'user-edit', path: '/admin/user/edit/:id', component: () => import('@/views/users/Form.vue'), props: edit },
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
    return to.name === 'login' ? { name: 'news' } : true;
  }
  catch {
    return to.meta.guest ? true : { name: 'login' };
  }
});

export default router;
