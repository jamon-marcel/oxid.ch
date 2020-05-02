
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
import HomeImagesIndex from '@/components/home/images/Index.vue';

// Projects
import ProjectIndex from '@/components/projects/Index.vue';
import ProjectCreate from '@/components/projects/Create.vue';
import ProjectEdit from '@/components/projects/Edit.vue';
import ProjectGrid from '@/components/projects/grid/Index.vue';

// Discourse
import DiscourseIndex from '@/components/discourses/Index.vue';
import DiscourseCreate from '@/components/discourses/Create.vue';
import DiscourseEdit from '@/components/discourses/Edit.vue';

// Team
import TeamIndex from '@/components/team/team/Index.vue';
import TeamCreate from '@/components/team/team/Create.vue';
import TeamEdit from '@/components/team/team/Edit.vue';
import TeamImagesIndex from '@/components/team/images/Index.vue';

// Jobs
import JobIndex from '@/components/jobs/Index.vue';
import JobCreate from '@/components/jobs/Create.vue';
import JobEdit from '@/components/jobs/Edit.vue';
import JobImagesIndex from '@/components/jobs/images/Index.vue';


// Profile
import ProfileIndex from '@/components/profile/text/Index.vue';
import ProfileCreate from '@/components/profile/text/Create.vue';
import ProfileEdit from '@/components/profile/text/Edit.vue';
import ProfileImagesIndex from '@/components/profile/images/Index.vue';

// Contact
import ContactIndex from '@/components/contact/Index.vue';
import ContactCreate from '@/components/contact/Create.vue';
import ContactEdit from '@/components/contact/Edit.vue';

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
    path: '/admin/home/news',
    component: NewsIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'news-create',
    path: '/admin/home/news/create',
    component: NewsCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'news-edit',
    path: '/admin/home/news/edit/:id',
    component: NewsEdit,
    meta: { requiresAuth: true },
  },

  // Home - Images
  {
    name: 'home-images',
    path: '/admin/home/images',
    component: HomeImagesIndex,
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
  {
    name: 'project-grids',
    path: '/admin/project/grid/:id',
    component: ProjectGrid,
    meta: { requiresAuth: true },
  },

  // Discourse
  {
    name: 'discourses',
    path: '/admin/discourses',
    component: DiscourseIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'discourse-create',
    path: '/admin/discourse/create',
    component: DiscourseCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'discourse-edit',
    path: '/admin/discourse/edit/:id',
    component: DiscourseEdit,
    meta: { requiresAuth: true },
  },

  // Team
  {
    name: 'team',
    path: '/admin/team',
    component: TeamIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'team-create',
    path: '/admin/team/create',
    component: TeamCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'team-edit',
    path: '/admin/team/edit/:id',
    component: TeamEdit,
    meta: { requiresAuth: true },
  },

  // Team - Images
  {
    name: 'team-images',
    path: '/admin/team/images',
    component: TeamImagesIndex,
    meta: { requiresAuth: true },
  },

  // Job
  {
    name: 'jobs',
    path: '/admin/job',
    component: JobIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'job-create',
    path: '/admin/job/create',
    component: JobCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'job-edit',
    path: '/admin/job/edit/:id',
    component: JobEdit,
    meta: { requiresAuth: true },
  },

  // Job - Images
  {
    name: 'job-images',
    path: '/admin/job/images',
    component: JobImagesIndex,
    meta: { requiresAuth: true },
  },

  // Profile
  {
    name: 'profile',
    path: '/admin/profile',
    component: ProfileIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'profile-create',
    path: '/admin/profile/create',
    component: ProfileCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'profile-edit',
    path: '/admin/profile/edit/:id',
    component: ProfileEdit,
    meta: { requiresAuth: true },
  },

  // Profile - Images
  {
    name: 'profile-images',
    path: '/admin/profile/images',
    component: ProfileImagesIndex,
    meta: { requiresAuth: true },
  },

  // Contact
  {
    name: 'contact',
    path: '/admin/contact',
    component: ContactIndex,
    meta: { requiresAuth: true },
  },
  {
    name: 'contact-create',
    path: '/admin/contact/create',
    component: ContactCreate,
    meta: { requiresAuth: true },
  },
  {
    name: 'contact-edit',
    path: '/admin/contact/edit/:id',
    component: ContactEdit,
    meta: { requiresAuth: true },
  },
];

export default routes