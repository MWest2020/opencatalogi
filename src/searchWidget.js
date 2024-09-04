import Vue from 'vue'
import SearchWidget from './views/widgets/SearchWidget.vue'

OCA.Dashboard.register('opencatalogi_search_widget', async (el, { widget }) => {
	Vue.mixin({ methods: { t, n } })
	const View = Vue.extend(SearchWidget)
	new View({
		propsData: { title: widget.title },
	}).$mount(el)
})
