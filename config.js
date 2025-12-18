// Configuration file for Legend Shop
const CONFIG = {
    // Google OAuth Configuration
    GOOGLE_CLIENT_ID: '674654993812-krpej9648d2205dqpls1dsq7tuhvlbft.apps.googleusercontent.com',
    GOOGLE_CLIENT_SECRET: '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',
    
    // Background rotation settings (in milliseconds)
    BACKGROUND_ROTATION_INTERVAL: 3 * 24 * 60 * 60 * 1000, // 3 days
    
    // Anime 8K Wallpaper URLs (curated collection)
    ANIME_WALLPAPERS: [
        'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=3840',
        'https://images.unsplash.com/photo-1613376023733-0a73315d9b06?q=80&w=3840',
        'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?q=80&w=3840',
        'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=3840',
        'https://images.unsplash.com/photo-1613332725672-8fc7da5d2912?q=80&w=3840'
    ]
};

// Make CONFIG available globally
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CONFIG;
}
