<script setup lang="ts">
import { onMounted, ref } from 'vue'
import L from 'leaflet'
import axios from 'axios'

const mapContainer = ref<HTMLElement | null>(null)
let map: L.Map | null = null
let polyline: L.Polyline | null = null
let marker: L.Marker | null = null

const fetchHistoryAndDrawMap = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/pets/1/locations')
    const locations = response.data.data

    if (locations.length === 0) return

    const pathCoordinates = locations
      .map((loc: any) => [parseFloat(loc.latitude), parseFloat(loc.longitude)])
      .reverse()

    const latestPosition = pathCoordinates[pathCoordinates.length - 1]

    map?.setView(latestPosition, 15)

    if (polyline) map?.removeLayer(polyline)
    if (marker) map?.removeLayer(marker)

    polyline = L.polyline(pathCoordinates, { color: '#3498db', weight: 4 }).addTo(map!)

    marker = L.marker(latestPosition)
      .addTo(map!)
      .bindPopup(`<b>Estou aqui!</b><br>Última atualização: ${new Date(locations[0].recorded_at).toLocaleTimeString()}`)
      .openPopup()

  } catch (error) {
    console.error("Erro ao buscar histórico do Pet:", error)
  }
}

onMounted(() => {
  if (!mapContainer.value) return

  map = L.map(mapContainer.value).setView([0, 0], 2)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map)

  fetchHistoryAndDrawMap()
})
</script>

<template>
  <main class="map-wrapper">
    <header class="header">
      <h1>Monitoramento de Pets</h1>
    </header>
    
    <div ref="mapContainer" class="map-container"></div>
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
  box-shadow: 0 4px 6px rgba(0,0,0,0.2);
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