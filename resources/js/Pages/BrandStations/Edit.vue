<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('brand-stations.index')">Brand Stations</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="station.deleted_at" class="mb-6" @restore="restore">
      This station has been deleted.
    </trashed-message>
    <div class="bg-white rounded-md shadow overflow-hidden w-full h-full">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.stream_url" :error="form.errors.stream_url" class="pr-6 pb-8 w-full lg:w-1/2" label="Stream url" />
          <text-input v-model="form.image_url" :error="form.errors.image_url" class="pr-6 pb-8 w-full lg:w-1/2" label="Image url" />
          <text-input v-model="form.artwork_image" :error="form.errors.artwork_image" class="pr-6 pb-8 w-full lg:w-1/2" label="Artwork url" />
          <text-input v-model="form.description" :error="form.errors.description" class="pr-6 pb-8 w-full lg:w-1/1" label="Description" />
          <textarea-input v-model="form.long_description" :error="form.errors.long_description" class="pr-6 pb-8 w-full lg:w-1/1" label="Long Description" />
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!station.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Brand Station</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update Brand Station</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextareaInput from '@/Shared/TextareaInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    LoadingButton,
    TextInput,
    TextareaInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    station: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.station.name,
        stream_url: this.station.stream_url,
        image_url: this.station.image_url,
        artwork_image: this.station.artwork_image,
        description: this.station.description,
        long_description: this.station.long_description,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('brand-stations.update', this.station.id))
    },
    destroy() {
      if (confirm('Are you sure you want to delete this brand station?')) {
        this.$inertia.delete(this.route('brand-stations.destroy', this.station.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this station?')) {
        this.$inertia.put(this.route('stations.restore', this.station.id))
      }
    },
  },
}
</script>
