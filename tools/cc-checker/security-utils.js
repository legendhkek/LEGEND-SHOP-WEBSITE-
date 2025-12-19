/**
 * Advanced Security Utilities
 * Additional security functions for enhanced protection
 */

// Anti-Bot Detection
class BotDetector {
    constructor() {
        this.score = 0;
        this.checks = [];
    }

    // Check if user is likely a bot
    async isBot() {
        await this.runChecks();
        return this.score > 3; // Threshold for bot detection
    }

    async runChecks() {
        this.checkWebDriver();
        this.checkPlugins();
        this.checkLanguages();
        this.checkUserAgent();
        this.checkCanvas();
        this.checkTiming();
    }

    checkWebDriver() {
        if (navigator.webdriver) {
            this.score += 2;
            this.checks.push('webdriver_detected');
        }
    }

    checkPlugins() {
        if (navigator.plugins.length === 0) {
            this.score += 1;
            this.checks.push('no_plugins');
        }
    }

    checkLanguages() {
        if (!navigator.languages || navigator.languages.length === 0) {
            this.score += 1;
            this.checks.push('no_languages');
        }
    }

    checkUserAgent() {
        const botPatterns = [
            /bot/i, /crawler/i, /spider/i, /curl/i, /wget/i,
            /headless/i, /phantom/i, /selenium/i, /puppeteer/i
        ];
        
        const ua = navigator.userAgent;
        if (botPatterns.some(pattern => pattern.test(ua))) {
            this.score += 2;
            this.checks.push('suspicious_ua');
        }
    }

    checkCanvas() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.fillText('Bot Check', 2, 2);
            const dataURL = canvas.toDataURL();
            
            // Check for default/blank canvas (common in headless browsers)
            if (dataURL === 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==') {
                this.score += 2;
                this.checks.push('canvas_fingerprint_failed');
            }
        } catch (e) {
            this.score += 1;
            this.checks.push('canvas_blocked');
        }
    }

    checkTiming() {
        const start = performance.now();
        for (let i = 0; i < 1000000; i++) {} // Busy work
        const end = performance.now();
        
        // Suspiciously fast execution (possible bot)
        if (end - start < 1) {
            this.score += 1;
            this.checks.push('timing_anomaly');
        }
    }

    getReport() {
        return {
            score: this.score,
            isBot: this.score > 3,
            checks: this.checks
        };
    }
}

// Advanced Input Validation
class InputValidator {
    static validateCardNumber(number) {
        // Remove spaces and dashes
        number = number.replace(/[\s-]/g, '');
        
        // Check if only digits
        if (!/^\d+$/.test(number)) {
            return { valid: false, error: 'Card number must contain only digits' };
        }
        
        // Check length
        if (number.length < 13 || number.length > 19) {
            return { valid: false, error: 'Invalid card number length' };
        }
        
        // Luhn algorithm
        let sum = 0;
        let isEven = false;
        
        for (let i = number.length - 1; i >= 0; i--) {
            let digit = parseInt(number[i]);
            
            if (isEven) {
                digit *= 2;
                if (digit > 9) {
                    digit -= 9;
                }
            }
            
            sum += digit;
            isEven = !isEven;
        }
        
        if (sum % 10 !== 0) {
            return { valid: false, error: 'Invalid card number (checksum failed)' };
        }
        
        return { valid: true, number: number };
    }

    static validateExpiry(month, year) {
        const m = parseInt(month);
        const y = parseInt(year);
        
        if (isNaN(m) || m < 1 || m > 12) {
            return { valid: false, error: 'Invalid month' };
        }
        
        if (isNaN(y)) {
            return { valid: false, error: 'Invalid year' };
        }
        
        // Convert 2-digit year to 4-digit
        const currentYear = new Date().getFullYear();
        let fullYear = y;
        if (y < 100) {
            fullYear = y < 50 ? 2000 + y : 1900 + y;
        }
        
        // Check if expired
        const expiryDate = new Date(fullYear, m, 0); // Last day of expiry month
        const now = new Date();
        
        if (expiryDate < now) {
            return { valid: false, error: 'Card has expired' };
        }
        
        return { valid: true, month: m, year: fullYear };
    }

    static validateCVV(cvv, cardType = 'visa') {
        if (!/^\d+$/.test(cvv)) {
            return { valid: false, error: 'CVV must contain only digits' };
        }
        
        // American Express uses 4-digit CVV
        if (cardType === 'amex' && cvv.length !== 4) {
            return { valid: false, error: 'American Express CVV must be 4 digits' };
        }
        
        // Other cards use 3-digit CVV
        if (cardType !== 'amex' && cvv.length !== 3) {
            return { valid: false, error: 'CVV must be 3 digits' };
        }
        
        return { valid: true, cvv: cvv };
    }

    static sanitizeInput(input) {
        if (typeof input !== 'string') return '';
        
        // Remove HTML tags
        input = input.replace(/<[^>]*>/g, '');
        
        // Remove SQL injection attempts
        const sqlPatterns = [
            /(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b)/gi,
            /(--|\;|\/\*|\*\/)/g,
            /(\bOR\b.*=.*|1=1)/gi
        ];
        
        sqlPatterns.forEach(pattern => {
            input = input.replace(pattern, '');
        });
        
        // Remove XSS attempts
        input = input.replace(/[<>\"']/g, '');
        
        return input.trim();
    }
}

// Rate Limiter (Client-side)
class ClientRateLimiter {
    constructor(maxRequests = 10, timeWindow = 60000) {
        this.maxRequests = maxRequests;
        this.timeWindow = timeWindow;
        this.requests = [];
    }

    canMakeRequest() {
        const now = Date.now();
        
        // Remove old requests outside time window
        this.requests = this.requests.filter(time => now - time < this.timeWindow);
        
        // Check if limit reached
        if (this.requests.length >= this.maxRequests) {
            return {
                allowed: false,
                retryAfter: Math.ceil((this.requests[0] + this.timeWindow - now) / 1000)
            };
        }
        
        this.requests.push(now);
        return { allowed: true };
    }

    reset() {
        this.requests = [];
    }
}

// Fingerprint Generator
class DeviceFingerprint {
    static async generate() {
        const components = {
            userAgent: navigator.userAgent,
            language: navigator.language,
            colorDepth: screen.colorDepth,
            deviceMemory: navigator.deviceMemory,
            hardwareConcurrency: navigator.hardwareConcurrency,
            screenResolution: `${screen.width}x${screen.height}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            platform: navigator.platform,
            plugins: Array.from(navigator.plugins).map(p => p.name).join(','),
            canvas: await this.getCanvasFingerprint(),
            webgl: await this.getWebGLFingerprint()
        };
        
        // Generate hash
        const str = JSON.stringify(components);
        return await this.hash(str);
    }

    static async getCanvasFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.fillStyle = '#f60';
            ctx.fillRect(0, 0, 100, 50);
            ctx.fillStyle = '#069';
            ctx.fillText('Fingerprint', 2, 2);
            return canvas.toDataURL();
        } catch (e) {
            return 'canvas_error';
        }
    }

    static async getWebGLFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl');
            const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
            return {
                vendor: gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL),
                renderer: gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL)
            };
        } catch (e) {
            return 'webgl_error';
        }
    }

    static async hash(str) {
        const encoder = new TextEncoder();
        const data = encoder.encode(str);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }
}

// Secure Storage
class SecureStorage {
    static encrypt(data, key) {
        try {
            const str = JSON.stringify(data);
            // Simple XOR encryption (for demo - use stronger encryption in production)
            let encrypted = '';
            for (let i = 0; i < str.length; i++) {
                encrypted += String.fromCharCode(str.charCodeAt(i) ^ key.charCodeAt(i % key.length));
            }
            return btoa(encrypted);
        } catch (e) {
            console.error('Encryption error:', e);
            return null;
        }
    }

    static decrypt(data, key) {
        try {
            const encrypted = atob(data);
            let decrypted = '';
            for (let i = 0; i < encrypted.length; i++) {
                decrypted += String.fromCharCode(encrypted.charCodeAt(i) ^ key.charCodeAt(i % key.length));
            }
            return JSON.parse(decrypted);
        } catch (e) {
            console.error('Decryption error:', e);
            return null;
        }
    }

    static setSecure(key, value, encryptionKey) {
        const encrypted = this.encrypt(value, encryptionKey);
        if (encrypted) {
            sessionStorage.setItem(key, encrypted);
            return true;
        }
        return false;
    }

    static getSecure(key, encryptionKey) {
        const encrypted = sessionStorage.getItem(key);
        if (encrypted) {
            return this.decrypt(encrypted, encryptionKey);
        }
        return null;
    }
}

// Activity Monitor
class ActivityMonitor {
    constructor() {
        this.events = [];
        this.suspicious = false;
        this.init();
    }

    init() {
        // Monitor mouse movements
        document.addEventListener('mousemove', (e) => {
            this.recordEvent('mousemove', { x: e.clientX, y: e.clientY });
        });

        // Monitor keyboard
        document.addEventListener('keydown', (e) => {
            this.recordEvent('keydown', { key: e.key });
        });

        // Monitor clicks
        document.addEventListener('click', (e) => {
            this.recordEvent('click', { x: e.clientX, y: e.clientY });
        });

        // Analyze periodically
        setInterval(() => this.analyze(), 5000);
    }

    recordEvent(type, data) {
        this.events.push({
            type: type,
            data: data,
            timestamp: Date.now()
        });

        // Keep only last 100 events
        if (this.events.length > 100) {
            this.events.shift();
        }
    }

    analyze() {
        // Check for bot-like behavior
        const recentEvents = this.events.filter(e => Date.now() - e.timestamp < 10000);
        
        // No mouse movement = suspicious
        const mouseEvents = recentEvents.filter(e => e.type === 'mousemove');
        if (mouseEvents.length === 0 && recentEvents.length > 5) {
            this.suspicious = true;
            console.warn('Suspicious activity: No mouse movement detected');
        }

        // Perfectly linear mouse movements = bot
        if (mouseEvents.length > 3) {
            const positions = mouseEvents.map(e => e.data);
            const isLinear = this.checkLinearity(positions);
            if (isLinear) {
                this.suspicious = true;
                console.warn('Suspicious activity: Bot-like mouse movement');
            }
        }
    }

    checkLinearity(positions) {
        if (positions.length < 3) return false;
        
        // Calculate if points are in a straight line
        const dx1 = positions[1].x - positions[0].x;
        const dy1 = positions[1].y - positions[0].y;
        
        for (let i = 2; i < positions.length; i++) {
            const dx2 = positions[i].x - positions[i-1].x;
            const dy2 = positions[i].y - positions[i-1].y;
            
            // Check if slopes are nearly identical
            if (Math.abs(dx1 * dy2 - dx2 * dy1) > 10) {
                return false;
            }
        }
        
        return true;
    }

    isSuspicious() {
        return this.suspicious;
    }

    reset() {
        this.events = [];
        this.suspicious = false;
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        BotDetector,
        InputValidator,
        ClientRateLimiter,
        DeviceFingerprint,
        SecureStorage,
        ActivityMonitor
    };
}
