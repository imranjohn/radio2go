<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Stations</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <!-- <label class="block text-gray-700">Trashed:</label> -->
        <!-- <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select> -->
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('stations.create')">
        <span>Create</span>
        <span class="hidden md:inline">Stations</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4 w-64">Station Name</th>
          <th class="px-6 pt-6 pb-4 w-44">Station Logo</th>
          <!-- <th class="px-6 pt-6 pb-4">Station Url</th> -->
          <th class="px pt-6 pb-4">Description</th>
          <th class="px-6 pt-6 pb-4">Html</th>
          <th class="px-6 pt-6 pb-4">Action</th>
        </tr>
        <tr v-for="station in stations.data" :key="station.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('stations.edit', station.id)">
              {{ station.name }}
              <icon v-if="station.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('stations.edit', station.id)" tabindex="-1">
              <img :src="station.image_url" style="height: 50px" />
            </inertia-link>
          </td>
          <!-- <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('stations.edit', station.id)" tabindex="-1">
              {{ station.stream_url !== null ? station.stream_url.substring(0, 30) : "" }}
            </inertia-link>
          </td> -->
          <td class="border-t">
            {{ station.description !== null ? station.description.substring(0, 50) : "" }}
          </td>
           <td class="border-t text-sm">
            <a :href="route('open.html', station.id)" target="_blank" class="float-left	ml-7 ">Open / </a><a :href="route('create.html', station.id)" target="_blank" class=" float-left	"> Download</a>
          </td>
          <td class="border-t">
            <inertia-link class=" ml-7 float-left	" :href="route('stations.edit', station.id)" tabindex="-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </inertia-link>
            <button v-if="!station.deleted_at" class="" tabindex="-1" type="button" @click="destroy(station.id)"> 
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </td>
        </tr>
        <tr v-if="stations.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No stations found.</td>
        </tr>
      </table>
    </div>
    <pagination class="mt-6" :links="stations.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'stations' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    stations: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('stations.index'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    destroy(id) {
      if (confirm('Are you sure you want to delete this station?')) {
        this.$inertia.delete(this.route('stations.destroy', id))
      }
    },
  },
}
</script>
