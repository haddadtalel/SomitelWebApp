/**
 * Notification System
 */
class NotificationSystem {
    constructor() {
        this.audioContext = null;
        this.notificationBuffer = null;
        this.initialized = false;
    }

    /**
     * Initialize the notification system
     */
    async initialize() {
        if (this.initialized) return;
        
        try {
            // Create audio context
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            this.audioContext = new AudioContext();
            
            // Load notification sound
            const response = await fetch('/BaseStyle/sounds/notification.mp3');
            const arrayBuffer = await response.arrayBuffer();
            this.notificationBuffer = await this.audioContext.decodeAudioData(arrayBuffer);
            
            this.initialized = true;
            console.log('Notification system initialized');
        } catch (error) {
            console.error('Failed to initialize notification system:', error);
        }
    }

    /**
     * Play notification sound
     */
    playNotification() {
        if (!this.initialized || !this.audioContext || !this.notificationBuffer) {
            console.warn('Notification system not initialized');
            return;
        }
        
        try {
            const source = this.audioContext.createBufferSource();
            source.buffer = this.notificationBuffer;
            source.connect(this.audioContext.destination);
            source.start(0);
        } catch (error) {
            console.error('Failed to play notification sound:', error);
        }
    }
}

// Create global notification system
window.notificationSystem = new NotificationSystem();

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Initialize with user interaction to comply with autoplay policies
    document.body.addEventListener('click', () => {
        if (!window.notificationSystem.initialized) {
            window.notificationSystem.initialize();
        }
    }, { once: true });
});
