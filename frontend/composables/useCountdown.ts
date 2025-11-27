import { ref, computed, onMounted, onUnmounted } from "vue";

export const useCountdown = (targetDate: string) => {
    const target = new Date(targetDate).getTime();
    const timeLeft = ref(target - Date.now());
    let interval: any;

    const updateCountdown = () => {
        const now = Date.now();
        const diff = target - now;
        timeLeft.value = diff > 0 ? diff : 0;
    };

    onMounted(() => {
        updateCountdown();
        interval = setInterval(updateCountdown, 1000);
    });

    onUnmounted(() => {
        clearInterval(interval);
    });

    const countdownTimer = computed(() => {
        let diff = timeLeft.value;

        // Nombre total de secondes restantes
        const totalSeconds = Math.floor(diff / 1000);

        // Calcul précis
        const seconds = totalSeconds % 60;
        const totalMinutes = Math.floor(totalSeconds / 60);
        const minutes = totalMinutes % 60;
        const totalHours = Math.floor(totalMinutes / 60);
        const hours = totalHours % 24;
        const totalDays = Math.floor(totalHours / 24);

        // Approximation des mois (1 mois = 30 jours)
        const months = Math.floor(totalDays / 30);
        const days = totalDays % 30;

        const formatNumber = (num: number) => String(num).padStart(2, "0");

        return {
            months: formatNumber(months),
            days: formatNumber(days),
            hours: formatNumber(hours),
            minutes: formatNumber(minutes),
            seconds: formatNumber(seconds)
        };
    });

    return {
        countdownTimer
    };
};
