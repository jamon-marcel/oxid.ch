<template>
	<div>
		<header class="site-header">
			<div>
				<a href="/admin/" class="brand">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 230.9 88.3"><path style="fill:#181716" d="M221.8 58.2c0 11.6-8 20.8-19.6 20.8s-19.6-9.2-19.6-20.8 8-20.8 19.6-20.8 19.6 9.2 19.6 20.8m-49.2 0c0 16.4 12 29.9 28.1 29.9 8.7 0 16.5-4.5 20.3-10v8.8h9.9V.7H221v37.6c-3.8-5.5-11.6-10-20.3-10-16.1 0-28.1 13.4-28.1 29.9M153 86.9h9.9V29.4H153zM165 7a6.9 6.9 0 0 0-6.9-7 7.2 7.2 0 0 0-7.2 7 7.1 7.1 0 0 0 7.2 7 6.9 6.9 0 0 0 6.9-7M85.8 86.9h12.3l17.5-21.7 17.5 21.7h12.2l-23.5-29.1 21.6-28.4h-12.1l-15.7 20.7-15.9-20.7H87.8l21.6 28.4zm-75-40.8C10.8 28.2 24.4 14 42.2 14s31.5 14.2 31.5 32.1-13.6 32.1-31.5 32.1-31.4-14.1-31.4-32.1m73.6 0A41.8 41.8 0 0 0 42.2 3.9 41.8 41.8 0 0 0 0 46.1a41.8 41.8 0 0 0 42.2 42.2 41.8 41.8 0 0 0 42.2-42.2"/></svg>
				</a>
				<a href="javascript:;" @click="toggleMenu()" class="icon-menu"></a>
			</div>
		</header>
		<nav class="site-nav" :class="{'is-visible': menuVisible}">
			<div>
				<a href="javascript:;" @click="toggleMenu()" class="icon-close"></a>
			</div>
			<ul>
				<li>
					<span>Home</span>
					<ul>
						<li>
							<router-link :to="{ name: 'news' }">News</router-link>
						</li>
						<li>
							<router-link :to="{ name: 'home-images' }">Bilder</router-link>
						</li>
					</ul>
				</li>
				<li>
					<router-link :to="{ name: 'projects' }">Projekte</router-link>
				</li>
				<li>
					<router-link :to="{ name: 'discourses' }">Diskurs</router-link>
				</li>
				<li>
					<span>Team</span>
					<ul>
						<li>
							<router-link :to="{ name: 'team' }">Mitarbeiter</router-link>
						</li>
						<li>
							<router-link :to="{ name: 'team-images' }">Bilder</router-link>
						</li>
					</ul>
				</li>
				<li>
					<span>Jobs</span>
					<ul>
						<li>
							<router-link :to="{ name: 'jobs' }">Inserate</router-link>
						</li>
						<li>
							<router-link :to="{ name: 'job-images' }">Bilder</router-link>
						</li>
					</ul>
				</li>
				<li>
					<span>Profil</span>
					<ul>
						<li>
							<router-link :to="{ name: 'profile' }">Text</router-link>
						</li>
						<li>
							<router-link :to="{ name: 'profile-images' }">Bilder</router-link>
						</li>
					</ul>
				</li>
				<li>
					<router-link :to="{ name: 'contact' }">Kontakt</router-link>
				</li>
			</ul>
			<router-link :to="{ name: 'logout' }" class="icon-logout">Logout</router-link>
		</nav>
	</div>
</template>
<script>
export default {
	data() {
		return {
			menuVisible: false
		}
	},
	methods: {
		toggleMenu() {
			this.menuVisible = !this.menuVisible;
		}
	}
}
</script>