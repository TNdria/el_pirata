import { ref } from 'vue'
import imageCompression from 'browser-image-compression'

export function useImageCompressor() {
  
    const compressImage = async (file: File) => {
        if (!file) return

        const options = {
            maxSizeMB: 2,
            maxWidthOrHeight: 1024,
            useWebWorker: true,
        }

        try {
            const result = await imageCompression(file, options)

            // Utilisation d'une promesse pour attendre la lecture du fichier
            const base64 = await new Promise<string>((resolve, reject) => {
                const reader = new FileReader()
                reader.onload = (e) => {
                    const base64 = e.target?.result as string
                    resolve(base64)
                }
                reader.onerror = (error) => reject(error)
                reader.readAsDataURL(result)
            })

            return base64
        } catch (error) {
            console.error('Erreur de compression :', error)
            throw error
        }
    }

    return { 
        compressImage,
    }
}
