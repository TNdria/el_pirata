<template>
    <div>
        <video ref="videoRef" class="video-player">
            <source :src="urlVideo" type="video/mp4" />
        </video>

        <div class="pl-6 video-controls d-flex flex grap-2   ">
            <button @click="rewind" class="video-ctrl control-btn">
                <i class="fas fa-backward"></i>
            </button>
            <button @click="togglePlayPause" class="control-btn play-btn">
                <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'"></i>
            </button>
            <button @click="forward" class="control-btn">
                <i class="fas fa-forward"></i>
            </button>
            <!-- <button @click="toggleFullScreen" class="control-btn fullscreen-btn">
                <i class="fas fa-expand"></i>
            </button> -->
            <div class="d-flex flex volume-control">
                <button @click="toggleMute" class="control-btn ml-2">
                    <i :class="isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up'"></i>
                </button>
                <input type="range" min="0" max="1" step="0.1" v-model="volume" @input="adjustVolume" />
            </div>
            <input type="range" min="0" :max="duration" step="0.1" v-model="currentTime" @input="seekVideo"
                class="progress-bar" />

        </div>
    </div>
</template>

<script>
export default {
    props: ['urlVideo'],
    data() {
        return {
            isPlaying: false,
            volume: 1,
            isMuted: false,
            currentTime: 0,
            duration: 0, // Ajouter la propriété duration manquante
        }
    },
    methods: {
        forward() {
            const video = this.$refs.videoRef
            if (video) {
                video.currentTime += 10
            }
        },
        rewind() {
            const video = this.$refs.videoRef
            if (video) {
                video.currentTime -= 10
            }
        },
        togglePlayPause() {
            const video = this.$refs.videoRef
            if (video.paused) {
                video.play()
                this.isPlaying = true
            } else {
                video.pause()
                this.isPlaying = false
            }
        },
        // toggleFullScreen() {
        //     const video = this.$refs.videoRef
        //     if (video.requestFullscreen) {
        //         video.requestFullscreen()
        //     } else if (video.mozRequestFullScreen) { // Firefox
        //         video.mozRequestFullScreen()
        //     } else if (video.webkitRequestFullscreen) { // Chrome, Safari and Opera
        //         video.webkitRequestFullscreen()
        //     } else if (video.msRequestFullscreen) { // IE/Edge
        //         video.msRequestFullscreen()
        //     }
        // },
        adjustVolume() {
            this.$refs.videoRef.volume = this.volume
        },
        seekVideo() {
            this.$refs.videoRef.currentTime = this.currentTime
        },
        toggleMute() {
            const video = this.$refs.videoRef
            if (video) {
                this.isMuted = !this.isMuted
                video.muted = this.isMuted
            }
        }
    },
    mounted() {
        const video = this.$refs.videoRef
        // Quand la vidéo est chargée, on récupère sa durée
        video.onloadedmetadata = () => {
            this.duration = video.duration
        }

        // Mise à jour en temps réel de la progress bar
        video.ontimeupdate = () => {
            this.currentTime = video.currentTime
        }
    }
}
</script>

<style scoped>
.video-player {
    width: 100%;
    border-radius: 8px;
    display: block;
    background-color: #000;
}

.video-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    z-index: 10;
    transition: opacity 0.3s;
}

.control-btn {
    color: white;
    background: transparent;
    border: none;
    padding: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: transform 0.2s;
}

.control-btn:hover {
    transform: scale(1.2);
    color: #ff0000;
}

.volume-control input[type="range"] {
    margin-left: 8px;
    accent-color: red;
    width: 100px;
}

.video-container {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: auto;
    background: black;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
}

.play-btn i {
    font-size: 22px;
}

.fullscreen-btn i {
    font-size: 18px;
}

.progress-bar {
    width: 100%;
    appearance: none;
    height: 4px;
    background: red;
    border-radius: 2px;
    margin-bottom: 5px;
    width: 60%;
    margin: 3px;
}
</style>