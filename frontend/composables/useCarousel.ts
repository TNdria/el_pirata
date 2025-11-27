import { ref, onMounted } from 'vue'

export const useCarousel = () => {
  const isClient = ref(false)
  
  onMounted(() => {
    isClient.value = true
  })
  
  return {
    isClient
  }
}

