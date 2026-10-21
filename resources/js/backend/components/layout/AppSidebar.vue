<template>
  <aside class="fixed top-0 bottom-0 left-0 z-30 flex w-240 flex-col bg-gray-100 dark:bg-warm-950">
    <div class="px-24 pt-24 pb-24 text-gray-900 dark:text-warm-100">
      <router-link :to="{ name: 'news' }" aria-label="Oxid Administration" class="block w-80">
        <OxidLogo />
      </router-link>
    </div>

    <nav class="flex-1 overflow-y-auto px-16 py-16" aria-label="Hauptmenü">
      <div class="space-y-24">
        <div v-for="(group, index) in navigation" :key="index">
          <p v-if="group.title" class="mb-6 px-12 text-[0.6875rem] tracking-[0.08em] text-gray-400 uppercase dark:text-warm-500">{{ group.title }}</p>
          <ul role="list">
            <li v-for="item in group.items" :key="item.route">
              <router-link
                :to="{ name: item.route }"
                :aria-current="isActive(item) ? 'page' : undefined"
                class="flex items-center gap-12 rounded-md px-12 py-10 text-sm transition-colors duration-150 focus-visible:ring-2 focus-visible:ring-gray-200 focus-visible:outline-none dark:focus-visible:ring-warm-700"
                :class="isActive(item)
                  ? 'text-gray-900 dark:text-warm-100'
                  : 'text-gray-400 hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100'"
              >
                <component :is="item.icon" :size="18" weight="light" />
                <span>{{ item.name }}</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="px-12 pb-24">
      <router-link
        :to="{ name: 'logout' }"
        class="flex w-full items-center gap-8 rounded-md px-12 py-10 text-xs text-gray-400 transition-colors duration-150 hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-200 focus-visible:outline-none dark:text-warm-500 dark:hover:text-warm-100 dark:focus-visible:ring-warm-700"
      >
        <PhSignOut :size="18" weight="light" />
        <span>Abmelden</span>
      </router-link>
    </div>
  </aside>
</template>
<script setup>
import { useRoute } from 'vue-router';
import {
  PhNewspaper, PhBuildings, PhChatsCircle, PhUsers, PhBriefcase, PhIdentificationCard,
  PhAddressBook, PhImages, PhUserGear, PhSignOut,
} from '@phosphor-icons/vue';
import OxidLogo from '@/components/layout/OxidLogo.vue';

// An item is active on its list and on the pages below it: names starting
// with one of its prefixes ('project' covers project-create/-edit)
const navigation = [
  {
    items: [
      { name: 'News', route: 'news', match: ['news'], icon: PhNewspaper },
      { name: 'Projekte', route: 'projects', match: ['project'], icon: PhBuildings },
      { name: 'Diskurs', route: 'discourses', match: ['discourse'], icon: PhChatsCircle },
    ],
  },
  {
    title: 'Büro',
    items: [
      { name: 'Team', route: 'team', match: ['team', 'team-create', 'team-edit'], exact: true, icon: PhUsers },
      { name: 'Jobs', route: 'jobs', match: ['jobs', 'job-create', 'job-edit'], exact: true, icon: PhBriefcase },
      { name: 'Profil', route: 'profile', match: ['profile', 'profile-create', 'profile-edit'], exact: true, icon: PhIdentificationCard },
      { name: 'Kontakt', route: 'contact', match: ['contact'], icon: PhAddressBook },
    ],
  },
  {
    title: 'Bilder',
    items: [
      { name: 'Startseite', route: 'home-images', match: ['home-images'], icon: PhImages },
      { name: 'Team', route: 'team-images', match: ['team-images'], icon: PhImages },
      { name: 'Jobs', route: 'job-images', match: ['job-images'], icon: PhImages },
      { name: 'Profil', route: 'profile-images', match: ['profile-images'], icon: PhImages },
    ],
  },
  {
    title: 'System',
    items: [
      { name: 'Benutzer', route: 'users', match: ['user'], icon: PhUserGear },
    ],
  },
];

const route = useRoute();

function isActive(item) {
  const name = String(route.name ?? '');
  return item.exact ? item.match.includes(name) : item.match.some(prefix => name.startsWith(prefix));
}
</script>
