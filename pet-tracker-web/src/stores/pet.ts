import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// @ts-ignore
window.Pusher = Pusher

export const usePetStore = defineStore('pet', () => {

  const latestBattery = ref<string | number>('--')
  const latestSpeed = ref<string | number>('--')
  const lastUpdated = ref<string>('Carregando...')
  const isOnline = ref<boolean>(false)
  const pathCoordinates = ref<[number, number][]>([])

  const latestPosition = ref<[number, number] | null>(null)

  const echo = new Echo({
    broadcaster: 'reverb',
    key: '34ua31eetafzlxuim70i',
    wsHost: 'localhost',
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
  })

  const fetchLocationHistory = async (petId: number) => {

    // TODO - REFACTOR TO USE JWT TOKEN WHEN I IMPLEMENT LOGIN, BUT CURRENTLY ITS VERY GOOD
    try {
        const response = await axios.get(`http://localhost:8000/api/pets/${petId}/locations`, {
            headers: {
            'X-API-KEY': 'secret_key'
            }
        })
      const locations = response.data.data

      if (locations.length === 0) return

      const currentData = locations[0]
      latestBattery.value = currentData.battery_level ?? '--'
      latestSpeed.value = currentData.speed ?? '--'
      isOnline.value = true

      const dateObj = new Date(currentData.recorded_at)
      lastUpdated.value = dateObj.toLocaleDateString('pt-BR') + ' às ' + dateObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })

      pathCoordinates.value = locations
        .map((loc: any) => [parseFloat(loc.latitude), parseFloat(loc.longitude)])
        .reverse()

      listenForRealTimeUpdates(petId)

    } catch (error) {
      console.error("Erro ao buscar histórico na Store:", error)
      lastUpdated.value = "Erro na conexão"
      isOnline.value = false
    }
  }

  const listenForRealTimeUpdates = (petId: number) => {
    echo.channel(`pet.${petId}`)
      .listen('PetLocationUpdated', (eventData: any) => {
        console.log("Nova coordenada recebida via WebSocket!", eventData)

        latestBattery.value = eventData.battery_level ?? '--'
        latestSpeed.value = eventData.speed ?? '--'
        
        const dateObj = new Date(eventData.recorded_at)
        lastUpdated.value = dateObj.toLocaleDateString('pt-BR') + ' às ' + dateObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })

        const newCoord: [number, number] = [parseFloat(eventData.latitude), parseFloat(eventData.longitude)]
        
        pathCoordinates.value.push(newCoord)
        latestPosition.value = newCoord
      })
  }

  return {
    latestBattery,
    latestSpeed,
    lastUpdated,
    isOnline,
    pathCoordinates,
    fetchLocationHistory
  }
})