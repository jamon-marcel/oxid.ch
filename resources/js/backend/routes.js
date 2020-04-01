
// Auth 
import LoginComponent from '@/components/auth/LoginComponent.vue';
import LogoutComponent from '@/components/auth/LogoutComponent.vue';

// Page
import PageComponent from '@/layout/Page.vue';

// Home - News
import NewsIndex from '@/components/home/news/Index.vue';
import NewsCreate from '@/components/home/news/Create.vue';
import NewsEdit from '@/components/home/news/Edit.vue';

// Home - Images
import ImagesIndex from '@/components/home/images/Index.vue';

// Home - News
import ProjectIndex from '@/components/projects/Index.vue';
import ProjectCreate from '@/components/projects/Create.vue';
import ProjectEdit from '@/components/projects/Edit.vue';

const routes = [
  {
    path: '/',
    redirect: { name: 'login' }
  },
  {
    path: '/admin',
    name: 'admin',
    component: PageComponent,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/dashboard',
    name: 'dashboard',
    component: PageComponent,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/login',
    name: 'login',
    component: LoginComponent
  },
  {
    path: '/admin/logout',
    name: 'logout',
    component: LogoutComponent
  },

  // Home - News
  {
    name: 'news',
    path: '/admin/news',
    component: NewsIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'news-create',
    path: '/admin/news/create',
    component: NewsCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'news-edit',
    path: '/admin/news/edit/:id',
    component: NewsEdit,
    meta: { requiresAuth: true },
  },

  // Home - Images
  {
    name: 'images',
    path: '/admin/images',
    component: ImagesIndex,
    meta: { requiresAuth: true },
  },

  // Projects
  {
    name: 'projects',
    path: '/admin/projects',
    component: ProjectIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'project-create',
    path: '/admin/project/create',
    component: ProjectCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'project-edit',
    path: '/admin/project/edit/:id',
    component: ProjectEdit,
    meta: { requiresAuth: true },
  },
];

export default routes