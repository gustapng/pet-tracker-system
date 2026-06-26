<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import L from 'leaflet'
import axios from 'axios'
import { usePetStore } from './stores/pet'
import { storeToRefs } from 'pinia'

const mapContainer = ref<HTMLElement | null>(null)
let map: L.Map | null = null
let polyline: L.Polyline | null = null
let marker: L.Marker | null = null

const petStore = usePetStore()

const { latestPosition } = storeToRefs(petStore)

const initializeMapAndData = async () => {
  await petStore.fetchLocationHistory(1)

  if (petStore.pathCoordinates.length === 0) return

  const initialPosition = petStore.pathCoordinates[petStore.pathCoordinates.length - 1]

  map?.setView(initialPosition, 15)

  polyline = L.polyline(petStore.pathCoordinates, { color: '#3498db', weight: 4 }).addTo(map!)

  marker = L.marker(initialPosition)
    .addTo(map!)
    .bindPopup(`<b>Estou aqui!</b><br>Atualizado: ${petStore.lastUpdated}`)
    .openPopup()
}

watch(latestPosition, (newPosition) => {
  console.log("📍 Observador disparou! Nova posição:", newPosition)

  if (!newPosition || !map) return

  if (!marker) {
    marker = L.marker(newPosition)
      .addTo(map)
      .bindPopup(`<b>Estou aqui!</b><br>Atualizado: ${petStore.lastUpdated}`)
      .openPopup()
  } else {
    marker.setLatLng(newPosition)
    marker.getPopup()?.setContent(`<b>Estou aqui!</b><br>Atualizado: ${petStore.lastUpdated}`)
  }

  if (!polyline) {
    polyline = L.polyline([newPosition], { color: '#3498db', weight: 4 }).addTo(map)
  } else {
    polyline.addLatLng(newPosition)
  }

  map.flyTo(newPosition, 16, { animate: true, duration: 1.5 })
}, { deep: true })

onMounted(() => {
  if (!mapContainer.value) return

  map = L.map(mapContainer.value).setView([0, 0], 2)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map)

  initializeMapAndData()
})
</script>

<template>
  <main class="flex h-screen w-screen bg-gray-100 overflow-hidden">
    
    <aside class="w-72 text-black flex flex-col shadow-xl z-[1000]">
      <header class="p-6 border-b border-slate-300">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-full bg-slate-600 flex items-center justify-center text-xl font-bold text-white">
            R
          </div>
          <div>
            <h1 class="text-xl font-bold tracking-tight">Rex</h1>
            <div class="flex items-center gap-2 mt-1">
              <span class="relative flex h-2.5 w-2.5">
                <span v-if="petStore.isOnline" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="petStore.isOnline ? 'bg-emerald-500' : 'bg-red-500'"></span>
              </span>
              <span class="text-xs text-slate-400 uppercase tracking-wider">
                {{ petStore.isOnline ? 'Sinal Ativo' : 'Offline' }}
              </span>
            </div>
          </div>
        </div>
      </header>

      <nav class="flex-1 overflow-y-auto p-4 space-y-6">
        
        <div>
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">{{ petStore.isOnline ? 'Status em Tempo Real' : 'Último registro' }}</h2>
          <div class="space-y-2">
            <div class="bg-slate-200/50 p-3 rounded-lg flex justify-between items-center border border-slate-600/50">
              <span class="text-sm text-slate-400">Bateria</span>
              <span class="font-mono font-medium text-emerald-400">{{ petStore.latestBattery }}%</span>
            </div>
            <div class="bg-slate-200/50 p-3 rounded-lg flex justify-between items-center border border-slate-600/50">
              <span class="text-sm text-slate-400">Velocidade</span>
              <span class="font-mono font-medium text-amber-400">{{ petStore.latestSpeed }} km/h</span>
            </div>
            <div class="bg-slate-200/50 p-3 rounded-lg flex flex-col border border-slate-600/50">
              <span class="text-sm text-slate-400 mb-1">Última atualização</span>
              <span class="text-xs text-slate-600">{{ petStore.lastUpdated }}</span>
            </div>
          </div>
        </div>

        <!-- TODO - IN FUTURE IMPLEMENTS FILTER OPTIONS -->
      </nav>
    </aside>

    <div class="flex-1 relative">
      <div ref="mapContainer" class="absolute inset-0 z-0"></div>
    </div>

  </main>
</template>

<style scoped>
:global(body) {
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.map-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100vw;
  background-color: #f8f9fa;
}

.header {
  padding: 1rem;
  background-color: #2c3e50;
  color: #ecf0f1;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  z-index: 1000;
}

.header h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.map-container {
  flex: 1;
  width: 100%;
}
</style>