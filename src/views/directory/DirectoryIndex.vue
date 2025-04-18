<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Directory
		</h2>
		<DirectoryList />
	</NcAppContent>
</template>

<script>
import { NcAppContent } from '@nextcloud/vue'
import DirectoryList from './DirectoryList.vue'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		DirectoryList,
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>
