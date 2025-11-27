// composables/useImageFallback.ts
export function useImageFallback(defaultSrc = '/images/users.png') {
    const handleImageError = (event: Event) => {
        const target = event.target as HTMLImageElement;
        if (!target.dataset.errorHandled) {
            target.dataset.errorHandled = 'true'; // éviter boucle infinie
            target.src = defaultSrc;
        }
    };

    return {
        handleImageError,
    };
}