<template>
  <div class="w-60">
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Dashboard</div>
      </inertia-link>
    </div>
    <div v-if="$page.props.auth.user.owner" class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('stations.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('stations') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('stations') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Stations</div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('brand-stations.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('brand-stations') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('brand-stations') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Brand Stations</div>
      </inertia-link>
    </div>
    <div v-if="$page.props.auth.user.owner" class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('users')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('users') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('users') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Users</div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter(url => currentUrl.startsWith(url)).length
    },
  },
}
</script>
